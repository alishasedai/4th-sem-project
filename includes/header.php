<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include './includes/db_connect.php';


// For notification of pending bookings (optional)
$pending_count = 0;
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'customer') {
    $customer_id = $_SESSION['user_id'];
    $sql = "SELECT COUNT(*) AS count FROM bookings WHERE customer_id=? AND status='pending'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $pending_count = $row['count'];
    }
}
?>

<link rel="stylesheet" href="./css/style.css">
<nav>
    <div class="logo">
        <a href="index.php"><img src="images/LOGO.jpg" alt="Logo" /></a>
    </div>

    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="services.php">Services</a>
        <a href="gallery.php">Gallery</a>
    </div>

    <div class="buttons bt">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['user_role'] === 'customer'): ?>
                <div class="customers">
                    <a href="customer_bookings.php" class="btn-notify">
                        My Bookings
                        <?php if ($pending_count > 0): ?>
                            <span class="badge"><?= $pending_count ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            <?php elseif ($_SESSION['user_role'] === 'contractor'): ?>
                <div class="contractors">
                    <a href="contractor_dashboard.php">Dashboard</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="default">
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="register-btn">Register</a>
            </div>
        <?php endif; ?>
    </div>
</nav>

<style>
    /* Example styles for the notification badge */
    .btn-notify {
        position: relative;
        padding: 8px 12px;
        background-color: #007BFF;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
    }

    .btn-notify .badge {
        position: absolute;
        top: -5px;
        right: -10px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 12px;
    }
</style>