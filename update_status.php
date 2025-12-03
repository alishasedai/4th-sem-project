<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    header("Location: login.php");
    exit();
}

$booking_id = $_POST['booking_id'];
$status = $_POST['status'];

$sql = "UPDATE bookings SET status = '$status' WHERE id = $booking_id";

if (mysqli_query($conn, $sql)) {
    header("Location: contractor_bookings.php?updated=1");
    exit();
} else {
    echo "Error updating: " . mysqli_error($conn);
}
