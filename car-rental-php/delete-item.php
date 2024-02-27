<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

if (empty($_SESSION["cart_items"])) {
  $_SESSION["cart_items"] = [];
}

$id = $_GET["id"];

// Find the index of the element with the matching ID
$index = null;
foreach ($_SESSION["cart_items"] as $key => $item) {
  if ($item["id"] == $id) {
    $index = $key;
    break;
  }
}

// Remove the element from the array if it exists
if ($index !== null) {
  array_splice($_SESSION["cart_items"], $index, 1);
}

echo json_encode($_SESSION["cart_items"]);

?>
