<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

if (!isset($_GET['contractor_id'])) {
    header("Location: index.php");
    exit();
}
$contractor_id = intval($_GET['contractor_id']);


$sql = "SELECT u.name, cd.services, cd.experience, cd.phone 
        FROM contractor_details cd 
        JOIN users u ON cd.user_id = u.id 
        WHERE cd.user_id = $contractor_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Contractor not found!";
    exit();
}



$contractor = mysqli_fetch_assoc($result);

if (isset($_POST['book'])) {
    $customer_id = $_SESSION['user_id'];
    $service_name = mysqli_real_escape_string($conn, $_POST['service']);
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $description = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');

    // Combine date and time into booking_date
    $date = $_POST['date'];
    $time = $_POST['time'];
    $booking_date = date('Y-m-d H:i:s', strtotime("$date $time"));

    $insert_sql = "INSERT INTO bookings (customer_id, contractor_id, service_name, address, description, booking_date, status, created_at) 
                   VALUES ('$customer_id', '$contractor_id', '$service_name', '$address', '$description', '$booking_date', 'pending', NOW())";

    if (mysqli_query($conn, $insert_sql)) {
        $booking_id = mysqli_insert_id($conn);

        // Handle uploaded photos if any
        if (isset($_FILES['customer_photos']) && !empty($_FILES['customer_photos']['name'][0])) {
            $uploads_dir = './uploads/customer_photos/';
            if (!is_dir($uploads_dir))
                mkdir($uploads_dir, 0777, true);

            foreach ($_FILES['customer_photos']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['customer_photos']['name'][$key]);
                $target_file = $uploads_dir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $file_path = mysqli_real_escape_string($conn, $target_file);
                    mysqli_query($conn, "INSERT INTO booking_photos (booking_id, photo_path) VALUES ('$booking_id', '$file_path')");
                }
            }
        }

        echo "<script>alert('Booking confirmed!'); window.location='index.php';</script>";
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Service - <?= htmlspecialchars($contractor['name']); ?></title>
    <link rel="stylesheet" href="./css/booking.css">
</head>

<body>

    <div class="page-container">

        <div class="contractor-card">
            <h2><?= htmlspecialchars($contractor['name']); ?></h2>
            <p><strong>Experience:</strong> <?= htmlspecialchars($contractor['experience']); ?> years</p>
            <p><strong>Services:</strong> <?= htmlspecialchars($contractor['services']); ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($contractor['phone']); ?></p>
        </div>

        <form class="booking-form" method="POST" enctype="multipart/form-data">

            <h3>Book This Service</h3>

            <div class="input-group">
                <label>Service You Want</label>
                <input type="text" name="service" placeholder="Enter desired service" required>
            </div>

            <div class="input-group">
                <label>Address (optional)</label>
                <input type="text" name="address" placeholder="Enter job location">
            </div>

            <div class="two-grid">
                <div class="input-group">
                    <label>Date</label>
                    <input type="date" name="date" required>
                </div>

                <div class="input-group">
                    <label>Time</label>
                    <input type="time" name="time" required>
                </div>
            </div>

            <div class="input-group">
                <label>Notes</label>
                <textarea name="notes" placeholder="Any additional details..."></textarea>
            </div>

            <div class="input-group">
                <label>Upload Reference Photos (optional)</label>
                <input type="file" name="customer_photos[]" multiple accept="image/*">
            </div>

            <button type="submit" name="book" class="btn-submit">Confirm Booking</button>

        </form>

    </div>

</body>

</html>