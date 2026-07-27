<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";

$products = Product::fetchProducts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST["status"] == "F") {
        Handling::insertHandling(
            $_POST["type"],
            $_POST["status"],
            $_POST["title"],
        );

        $handling_id = $conn->lastInsertId();

        $products = $_POST["products"];
        $quantities = $_POST["quantities"];

        for ($i = 0; $i < count($products); $i++) {
            $product_id = $products[$i];
            $qtd = $quantities[$i];

            Handling::insertHandleItem(
                $handling_id,
                $product_id,
                $qtd,
            );

            Product::updateQuantity( $product_id,
               $qtd, $_POST["type"]);
        }

        $_SESSION["success"] = "Movimentação realizada com sucesso!";

        header("Location: handles.php");
        exit;
    } else {
        Handling::insertHandling(
            $_POST["type"],
            $_POST["status"],
            $_POST["title"],
        );

        $handling_id = $conn->lastInsertId();

        $products = $_POST["products"];
        $quantities = $_POST["quantities"];

        for ($i = 0; $i < count($products); $i++) {
            $prdId = $products[$i];
            $qtd = $quantities[$i];

            Handling::insertHandleItem(
                $handling_id,
                $prdId,
                $qtd,
            );
        }

        $_SESSION["success"] = "Registro de movimentação criado com sucesso!";

        header("Location: handles.php");
        exit;
    }
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
            <a href="../index.php" type="button" class="btn btn-secondary">Voltar</a>
        </div>

        <form class="w-100" method="POST" action="handleForm.php">
            <h1>Movimentar produto</h1>

            <p>Preencha os campos abaixo:</p>

            <?php require __DIR__ . "/../resources/components/error.php" ?>
            <?php require __DIR__ . "/../resources/components/success.php" ?>

            <div class="mb-3 w-25">
                <label for="status" class="form-label">Status</label>

                <select name="status" id="status" class="form-select mb-3">
                    <option selected value="A">Aberto</option>
                    <option value="F">Fechado</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control" name="title" id="title">
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Tipo</label>

                <select name="type" id="type" class="form-select mb-3">
                    <option selected value="A">Ajuste</option>
                    <option value="E">Entrada</option>
                    <option value="S">Saída</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Produtos</label>

                <div id="products-container">
                    <div class="d-flex gap-2 mb-2 product-row">
                        <select name="products[]" class="form-select flex-grow-1">
                            <?php
                                foreach ($products as $product) {
                                    echo '<option value="' . $product["id"] . '">'
                                        . $product["description"] .
                                        '</option>';
                                }
                            ?>
                        </select>
                        <input type="number" name="quantities[]" class="form-control" style="max-width: 120px;" placeholder="Qtd" min="1">
                        <button type="button" class="btn btn-danger btn-remove" onclick="removeRow(this)">X</button>
                    </div>
                </div>

                <button type="button" class="btn btn-success btn-sm mt-1" onclick="addRow()">+ Adicionar produto</button>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>

            <script>
                const productsOptions = <?php
                                        $options = '';
                                        foreach ($products as $product) {
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