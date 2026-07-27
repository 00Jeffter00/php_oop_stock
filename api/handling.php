<?php
require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../app/controllers/handlingController.php";

header("Content-Type: application/json");

$page = isset($_GET["page"]) ? max(1, (int)$_GET["page"]) : 1;
$perPage = 10;

$totalHandling = Handling::countHandling();
$totalPages = max(1, ceil($totalHandling / $perPage));
$page = min($page, $totalPages);
$handling = Handling::fetchAllHandlingPaginated($page, $perPage);

echo json_encode([
    "handling" => $handling,
    "total" => $totalHandling,
    "page" => $page,
    "totalPages" => $totalPages,
]);
