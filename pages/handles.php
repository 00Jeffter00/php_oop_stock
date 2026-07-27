<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/redirect.php";

// Handle reopening/deleting validation
if (!empty($_GET["id"])) {

    // 1. Fetch all "handle_items"
    $result = Handling::fetchItemByHandle($_GET["id"]);

    // 2. If type = F (finished), reopen
    if ($result[0]["status"] === "F") {
        $products = [];

        // 2.1 Separe products id and quantity in an array
        for ($i = 0; $i < count($result); $i++) {
            $products[$result[$i]["prd_id"]] = $result[$i]["qtd"];
        }

        // 2.2 Separe the type of movimentation
        $movement = $result[0]["type"];

        // 2.3 For each product on $products array, update its quantity
        foreach ($products as $key => $value) {
            Product::updateQuantity($key, $value, $movement, true);
        };

        // 2.4 Update the handling status to "A" (open)
        Handling::updateHandligStatus($_GET["id"], "A");

        $_SESSION["success"] = "Movimentação reaberta com sucesso!";
        redirect("pages/handles.php");
    
    // 3. Else, delete
    } else {
        Handling::deleteHandle($_GET["id"]);

        $_SESSION["success"] = "Movimentação cancelada com sucesso!";
        redirect("pages/handles.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="../resources/css/main.css">
</head>

<body>

    <div class="container d-flex flex-column align-items-center justify-content-center vh-100">
        <div class="d-flex w-100 mb-5">
            <div class="w-50">
                <a href="product.php" type="button" class="btn btn-success">Cadastrar produto</a>
            </div>

            <div class="d-flex gap-2 w-50 justify-content-end">
                <a href="../index.php" type="button" class="btn btn-secondary">Ver Produtos</a>
                <a href="handleForm.php" type="button" class="btn btn-primary">Movimentar</a>
            </div>
        </div>

        <?php require __DIR__ . "/../resources/components/error.php" ?>
        <?php require __DIR__ . "/../resources/components/success.php" ?>

        <div class="table-container">
            <div class="info-bar" id="handling-info"></div>

            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Status</th>
                        <th scope="col">Título</th>
                        <th scope="col" class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody id="handling-tbody"></tbody>
            </table>

            <nav class="mt-3 d-flex justify-content-center" id="handling-pagination"></nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../resources/js/table-handles.js"></script>
</body>

</html>