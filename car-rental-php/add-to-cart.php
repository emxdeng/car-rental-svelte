<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

if (empty($_SESSION["cart_items"])) {
  $_SESSION["cart_items"] = [];
}

$id = $_GET["id"];

array_push($_SESSION["cart_items"], ["id" => $id, "rental_days" => 1]);

?>