<?php

define("HOST", "localhost");
define("DB_NAME", "php_stock");
define("DB_USER", "root");
define("DB_PASS", "masterkey");

try {

    $conn = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

} catch(Exception $e){
    error_log($e->getMessage());
    die("500 Internal Server Error");
}