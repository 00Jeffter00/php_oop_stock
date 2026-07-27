<?php
require_once __DIR__ . "/../helper/executeSQL.php";

class Handling
{
    public static function fetchHandling(array $params)
    {
        $sql = "
            SELECT 
                *

            FROM product_handling
            WHERE id = :id
        ";

        return executeSQL($sql, $params);
    }

    public static function fetchAllHandling(object $conn)
    {
        $sql = "
            SELECT 
                *

            FROM product_handling
        ";

        return executeSQL($sql);
    }

    public static function insertHandling(array $params) {
        $sql = "
            INSERT INTO product_handling
            (type, status, title)
            VALUES
            (:type, :status, :title)
        ";

        executeSQL($sql, $params, false);
    }

    public static function insertHandleItem(array $params) {
        $sql = "
            INSERT INTO product_handling_item
            (h_id, prd_id, qtd)
            VALUES
            (:h_id, :prd_id, :qtd)
        ";

        executeSQL($sql, $params, false);
    }
}
