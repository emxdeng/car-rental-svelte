<?php

$user = 'root';
$password = '';
$database = 'renting-history';

$db = new mysqli('localhost', $user, $password, $database) or die("Unable to connect to MySQL");

// $sql = "INSERT INTO renting_history (user_email, rent_date, bond_amount) VALUES ('emilyxdeng@gmail.com', CURDATE(), '200')";

// $db->query($sql);

?>