<?php
session_start();
include './includes/db_connect.php';

// Ensure contractor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'contractor') {
    die("Unauthorized access!");
}

// Check booking ID
if (!isset($_GET['id'])) {
    die("No booking ID provided");
}

$booking_id = $_GET['id'];

// Fetch details
$sql = "SELECT b.*, u.name AS customer_name 
        FROM bookings b
        JOIN users u ON u.id = b.customer_id
        WHERE b.id='$booking_id'";

$res = mysqli_query($conn, $sql);
$booking = mysqli_fetch_assoc($res);

if (!$booking) {
    die("Booking not found!");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Confirm Booking</title>
</head>

<body>

    <h2>Booking Details</h2>

    <p><strong>Customer Name:</strong> <?= $booking['customer_name'] ?></p>
    <p><strong>Service:</strong> <?= $booking['service_name'] ?></p>
    <p><strong>Date:</strong> <?= $booking['booking_date'] ?></p>
    <p><strong>Address:</strong> <?= $booking['address'] ?></p>
    <p><strong>Description:</strong> <?= $booking['description'] ?></p>
    <p><strong>Status:</strong> <?= $booking['status'] ?></p>

    <form method="POST" action="update_status.php">
        <input type="hidden" name="booking_id" value="<?= $booking['id']; ?>">
        <select name="new_status" class="status-select">
            <option value="confirmed">Confirm</option>
            <option value="rejected">Reject</option>
        </select>
        <button type="submit" class="action-btn">Update</button>
    </form>

</body>

</html>