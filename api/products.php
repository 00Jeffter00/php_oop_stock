<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../app/controllers/productController.php";
require_once __DIR__ . "/../app/helper/executeSQL.php";

header("Content-Type: application/json");

$page = isset($_GET["page"]) ? max(1, (int)$_GET["page"]) : 1;
$perPage = 10;

$totalProducts = Product::countProducts();
$totalPages = max(1, ceil($totalProducts / $perPage));
$page = min($page, $totalPages);
$products = Product::fetchProductsPaginated($page, $perPage);

echo json_encode([
    "products" => $products,
    "total" => $totalProducts,
    "page" => $page,
    "totalPages" => $totalPages,
]);
