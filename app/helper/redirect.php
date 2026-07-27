<?php
function redirect(string $page) {

    Header("Location: /php/stock/$page"); 
    exit;
}