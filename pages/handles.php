<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";

if ($_SERVER["REQUEST_METHOD"] === "GET" && !empty($_GET["id"])) {
    $sql = "
        SELECT
            *  
        FROM product_handling ph
            LEFT JOIN product_handling_item phi ON (phi.h_id = ph.id)

        WHERE ph.id = :id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        "id" => $_GET["id"],
    ]);

    $result = $stmt->fetchAll();

    if ($result[0]["status"] === "F") {
        $products = [];

        for ($i = 0; $i < count($result); $i++) {
            $products[$result[$i]["prd_id"]] = $result[$i]["qtd"];
        }

        $movement = " qtd + :qtd ";

        switch ($result[0]["type"]) {
            case "S":
                break;
            case "E" || "A":
                $movement = " qtd - :qtd ";
                break;
        }

        $sql = "
        UPDATE products
        SET qtd = $movement
        WHERE id = :id 
    ";

        foreach ($products as $key => $value) {
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                "qtd" => $value,
                "id" => $key,
            ]);
        }

        $sql = "
        UPDATE product_handling
        SET status = 'A'
        WHERE id = :id
    ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "id" => $_GET["id"],
        ]);
    } else {
        $sql = "
            DELETE FROM product_handling_item WHERE h_id = :id
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "id" => $_GET["id"],
        ]);

        $sql = "
            DELETE FROM product_handling WHERE id = :id
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "id" => $_GET["id"],
        ]);
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
            <div class="w-50">
                <a href="product.php" type="button" class="btn btn-success">Cadastrar produto</a>
            </div>

            <div class="d-flex gap-2 w-50 justify-content-end">
                <a href="../index.php" type="button" class="btn btn-secondary">Ver Produtos</a>
                <a href="handleForm.php" type="button" class="btn btn-primary">Movimentar</a>
            </div>
        </div>

        <?php

        ?>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Status</th>
                    <th scope="col">Título</th>
                    <th scope="col">Ação</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $handling = Handling::fetchAllHandling($conn);

                foreach ($handling as $item) {
                    $deleteText = $item["status"] === "A" ? 'Cancelar' : 'Reabrir';
                    $editText = $item["status"] === "A" ? 'Editar' : 'Visualizar';

                    echo '<tr>
                                <th scope="row">' . $item["id"] . '</th>
                                <td>' . $item["type"] . '</td>
                                <td>' . $item["status"] . '</td>
                                <td>' . $item["title"] . '</td>
                                <td><a class="btn btn-warning" href="handleEdit.php?id=' . $item["id"] . '">' . $editText . '</a></td>
                                <td><a class="btn btn-danger" href="handles.php?id=' . $item["id"] . '"> ' . $deleteText . '</a></td>
                        </tr>';
                };
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>