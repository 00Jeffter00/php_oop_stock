<?php
require_once __DIR__ . "/config/app.php";
require_once __DIR__ . "/app/controllers/productController.php";
require_once __DIR__ . "/app/helper/executeSQL.php";
require_once __DIR__ . "/app/helper/redirect.php";

// Product delete validation
if (!empty($_GET["id"])) {

    // 1. Query if the product have any handle
    $result = executeSQL("
        SELECT 
            prd_id
        FROM product_handling_item
        WHERE prd_id = :id
    ", ["id" => $_GET["id"]]);

    // 2. If have handle redirect
    if ($result) {
        $_SESSION["error"] = "Não foi possível excluir o produto, pois o mesmo possui movimentação!";
        
        redirect("index.php");
    }

    // 3. If not, delete
    Product::deleteProduct($_GET["id"]);
    $_SESSION["success"] = "Produto removido com sucesso!";

    redirect("index.php");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="./resources/css/main.css">
</head>

<body>

    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="d-flex w-100 mb-5">
            <div class="w-50">
                <a href="pages/product.php" type="button" class="btn btn-success">Cadastrar produto</a>
            </div>

            <div class="d-flex gap-2 w-50 justify-content-end">
                <a href="pages/handles.php" type="button" class="btn btn-secondary">Ver movimentações</a>
                <a href="pages/handleForm.php" type="button" class="btn btn-primary">Movimentar</a>
            </div>
        </div>

        <?php require __DIR__ . "/resources/components/error.php" ?>
        <?php require __DIR__ . "/resources/components/success.php" ?>

        <div class="table-container">
            <div class="info-bar" id="products-info"></div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Descrição</th>
                        <th scope="col">Quantidade</th>
                        <th scope="col" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="products-tbody"></tbody>
            </table>

            <nav class="mt-3 d-flex justify-content-center" id="products-pagination"></nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="./resources/js/table-index.js"></script>
</body>

</html>