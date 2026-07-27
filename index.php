<?php


require_once "./config/app.php";
require_once __DIR__ . "/app/controllers/productController.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $sql = "
        SELECT 
            prd_id
        FROM product_handling_item
        WHERE prd_id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "id" => $_GET["id"],
    ]);

    $result = $stmt->fetch();

    if ($result) {
        $_SESSION["error"] = "Não foi possível excluir o produto, pois o mesmo possui movimentação.";
    } else {
        $sql = "
            DELETE FROM products
            WHERE id = :id
        ";  

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "id" => $_GET["id"],
        ]);


        $_SESSION["success"] = "Produto removido com sucesso.";
    };
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
            <div class="w-50">
                <a href="pages/product.php" type="button" class="btn btn-success">Cadastrar produto</a>
            </div>

            <div class="d-flex gap-2 w-50 justify-content-end">
                <a href="pages/handles.php" type="button" class="btn btn-secondary">Ver movimentações</a>
                <a href="pages/handleForm.php" type="button" class="btn btn-primary">Movimentar</a>
            </div>
        </div>

        <?php
        if (isset($_SESSION["error"])) {
            echo '
                    <div class="alert alert-danger" role="alert">
                        ' . $_SESSION["error"] . '
                    </div>
                ';

            unset($_SESSION["error"]);
        }
        ?>

        <?php
        if (isset($_SESSION["success"])) {
            echo '
                    <div class="alert alert-success" role="alert">
                        ' . $_SESSION["success"] . '
                    </div>
                ';

            unset($_SESSION["success"]);
        }
        ?>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Descrição</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Ações</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = Product::fetchProducts();

                foreach ($products as $product) {
                    echo '<tr>
                                    <th scope="row">' . $product["id"] . '</th>
                                    <td>' . $product["description"] . '</td>
                                    <td>' . $product['qtd'] . '</td>
                                    <td><a class="btn btn-warning" href="./pages/productEdit.php?id=' . $product["id"] . '"> Editar </a></td>
                                    <td><a class="btn btn-danger" href="index.php?id=' . $product["id"] . '"> Deletar </a></td>
                                </tr>';
                };
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>