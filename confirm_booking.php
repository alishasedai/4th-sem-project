<?php
session_start();
include './includes/db_connect.php';

if (!isset($_POST['submit'])) {
    die("Invalid access");
}

$customer_id = $_SESSION['user_id'];
$contractor_id = $_POST['contractor_id'];
$service_name = $_POST['service_name'];
$booking_date = $_POST['booking_date'];
$address = $_POST['address'];
$description = $_POST['description'];

$sql = "INSERT INTO bookings 
(customer_id, contractor_id, service_name, booking_date, address, description, status)
VALUES 
('$customer_id', '$contractor_id', '$service_name', '$booking_date', '$address', '$description', 'pending')";

$result = mysqli_query($conn, $sql);

if ($result) {
    echo "<script>alert('Booking Successful!'); window.location='customer_dashboard.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
