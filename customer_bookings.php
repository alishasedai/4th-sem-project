<?php
session_start();
include './includes/db_connect.php';
$_SESSION['user_id']; // should be 'customer' ID
$_SESSION['user_role']; // should be 'customer'


// Only allow logged-in customers
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['user_id'];

// Fetch all bookings for this customer along with contractor info
$sql = "SELECT b.*, u.name AS contractor_name, u.email AS contractor_email, u.phone AS contractor_phone
        FROM bookings b
        JOIN users u ON b.contractor_id = u.id
        WHERE b.customer_id = ?
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Bookings - Customer Dashboard</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: black;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ff9900;
        }

        .status-approved {
            background-color: green;
        }

        .status-rejected {
            background-color: red;
        }

        .no-bookings {
            text-align: center;
            font-size: 18px;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <h2>My Bookings</h2>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Contractor</th>
                <th>Service</th>
                <th>Date & Time</th>
                <th>Status</th>
                <th>Contractor Contact</th>
                <th>Notes</th>
                <th>Review</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php $status = strtolower($row['status']); ?>
                <tr>
                    <td><?= htmlspecialchars($row['contractor_name']); ?></td>
                    <td><?= htmlspecialchars($row['service_name']); ?></td>
                    <td><?= date("d M Y, H:i", strtotime($row['booking_date'])); ?></td>
                    <td>
                        <span class="status-badge status-<?= $status; ?>">
                            <?php
                            if ($status == 'pending') echo "⏳ Pending";
                            elseif ($status == 'approved' || $status == 'confirmed') echo "❤️ Approved";
                            elseif ($status == 'rejected') echo "❌ Rejected";
                            ?>
                        </span>
                    </td>
                    <td>
                        Email: <?= htmlspecialchars($row['contractor_email']); ?><br>
                        Phone: <?= htmlspecialchars($row['contractor_phone']); ?>
                    </td>
                    <td><?= htmlspecialchars($row['description']); ?></td>
                    <td>
                        <?php if ($status === 'confirmed'): ?>
                            <?php
                            // Check if already reviewed
                            $check_stmt = $conn->prepare("SELECT rating, comment FROM reviews WHERE booking_id=? AND customer_id=?");
                            $check_stmt->bind_param("ii", $row['id'], $_SESSION['user_id']);
                            $check_stmt->execute();
                            $check_result = $check_stmt->get_result();
                            $review = $check_result->fetch_assoc();
                            ?>
                            <?php if ($review): ?>
                                ⭐ <?= $review['rating'] ?> / 5
                                <br>
                                <?= htmlspecialchars($review['comment']); ?>
                            <?php else: ?>
                                <form method="POST" action="submit_review.php">
                                    <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                    <input type="hidden" name="contractor_id" value="<?= $row['contractor_id']; ?>">
                                    <select name="rating" required>
                                        <option value="">Rating</option>
                                        <option value="1">1 ⭐</option>
                                        <option value="2">2 ⭐</option>
                                        <option value="3">3 ⭐</option>
                                        <option value="4">4 ⭐</option>
                                        <option value="5">5 ⭐</option>
                                    </select>
                                    <input type="text" name="comment" placeholder="Optional comment">
                                    <button type="submit" name="submit_review">Submit</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p class="no-bookings">You have no bookings yet.</p>
    <?php endif; ?>

</body>

</html>