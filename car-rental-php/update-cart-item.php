<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

if (empty($_SESSION["cart_items"])) {
  $_SESSION["cart_items"] = [];
}

$id = $_GET["id"];
$rental_days = $_GET["days"];

// Find the element with the matching ID
foreach ($_SESSION["cart_items"] as &$item) {
    if ($item["id"] == $id) {
      // Update the rental_days value
      $item["rental_days"] = $rental_days;
      break;
    }
  }

echo json_encode($_SESSION["cart_items"]);

?>
