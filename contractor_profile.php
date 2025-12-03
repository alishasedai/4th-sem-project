<?php
include('./includes/db_connect.php');
session_start();

if (!isset($_GET['id'])) {
    die("Contractor not found.");
}

$contractor_id = intval($_GET['id']);

// Fetch contractor details
$query = "SELECT * FROM contractor_details WHERE user_id = $contractor_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Contractor not found!");
}

$contractor = mysqli_fetch_assoc($result);

// Fetch reviews
$reviewQuery = "
    SELECT r.*, u.name AS customer_name 
    FROM reviews r 
    JOIN users u ON r.customer_id = u.id 
    WHERE r.contractor_id = $contractor_id
    ORDER BY r.created_at DESC
";
$reviewResult = mysqli_query($conn, $reviewQuery);
$can_review = false;

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer') {
    $customer_id = $_SESSION['user_id'];
    $checkBooking = "SELECT id FROM bookings 
                     WHERE customer_id = $customer_id 
                       AND contractor_id = $contractor_id 
                       AND status='completed' 
                     LIMIT 1";
    $bookingResult = mysqli_query($conn, $checkBooking);

    if (mysqli_num_rows($bookingResult) > 0) {
        $can_review = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= $contractor['service_name'] ?> - Contractor Profile</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f7fa;
            margin: 0;
        }

        .profile-header {
            background: white;
            padding: 30px;
            display: flex;
            gap: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .profile-header img {
            width: 160px;
            height: 160px;
            border-radius: 10px;
            object-fit: cover;
        }

        .profile-info h2 {
            margin: 0;
            font-size: 28px;
            color: #333;
        }

        .profile-info p {
            color: #555;
        }

        .section {
            margin: 25px;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section h3 {
            margin-bottom: 15px;
            color: #222;
        }

        .portfolio img {
            width: 160px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 10px;
        }

        .review-card {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .review-card h4 {
            margin: 0;
        }

        .btn {
            background: #007bff;
            padding: 10px 18px;
            color: white;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 15px;
        }

        .btn:hover {
            background: #0056b3;
        }

        .no-reviews {
            padding: 15px;
            background: #eee;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="profile-header">
        <img src="<?= !empty($contractor['profile_photo']) ? 'uploads/' . $contractor['profile_photo'] : 'https://via.placeholder.com/150' ?>">

        <div class="profile-info">
            <h2><?= htmlspecialchars($contractor['service_name']) ?></h2>
            <p><?= htmlspecialchars($contractor['description']) ?></p>
            <p><strong>Experience:</strong> <?= $contractor['experience'] ?> years</p>
            <p><strong>Phone:</strong> <?= $contractor['phone'] ?></p>
            <p><strong>Address:</strong> <?= $contractor['address'] ?></p>
        </div>
    </div>

    <!-- Portfolio Section -->
    <div class="section">
        <h3>Portfolio / Work Photos</h3>
        <div class="portfolio">
            <?php
            if (!empty($contractor['work_photos'])) {
                $photos = explode(',', $contractor['work_photos']);
                foreach ($photos as $photo) {
                    echo "<img src='uploads/$photo'>";
                }
            } else {
                echo "<p>No portfolio added.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="section">
        <h3>Customer Reviews</h3>

        <?php if (mysqli_num_rows($reviewResult) > 0) { ?>

            <?php while ($review = mysqli_fetch_assoc($reviewResult)) { ?>
                <div class="review-card">
                    <h4><?= $review['customer_name'] ?> ⭐ <?= $review['rating'] ?>/5</h4>
                    <p><?= htmlspecialchars($review['review_text']) ?></p>
                    <small><?= $review['created_at'] ?></small>
                </div>
            <?php } ?>

        <?php } else { ?>
            <p class="no-reviews">No reviews yet.</p>
        <?php } ?>

        <!-- Add Review Button (Only customers logged in) -->
        <!-- Add Review Button (Only if allowed) -->
        <!-- Add Review Button (Only if allowed) -->
        <?php if ($can_review) { ?>
            <br>
            <button class="btn" onclick="window.location.href='write_review.php?contractor_id=<?= $contractor_id ?>'">
                Write a Review
            </button>
        <?php } ?>
    </div>

</body>

</html>