<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

$_SESSION["cart_items"] = [];

echo json_encode(true);

?>