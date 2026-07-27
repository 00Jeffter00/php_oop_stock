<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";

$allProducts = Product::fetchProducts();

$products = $handling = $status = $type = [];

function refreshData()
{
    global $products, $handling, $status, $type;

    $products = Product::fetchProductHandle($_GET["id"]);
    $handling = Handling::fetchHandling($_GET["id"]);

    $status = $handling["status"];
    $type = $handling["type"];
}

refreshData();

// 1. Trás os produtos com base nas movimentações_item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($status == "F" && $_POST["status"] == "A") {
        die("Error 403 | Unauthorized Action");
    };

    $products_id = [];

    foreach ($products as $p) {
        foreach ($p as $key => $value) {
            if ($key == "id") {
                $products_id[] = $value;
            }
        }
    };

    for ($i = 0; $i < count($products_id); $i++) {
        if (!in_array($products_id[$i], $_POST["products"])) {
            Handling::deleteHandleItem($products_id[$i], $_GET["id"]);
        }
    };

    for ($i = 0; $i < count($_POST["products"]); $i++) {
        if (!in_array($_POST["products"][$i], $products_id)) {
            Handling::insertHandleItem(
                $_GET["id"],
                $_POST['products'][$i],
                $_POST['quantities'][$i],
            );
        }
    };

    // Permanecer registro em aberto
    if ($status == "A") {
        for ($i = 0; $i < count($_POST['products']); $i++) {
            Handling::updateItemQuantity(
                $_POST["quantities"][$i],
                $_POST["products"][$i],
                $_GET["id"]
            );

            $_SESSION["success"] = "Registro alterado com sucesso!";
        }
    }

    // Fechar e efetivar movimentação
    if ($status == "A" && $_POST["status"] == "F") {
        Handling::updateHandligStatus($_GET["id"], "F");

        for ($i = 0; $i < count($_POST['products']); $i++) {
            Product::updateQuantity(
                $_POST['products'][$i],
                $_POST['quantities'][$i],
                $_POST["type"]
            );
        }
        $_SESSION["success"] = "Movimentação concluída com sucesso!";
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

            <p <?= $status === "A" ? "" : "hidden" ?>>Preencha os campos abaixo:</p>

            <?php require __DIR__ . "/../resources/components/error.php" ?>
            <?php require __DIR__ . "/../resources/components/success.php" ?>

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
                                            <select ' . $disabled . ' name="products[]" class="form-select flex-grow-1">
                                                <option value="' . $product["id"] . '" selected>' . $product['description'] . '</option>
                                            </select>
                                            <input ' . $disabled . ' type="number" name="quantities[]" class="form-control" style="max-width: 120px;" value="' . $product["qtd"] . '" placeholder="Qtd" min="1">
                                            <button ' . $disabled . ' type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">X</button>
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