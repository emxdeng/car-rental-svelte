<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

if (empty($_SESSION["cart_items"])) {
  $_SESSION["cart_items"] = [];
}

echo json_encode($_SESSION["cart_items"]);

?>