<?php
require_once __DIR__ . "/../helper/executeSQL.php";

class Product
{
    public static function fetchProducts()
    {
        $sql = "
            SELECT
             * 
            FROM products
        ";

        return executeSQL($sql);
    }

    public static function fetchProduct(int $id)
    {
        $sql = "
            SELECT
             * 
            FROM products
            WHERE id = :id
        ";

        $params = [
            "id" => $id,
        ];

        return executeSQL($sql, $params);
    }

    public static function fetchProductByName(string $description)
    {
        $sql = "
            SELECT
             * 
            FROM products
            WHERE description = :desc
        ";

        $params = [ 
            "desc" => $description
        ];

        return executeSQL($sql, $params);
    }

    public static function fetchProductHandle(int $handle_id)
    {
        $sql = "
            SELECT
            p.id,
            p.description,
            phi.qtd
            FROM product_handling_item phi

            JOIN products p
                ON p.id = phi.prd_id

            WHERE phi.h_id = :id
        ";

        $params = [
            "id" => $handle_id,
        ];

        return executeSQL($sql, $params, true, true);
    }

    public static function insertProduct(string $description)
    {
        $sql = "
            INSERT INTO products
            (description, qtd)
            VALUES (:desc, 0)
        ";

        $params = [
            "desc" => $description,
        ];

        executeSQL($sql, $params, false);
    }

    public static function updateQuantity(int $id, int $qtd, string $movement, bool $invert = false)
    {

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

        if ($invert) {
            switch ($movement) {
                case "E" || "A":
                    $movement = " qtd - :qtd ";
                    break;
                case "S":
                    $movement = " qtd + :qtd ";
                    break;
            };
        }

        $sql = "
            UPDATE products
            SET qtd = $movement
            WHERE id = :id
        ";

        $params = [
            "id" => $id,
            "qtd" => $qtd
        ];

        executeSQL($sql, $params, false);
    }

    public static function updateDescription(string $description, int $id)
    {

        $sql = "
            UPDATE products
            SET description = :description
            WHERE id = :id
        ";

        $params = [
            "description" => $description,
            "id" => $id
        ];

        executeSQL($sql, $params, false);
    }

    public static function countProducts()
    {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = executeSQL($sql);
        return (int) $result[0]["total"];
    }

    public static function fetchProductsPaginated(int $page, int $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT * 
            FROM products
            ORDER BY id ASC
            LIMIT :limit OFFSET :offset
        ";

        return executeSQL($sql, ["limit" => $perPage, "offset" => $offset], true, true);
    }

    public static function deleteProduct(int $id)
    {
        $sql = "
            DELETE FROM products
            WHERE id = :id
        ";

        $params = [
            "id" => $id
        ];

        executeSQL($sql, $params, false);
    }
}
