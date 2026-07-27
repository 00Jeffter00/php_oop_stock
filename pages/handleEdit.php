<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";

$allProducts = Product::fetchProducts();

function fetchProducts()
{
    global $conn;

    $sql = "
        SELECT
            p.id,
            p.description,
            phi.qtd
        FROM product_handling_item phi

        JOIN products p
            ON p.id = phi.prd_id

        WHERE phi.h_id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "id" => $_GET["id"]
    ]);

    $products = $stmt->fetchAll();
    return $products;
}

$products = $handling = $status = $type = null;

function refreshData() {
    global $products, $handling, $status, $type;

    $products = fetchProducts();
    $handling = Handling::fetchHandling(["id" => $_GET["id"]]);

    $status = $handling["status"];
    $type = $handling["type"];
}

refreshData();

// 1. Trás os produtos com base nas movimentações_item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($status == "F" && $_POST["status"] == "A") {
        die("Error 403 | Unauthorized Action");
    };

    // Ex.: [1, 12, 8, 34]
    $products_id = [];

    foreach ($products as $p) {
        foreach ($p as $key => $value) {
            if ($key == "id") {
                $products_id[] = $value;
            }
        }
    };

    for ($i = 0; $i < count($products_id); $i++) {
        if (in_array($products_id[$i], $_POST["products"])) {
            //echo "Esse produto deve permancer nas movimentações $products_id[$i] <br>";
        } else {
            $sql = "
                DELETE FROM product_handling_item
                WHERE prd_id = :prd_id AND h_id = :h_id
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                "prd_id" => $products_id[$i],
                "h_id" => $_GET["id"],
            ]);

            //echo "Produto $products_id[$i] removido!";
        };
    };

    for ($i = 0; $i < count($_POST["products"]); $i++) {
        if (in_array($_POST["products"][$i], $products_id)) {
            //echo "Esse produto " . $_POST['products'][$i] . " já estava nas movimentações <br>";
        } else {
            Handling::insertHandleItem([
                "h_id" => $_GET["id"],
                "prd_id" => $_POST['products'][$i],
                "qtd" => $_POST['quantities'][$i],
            ]);
        };
    };

    // Permanecer registro em aberto
    if ($status == "A") {

        for ($i = 0; $i < count($_POST['products']); $i++) {
            $sql = "
                    UPDATE product_handling_item
                    SET qtd = :qtd
                    WHERE prd_id = :prd_id AND h_id = :h_id
                ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                "prd_id" => $_POST['products'][$i],
                "h_id" => $_GET["id"],
                "qtd" => $_POST['quantities'][$i],
            ]);
        }
    }

    // Fechar e efetivar movimentação
    if ($status == "A" && $_POST["status"] == "F") {
        $sql = "
                    UPDATE product_handling
                    SET status = 'F'
                    WHERE id = :id
                ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "id" => $_GET["id"],
        ]);

        for ($i = 0; $i < count($_POST['products']); $i++) {
            Product::updateQuantity([
                "id" => $_POST['products'][$i],
                "qtd" => $_POST['quantities'][$i]
            ], $_POST["type"]);
        }
    }

    refreshData();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>

<body>

    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="d-flex w-100 mb-5">
            <a href="handles.php" type="button" class="btn btn-secondary">Voltar</a>
        </div>

        <form class="w-100" method="POST" action="handleEdit.php?id=<?= $_GET["id"] ?>">
            <h1><?= $status === "A" ? "Editar movimentação" : "Visualizar movimentação" ?></h1>

            <p <?= $status === "A" ? "" : "hidden" ?> >Preencha os campos abaixo:</p>

            <div class="mb-3 w-25">
                <label for="status" class="form-label">Status</label>

                <select <?= $status === "F" ? "disabled" : "" ?> name="status" id="status" class="form-select mb-3">
                    <option <?= $status == "A" ? "selected" : "" ?> value="A">Aberto</option>
                    <option <?= $status == "F" ? "selected" : "" ?> value="F">Fechado</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input <?= $status === "F" ? "disabled" : "" ?> type="text" class="form-control" value="<?= $handling["title"] ?>" name="title" id="title">
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipo</label>

                <select <?= $status === "F" ? "disabled" : "" ?> name="type" id="type" class="form-select mb-3">
                    <option <?= $type == "A" ? "selected" : "" ?> value="A">Ajuste</option>
                    <option <?= $type == "E" ? "selected" : "" ?> value="E">Entrada</option>
                    <option <?= $type == "S" ? "selected" : "" ?> value="S">Saída</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Produtos</label>

                <div id="products-container">
                    <?php
                    foreach ($products as $product) {
                        $disabled = $status === "A" ? "" : "disabled";

                        echo '
                                        <div class="d-flex gap-2 mb-2 product-row">
                                            <select '. $disabled .' name="products[]" class="form-select flex-grow-1">
                                                <option value="' . $product["id"] . '" selected>' . $product['description'] . '</option>
                                            </select>
                                            <input '. $disabled .' type="number" name="quantities[]" class="form-control" style="max-width: 120px;" value="' . $product["qtd"] . '" placeholder="Qtd" min="1">
                                            <button '. $disabled .' type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">X</button>
                                        </div>
                                        ';
                    }
                    ?>
                </div>

                <button <?= $status === "F" ? "hidden" : "" ?> type="button" class="btn btn-success btn-sm mt-1" onclick="addRow()">+ Adicionar produto</button>
            </div>

            <button <?= $status === "F" ? "hidden" : "" ?> type="submit" class="btn btn-primary">Salvar</button>

            <script>
                const productsOptions = <?php
                                        $options = '';
                                        foreach ($allProducts as $product) {
                                            $options .= '<option value="' . $product["id"] . '">' . $product["description"] . '</option>';
                                        }
                                        echo json_encode($options);
                                        ?>;

                function addRow() {
                    const container = document.getElementById('products-container');
                    const row = document.createElement('div');
                    row.className = 'd-flex gap-2 mb-2 product-row';
                    row.innerHTML = `
                        <select name="products[]" class="form-select flex-grow-1">${productsOptions}</select>
                        <input type="number" name="quantities[]" class="form-control" style="max-width: 120px;" placeholder="Qtd" min="1">
                        <button type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">X</button>
                    `;
                    container.appendChild(row);
                }

                function removeRow(btn) {
                    const container = document.getElementById('products-container');
                    if (container.children.length > 1) {
                        btn.closest('.product-row').remove();
                    }
                }
            </script>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>