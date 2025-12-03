<?php
session_start();
include './includes/db_connect.php';

// Only allow contractors
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
    header("Location: login.php");
    exit();
}

$contractor_id = $_SESSION['user_id'];

// Fetch bookings for this contractor
$sql = "SELECT b.*, u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone
        FROM bookings b
        JOIN users u ON b.customer_id = u.id
        WHERE b.contractor_id = ?
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $contractor_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Bookings - Contractor Dashboard</title>
    <link rel="stylesheet" href="./css/contractor_bookings.css">
</head>

<body>

    <div class="navbar">
        <div class="nav-left">
            <h2><?= htmlspecialchars($_SESSION['user_name']); ?> — Contractor Dashboard</h2>
        </div>
        <div class="nav-right">
            <a href="contractor_dashboard.php">Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2 class="page-title">📑 My Bookings</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="booking-list">

                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="booking-card">

                        <div class="booking-header">
                            <h3><?= htmlspecialchars($row['service_name']); ?></h3>
                            <span class="status-badge status-<?= strtolower($row['status']); ?>">
                                <?= ucfirst($row['status']); ?>
                            </span>
                        </div>

                        <div class="booking-info">
                            <p><strong>📅 Date & Time:</strong> <?= htmlspecialchars($row['booking_date']); ?></p>
                            <p><strong>👤 Customer:</strong> <?= htmlspecialchars($row['customer_name']); ?></p>
                            <p><strong>📧 Email:</strong> <?= htmlspecialchars($row['customer_email']); ?></p>
                            <p><strong>📞 Phone:</strong> <?= htmlspecialchars($row['customer_phone']); ?></p>
                            <p><strong>📝 Notes:</strong> <?= htmlspecialchars($row['description']); ?></p>
                        </div>

                        <div class="booking-actions">
                            <?php if ($row['status'] === 'pending'): ?>
                                <form method="POST" action="update_booking_status.php">
                                    <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                    <select name="new_status" class="status-select">
                                        <option value="confirmed">Confirm</option>
                                        <option value="rejected">Reject</option>
                                    </select>
                                    <button type="submit" class="action-btn">Update</button>
                                </form>
                            <?php else: ?>
                                <span class="no-action">No action required</span>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <p class="empty-message">No bookings yet 📭</p>
        <?php endif; ?>
    </div>

</body>

</html>