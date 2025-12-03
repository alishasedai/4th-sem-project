<?php
session_start();
include './includes/db_connect.php';

// Only super admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}

// Fetch stats
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE user_role='customer'"))['total'];
$total_contractors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE user_role='contractor'"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];

// Bookings per contractor
$bookings_per_contractor = mysqli_query($conn, "
    SELECT c.name, COUNT(b.id) as total_bookings 
    FROM users c 
    LEFT JOIN bookings b ON c.id = b.contractor_id 
    WHERE c.role='contractor'
    GROUP BY c.id
");

// All bookings
$all_bookings = mysqli_query($conn, "
    SELECT b.*, u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone,
           c.name AS contractor_name
    FROM bookings b
    JOIN users u ON b.customer_id = u.id
    JOIN users c ON b.contractor_id = c.id
    ORDER BY b.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="./css/dashboard.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: #333;
            color: #fff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
        }

        .navbar a {
            color: #fff;
            margin-left: 15px;
            text-decoration: none;
        }

        .container {
            padding: 20px;
        }

        .cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            flex: 1;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #888;
        }

        .card p {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f0f0f0;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .status-pending {
            color: orange;
            font-weight: bold;
        }

        .status-confirmed {
            color: green;
            font-weight: bold;
        }

        .status-rejected {
            color: red;
            font-weight: bold;
        }

        .status-completed {
            color: blue;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div><strong>Super Admin: <?= $_SESSION['user_name']; ?></strong></div>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Dashboard</h1>

        <!-- Stats Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Customers</h3>
                <p><?= $total_customers; ?></p>
            </div>
            <div class="card">
                <h3>Total Contractors</h3>
                <p><?= $total_contractors; ?></p>
            </div>
            <div class="card">
                <h3>Total Bookings</h3>
                <p><?= $total_bookings; ?></p>
            </div>
        </div>

        <!-- Bookings per Contractor -->
        <h2>Contractor Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Contractor</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($bookings_per_contractor)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= $row['total_bookings']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- All Bookings -->
        <h2>All Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contractor</th>
                    <th>Service</th>
                    <th>Date & Time</th>
                    <th>Notes</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($all_bookings)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['customer_name']); ?><br><?= htmlspecialchars($row['customer_email']); ?></td>
                        <td><?= htmlspecialchars($row['contractor_name']); ?></td>
                        <td><?= htmlspecialchars($row['service_name']); ?></td>
                        <td><?= htmlspecialchars($row['booking_date']); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td class="status-<?= strtolower($row['status']); ?>"><?= ucfirst($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</body>

</html>