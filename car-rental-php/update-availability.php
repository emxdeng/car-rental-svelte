<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');

session_start();

// Read the request parameters
$cart = $_SESSION['cart_items'];
$availability = false;

var_dump($cart);

// Read the contents of the cars.json file
$carsData = file_get_contents('../car-rental/static/cars.json');

// Parse the JSON data
$cars = json_decode($carsData, true);

var_dump($cars);

// Update the availability for each car in the cart
foreach ($cart as $item) {
    $carId = $item['id'];
    $rentalDays = $item['rental_days'];

    foreach ($cars['cars'] as &$car) {
        if ($car['id'] == $carId) {
            $car['availability'] = false;
            $car['rental_days'] = $rentalDays;
            break;
        }
    }
}

var_dump($cars);

// Save the modified JSON data back to the file
if (file_put_contents('../car-rental/static/cars.json', json_encode($cars, JSON_PRETTY_PRINT))) {
    // Success response
    echo json_encode(['success' => true]);
} else {
    // Failure response
    echo json_encode(['success' => false, 'error' => 'Failed to update availability']);
}
?>
