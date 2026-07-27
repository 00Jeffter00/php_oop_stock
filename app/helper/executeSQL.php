<?php

require_once __DIR__ . "/../../config/database.php";

function executeSQL(string $sql, array $params = [], bool $return = true) {
    global $conn;    

    $stmt = $conn->prepare($sql);
    
    $stmt->execute($params);
    
    $result = empty($params) ? $stmt->fetchAll() : $stmt->fetch();

   if ($return) {
        return $result;
   }
}

