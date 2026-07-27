<?php
function redirect(string $page) {

    Header("Location: " . $page); 
    exit;
}