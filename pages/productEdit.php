<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "
        UPDATE products
        SET description = :description
        WHERE id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "description" => $_POST["description"],
        "id" => $_GET["id"],
    ]);
} 

if(!empty($_GET["id"])) {
    $sql = "
        SELECT description
        FROM products
        WHERE id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "id" => $_GET["id"],
    ]);

    $product = $stmt->fetch();

    $description = $product["description"];
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

        <?php
            if(isset($_SESSION["success"])) {
                echo '<div class="alert alert-success" role="alert">'
                        . $_SESSION['success'] . 
                    '</div>';

                unset($_SESSION['success']);
            }
        ?>

        <form class="w-100" method="POST" action="productEdit.php?id=<?= $_GET["id"] ?>">
            <h1>Alterar produto</h1>

            <p>Preencha o campo abaixo:</p>

            <div class="mb-3">
                <label for="description" class="form-label">Descrição</label>
                <input value="<?= $description ?>" type="text" class="form-control" name="description" id="description">
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>