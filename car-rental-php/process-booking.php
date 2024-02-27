<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

require_once 'db-connect.php';

$body = file_get_contents('php://input');
$data = json_decode($body, true);
$email = $data['email'];

$response = array(
    'email' => $email
);

// Escape the strings to prevent SQL injection
$email = mysqli_real_escape_string($db, $data['email']);

// Check if the email exists within the past 3 months
$threeMonthsAgo = date('Y-m-d', strtotime('-3 months'));
$checkEmailQuery = "SELECT COUNT(*) AS emailCount FROM renting_history WHERE user_email = '$email' AND rent_date >= '$threeMonthsAgo'";
$checkEmailResult = $db->query($checkEmailQuery);

if ($checkEmailResult && $checkEmailResult->num_rows > 0) {
    $emailCount = $checkEmailResult->fetch_assoc()['emailCount'];
    if ($emailCount > 0) {
        $bond = 0;
    } else {
        $bond = 200;
    }
}

// Insert the data into the renting_history table
$insertQuery = "INSERT INTO renting_history (user_email, rent_date, bond_amount) VALUES ('$email', CURDATE(), '$bond')";
if ($db->query($insertQuery) === TRUE) {
    $bookingProcessed = true;
} else {
    $bookingProcessed = false;
}

mysqli_close($db);

echo json_encode($bookingProcessed);


?>