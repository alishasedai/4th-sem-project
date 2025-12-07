<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    die("Unauthorized");
}

if (isset($_POST['booking_id'], $_POST['new_status'])) {
    $booking_id = intval($_POST['booking_id']);
    $new_status = $_POST['new_status'];

    if (!in_array($new_status, ['confirmed', 'rejected'])) {
        die("Invalid status");
    }

    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=? AND contractor_id=?");
    $stmt->bind_param("sii", $new_status, $booking_id, $_SESSION['user_id']);
    if ($stmt->execute()) {
        header("Location: contractor_bookings.php?updated=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
