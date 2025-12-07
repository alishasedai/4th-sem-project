<?php
session_start();
include './includes/db_connect.php';

// Only logged-in customers
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

// Fetch contractor info
$sql = "SELECT u.name, cd.services, cd.experience, cd.phone 
        FROM contractor_details cd 
        JOIN users u ON cd.user_id = u.id 
        WHERE cd.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contractor_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    die("Contractor not found!");
}

$contractor = $result->fetch_assoc();

// Handle booking submission
if (isset($_POST['book'])) {
    $customer_id = $_SESSION['user_id'];
    $service_name = $_POST['service'];
    $address = $_POST['address'] ?? '';
    $description = $_POST['notes'] ?? '';
    $date = $_POST['date'];
    $time = $_POST['time'];
    $booking_date = date('Y-m-d H:i:s', strtotime("$date $time"));

    // Insert booking safely
    $insert_sql = "INSERT INTO bookings 
                   (customer_id, contractor_id, service_name, address, description, booking_date, status, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("iissss", $customer_id, $contractor_id, $service_name, $address, $description, $booking_date);

    if ($stmt_insert->execute()) {
        $booking_id = $stmt_insert->insert_id;

        // Handle uploaded photos
        if (isset($_FILES['customer_photos']) && !empty($_FILES['customer_photos']['name'][0])) {
            $uploads_dir = './uploads/customer_photos/';
            if (!is_dir($uploads_dir)) mkdir($uploads_dir, 0777, true);

            foreach ($_FILES['customer_photos']['tmp_name'] as $key => $tmp_name) {
                $file_name = basename($_FILES['customer_photos']['name'][$key]);
                $target_file = $uploads_dir . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt_photo = $conn->prepare("INSERT INTO booking_photos (booking_id, photo_path) VALUES (?, ?)");
                    $stmt_photo->bind_param("is", $booking_id, $target_file);
                    $stmt_photo->execute();
                }
            }
        }

        echo "<script>alert('Booking confirmed!'); window.location='customer_bookings.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt_insert->error;
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