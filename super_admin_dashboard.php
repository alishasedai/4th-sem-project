<?php
session_start();
include './includes/db_connect.php';

// Only super admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}

// Fetch stats
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$total_contractors = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='contractor'"))['total'];
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

$reviews = mysqli_query($conn, "
    SELECT r.*, 
           c.name AS customer_name, 
           ctr.name AS contractor_name 
    FROM reviews r
    JOIN users c ON r.customer_id = c.id
    JOIN users ctr ON r.contractor_id = ctr.id
    ORDER BY r.created_at DESC
");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>

    <style>
        /* ------------------- BASIC ------------------- */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #eef1f7;
            color: #333;
        }

        /* ------------------- NAVBAR ------------------- */
        .navbar {
            background: #1f2937;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .navbar a {
            color: #fff;
            padding: 8px 18px;
            background: #ef4444;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
        }

        .navbar a:hover {
            background: #dc2626;
        }

        /* ------------------- CONTAINER ------------------- */
        .container {
            padding: 30px;
        }

        h1 {
            font-size: 28px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 25px;
        }

        /* ------------------- CARDS ------------------- */
        .cards {
            display: flex;
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            flex: 1;
            background: #fff;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            font-size: 18px;
            color: #6b7280;
            margin: 0 0 8px;
        }

        .card p {
            font-size: 30px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        /* ------------------- TABLE DESIGN ------------------- */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
        }

        th {
            background: #111827;
            color: #fff;
            padding: 14px;
            font-size: 15px;
            text-align: left;
        }

        td {
            padding: 12px 14px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        tr:hover {
            background: #f9fafb;
        }

        /* ------------------- STATUS COLORS ------------------- */
        .status-pending {
            color: #d97706;
            font-weight: 600;
        }

        .status-confirmed {
            color: #16a34a;
            font-weight: 600;
        }

        .status-rejected {
            color: #dc2626;
            font-weight: 600;
        }

        .status-completed {
            color: #2563eb;
            font-weight: 600;
        }

        h2.section-title {
            font-size: 22px;
            color: #1f2937;
            margin: 25px 0 10px;
        }
    </style>

</head>

<body>

    <div class="navbar">
        <h2>Super Admin Dashboard</h2>
        <div>
            <strong><?= $_SESSION['user_name']; ?></strong>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h1>Overview</h1>

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

        <!-- Contractor Performance -->
        <h2 class="section-title">Contractor Performance</h2>
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
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['total_bookings']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- All Bookings -->
        <h2 class="section-title">All Bookings</h2>
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
                        <td><?= $row['customer_name']; ?><br><small><?= $row['customer_email']; ?></small></td>
                        <td><?= $row['contractor_name']; ?></td>
                        <td><?= $row['service_name']; ?></td>
                        <td><?= $row['booking_date']; ?></td>
                        <td><?= $row['description']; ?></td>
                        <td class="status-<?= strtolower($row['status']); ?>"><?= ucfirst($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Reviews Section -->
        <h2 class="section-title">Customer Reviews</h2>

        <table>
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contractor</th>
                    <th>Rating</th>
                    <th>Review</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
                    <tr>
                        <td><?= $r['customer_name']; ?></td>
                        <td><?= $r['contractor_name']; ?></td>
                        <td><?= str_repeat("⭐", $r['rating']); ?></td>
                        <td><?= $r['review_text']; ?></td>
                        <td><?= $r['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>


    </div>

</body>

</html>