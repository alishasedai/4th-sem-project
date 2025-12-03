<?php
session_start();
include './includes/db_connect.php';

// If ID not found
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("⚠ Invalid contractor profile!");
}

$contractor_id = $_GET['id'];

// Fetch contractor details
$sql = "SELECT cd.*, u.name AS contractor_name 
        FROM contractor_details cd
        JOIN users u ON cd.user_id = u.id
        WHERE cd.user_id = '$contractor_id'
        LIMIT 1";

$result = mysqli_query($conn, $sql);
$contractor = mysqli_fetch_assoc($result);

if (!$contractor) {
    die("⚠ Contractor not found!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($contractor['contractor_name']); ?> - Contractor Profile</title>

    <link rel="stylesheet" href="./css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: #f3f3f3;
        }

        .profile-container {
            max-width: 1100px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .header-section img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ddd;
        }

        .header-section h2 {
            font-size: 28px;
            margin-top: 12px;
        }

        .header-section p {
            color: #444;
            font-size: 16px;
            margin-top: -5px;
        }

        .section-box {
            background: #fafafa;
            padding: 18px 22px;
            margin-top: 25px;
            border-radius: 10px;
            border-left: 4px solid #000;
        }

        .section-box h3 {
            margin-top: 0;
        }

        .section-box p {
            margin: 6px 0;
        }

        .services-list ul {
            list-style: none;
            padding-left: 0;
        }

        .services-list li {
            background: #eaeaea;
            display: inline-block;
            padding: 8px 14px;
            margin: 5px;
            border-radius: 6px;
            font-size: 14px;
        }

        .gallery img {
            width: 210px;
            height: 170px;
            object-fit: cover;
            border-radius: 8px;
            margin: 10px;
            border: 1px solid #ccc;
        }

        .review-box {
            margin-top: 30px;
            background: #fff;
            padding: 18px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .btn-contact {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
        }

        .btn-contact:hover {
            background: #333;
        }
    </style>
</head>

<body>

    <!-- <?php include('./includes/header.php'); ?> -->

    <div class="profile-container">

        <!-- HEADER -->
        <div class="header-section">
            <img src="uploads/<?= htmlspecialchars($contractor['profile_photo']); ?>" alt="Profile">
            <h2><?= htmlspecialchars($contractor['contractor_name']); ?></h2>
            <p><?= htmlspecialchars($contractor['service_name']); ?></p>
        </div>

        <!-- BASIC DETAILS -->
        <div class="section-box">
            <h3>Contractor Information</h3>
            <p><strong>Experience:</strong> <?= htmlspecialchars($contractor['experience']); ?> years</p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($contractor['phone']); ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($contractor['address']); ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($contractor['description']); ?></p>
        </div>

        <!-- SERVICES -->
        <div class="section-box services-list">
            <h3>Services Offered</h3>
            <ul>
                <?php
                $services = explode(',', $contractor['services']);
                foreach ($services as $service) {
                    echo "<li>" . htmlspecialchars(trim($service)) . "</li>";
                }
                ?>
            </ul>
        </div>

        <!-- WORK GALLERY -->
        <div class="section-box">
            <h3>Work Portfolio</h3>
            <div class="gallery">
                <?php
                if (!empty($contractor['work_photos'])) {
                    $photos = explode(',', $contractor['work_photos']);
                    foreach ($photos as $photo) {
                        if (trim($photo) !== "") {
                            echo '<img src="uploads/' . htmlspecialchars(trim($photo)) . '">';
                        }
                    }
                } else {
                    echo "<p>No work photos uploaded.</p>";
                }
                ?>
            </div>
        </div>

        <!-- REVIEWS -->
        <div class="review-box">
            <h3>Customer Reviews</h3>
            <p>⭐ Reviews feature coming soon...</p>
        </div>

        <a class="btn-contact" href="contact_contractor.php?id=<?= $contractor_id; ?>">Contact Contractor</a>

    </div>

    <?php include('./includes/footer.php'); ?>

</body>

</html>