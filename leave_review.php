<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$customer_id = $_SESSION['user_id'];

// Get contractor info
$sql = "SELECT b.*, u.name AS contractor_name FROM bookings b
        JOIN users u ON b.contractor_id = u.id
        WHERE b.id = ? AND b.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $customer_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Invalid booking.");
}

if (isset($_POST['submit'])) {
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $insert = $conn->prepare("
        INSERT INTO reviews (booking_id, customer_id, contractor_id, rating, review_text)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("iiiis", $booking_id, $customer_id, $booking['contractor_id'], $rating, $review);

    if ($insert->execute()) {
        echo "<script>alert('Review submitted!'); window.location='customer_dashboard.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error submitting review!');</script>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Leave Review</title>
</head>

<body>
    <h2>Review Contractor: <?= $booking['contractor_name']; ?></h2>

    <form method="POST">
        <label>Rating (1–5)</label>
        <select name="rating" required>
            <option value="">Select</option>
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
        </select>

        <br><br>

        <label>Review</label><br>
        <textarea name="review" rows="5" cols="40" required></textarea>

        <br><br>

        <button type="submit" name="submit">Submit Review</button>
    </form>
</body>

</html>