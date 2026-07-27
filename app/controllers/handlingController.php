<?php
require_once __DIR__ . "/../helper/executeSQL.php";

class Handling
{
    public static function fetchHandling(int $id)
    {
        $sql = "
            SELECT 
                *

            FROM product_handling
            WHERE id = :id
        ";

        $params = [ 
            "id" => $id,
        ];

        return executeSQL($sql, $params);
    }

    public static function fetchAllHandling()
    {
        $sql = "
            SELECT 
                *

            FROM product_handling
        ";

        return executeSQL($sql);
    }

    public static function fetchItemByHandle(int $id)
    {
        $sql = "
            SELECT
                *  
            FROM product_handling ph
                LEFT JOIN product_handling_item phi ON (phi.h_id = ph.id)

            WHERE ph.id = :id
        ";

        $params = [
            "id" => $id,
        ];

        return executeSQL($sql, $params, true, true);
    }

    public static function insertHandling(string $type, string $status, string $title)
    {
        $sql = "
            INSERT INTO product_handling
            (type, status, title)
            VALUES
            (:type, :status, :title)
        ";

        $params = [
            "type" => $type,
            "status" => $status,
            "title" => $title
        ];

        executeSQL($sql, $params, false);
    }

    public static function insertHandleItem(int $h_id, int $prd_id, float $qtd)
    {
        $sql = "
            INSERT INTO product_handling_item
            (h_id, prd_id, qtd)
            VALUES
            (:h_id, :prd_id, :qtd)
        ";

        $params = [
            "h_id" => $h_id,
            "prd_id" => $prd_id,
            "qtd" => $qtd,
        ];

        executeSQL($sql, $params, false);
    }

    public static function updateHandligStatus(int $id, string $status)
    {
        $sql = "
            UPDATE product_handling
            SET status = :status
            WHERE id = :id
        ";

        $params = [
            "id" => $id,
            "status" => $status,
        ];

        executeSQL($sql, $params, false);
    }

    public static function updateItemQuantity(float $qtd, int $prd_id, int $handle_id)
    {
        $sql = "
            UPDATE product_handling_item
            SET qtd = :qtd
            WHERE prd_id = :prd_id AND h_id = :h_id
        ";

        $params = [
            "qtd" => $qtd,
            "prd_id" => $prd_id,
            "h_id" => $handle_id,
        ];

        executeSQL($sql, $params, false);
    }

    public static function deleteHandle(int $id)
    {
        $sql = "
            DELETE FROM product_handling
            WHERE id = :id
        ";

        $params = [
            "id" => $id,
        ];

        executeSQL($sql, $params, false);

        $sql = "
            DELETE FROM product_handling_item
            WHERE h_id = :id
        ";

        $params = [
            "id" => $id,
        ];

        executeSQL($sql, $params, false);
    }

    public static function deleteHandleItem(int $prd_id, int $handle_id)
    {
        $sql = "
            DELETE FROM product_handling_item
            WHERE prd_id = :prd_id AND h_id = :h_id
        ";

        $params = [
            "prd_id" => $prd_id,
            "h_id" => $handle_id,
        ];

        executeSQL($sql, $params, false);
    }

    public static function deleteItemByHandle(int $id)
    {
        $sql = "
            DELETE FROM product_handling_item
            WHERE h_id = :id
        ";

        $params = [
            "id" => $id,
        ];

        executeSQL($sql, $params, false);
    }

    public static function countHandling()
    {
        $sql = "SELECT COUNT(*) as total FROM product_handling";
        $result = executeSQL($sql);
        return (int) $result[0]["total"];
    }

    public static function fetchAllHandlingPaginated(int $page, int $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $sql = "
            SELECT * 
            FROM product_handling
            ORDER BY id ASC
            LIMIT :limit OFFSET :offset
        ";

        return executeSQL($sql, ["limit" => $perPage, "offset" => $offset], true, true);
    }
}
