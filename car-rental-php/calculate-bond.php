<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

require_once 'db-connect.php';

$sql = "SELECT user_email FROM renting_history";

$emailToCheck = $_GET['email'];

// Get the date 3 months ago
$threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));

$sql = "SELECT user_email, rent_date FROM renting_history";

$result = $db->query($sql);

if ($result) {
    $emailFound = false;

    while ($row = $result->fetch_assoc()) {
        if ($row["user_email"] === $emailToCheck && $row["rent_date"] >= $threeMonthsAgo) {
            $emailFound = true;
            break;
        }
    }

    if ($emailFound) {
        $bond = 0;
        echo json_encode($bond);
    } else {
        $bond = 200;
        echo json_encode($bond);
    }

    $result->free();
} else {
    echo "Error retrieving data: " . $db->error;
}



$db->close();

?>