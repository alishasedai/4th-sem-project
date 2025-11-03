<?php
session_start();
include './includes/db_connect.php';

// ✅ Allow only logged-in customers
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// ✅ Restrict access only to customers (not contractors)
if ($_SESSION['user_role'] !== 'customer') {
    header("Location: contractor_dashboard.php");
    exit();
}

// ✅ Fetch all contractors and their services
$sql = "SELECT u.name, u.email, u.phone, u.address, cd.services, cd.experience, cd.design_photo, cd.profile_photo 
        FROM contractor_details cd
        JOIN users u ON cd.user_id = u.id
        ORDER BY cd.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Gypsum Services</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .logout {
            position: absolute;
            right: 30px;
            top: 20px;
            background: black;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }

        .contractor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }

        .contractor-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .contractor-card img {
            width: 100%;
            height: 180px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        .contractor-card h3 {
            margin-bottom: 5px;
            font-size: 18px;
            color: #222;
        }

        .contractor-card p {
            color: #555;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .book-btn {
            display: inline-block;
            padding: 8px 15px;
            background: black;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .book-btn:hover {
            background: #333;
        }
    </style>
</head>

<body>

    <a href="logout.php" class="logout">Logout</a>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
    <p style="text-align:center; color:gray;">Explore verified contractors and their services</p>

    <div class="contractor-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="contractor-card">
                    <img src="./uploads/<?= htmlspecialchars($row['design_photo']); ?>" alt="Contractor Work">
                    <h3><?= htmlspecialchars($row['name']); ?></h3>
                    <p><b>Phone:</b> <?= htmlspecialchars($row['phone']); ?></p>
                    <p><b>Address:</b> <?= htmlspecialchars($row['address']); ?></p>
                    <p><b>Experience:</b> <?= htmlspecialchars($row['experience']); ?> years</p>
                    <p><b>Services:</b> <?= htmlspecialchars($row['services']); ?></p>
                    <a href="#" class="book-btn">Book Now</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center;">No contractors available yet.</p>
        <?php endif; ?>
    </div>

</body>

</html>