<?php
require_once __DIR__ . "/../helper/executeSQL.php";

class Product {
    public static function fetchProducts() {  
        $sql = "
            SELECT
             * 
            FROM products
        ";
    
        return executeSQL($sql);
    }

    public static function updateQuantity(array $params, string $movement) {
        
        switch ($movement) {
            case "E":
                $movement = " qtd + :qtd ";
                break;
            case "S":
                $movement = " qtd - :qtd ";
                break;
            default:
                $movement = " :qtd ";
                break;
        };

        $sql = "
            UPDATE products
            SET qtd = $movement
            WHERE id = :id
        ";

        executeSQL($sql, $params, false);
    }
}