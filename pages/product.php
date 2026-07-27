<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";
require_once __DIR__ . "/../app/helper/redirect.php";

// Product creation validation
if($_SERVER['REQUEST_METHOD'] === "POST") {
    // 1. Checks if the descripton are filled
    if($_POST["description"] === "") {
        $_SESSION["error"] = "Preencha uma descrição!";
        redirect("product.php");
    }

    // 2. Query if the product already exists on the database
    $alreadyExists = Product::fetchProductByName($_POST["description"]);
    
    // 3. If exists, don't create
    if($alreadyExists) {
        $_SESSION["error"] = "Já existe um produto com esse nome!";
        redirect("product.php");
    }    

    // 4. Else, create product
    Product::insertProduct($_POST["description"]);

    $_SESSION["success"] = "Produto cadastrado com sucesso!";
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

        <?php require __DIR__ . "/../resources/components/error.php" ?>
        <?php require __DIR__ . "/../resources/components/success.php" ?>

        <form class="w-100" method="POST" action="product.php">
            <h1>Cadastrar produto</h1>

            <p>Preencha o campo abaixo:</p>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <input type="text" class="form-control" name="description" id="description">
            </div>

            <button type="submit" class="btn btn-primary">Cadastrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>