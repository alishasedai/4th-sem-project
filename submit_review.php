<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    die("Unauthorized");
}

if (isset($_POST['submit_review'], $_POST['booking_id'], $_POST['contractor_id'], $_POST['rating'])) {
    $customer_id = $_SESSION['user_id'];
    $booking_id = intval($_POST['booking_id']);
    $contractor_id = intval($_POST['contractor_id']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

    // Check rating validity
    if ($rating < 1 || $rating > 5) die("Invalid rating");

    // Insert review
    $stmt = $conn->prepare("INSERT INTO reviews (booking_id, contractor_id, customer_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $booking_id, $contractor_id, $customer_id, $rating, $comment);
    if ($stmt->execute()) {
        header("Location: customer_bookings.php?review_submitted=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
