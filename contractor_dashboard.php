<?php
session_start();
include './includes/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'contractor') {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM contractor_details WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$contractor = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Contractor Dashboard</title>
  <link rel="stylesheet" href="./css/dashboard.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f6f8;
      margin: 0;
      padding: 0;
    }

    .navbar {
      background: #000;
      padding: 15px 60px;
      color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar a {
      color: white;
      text-decoration: none;
      margin-right: 20px;
      font-weight: 500;
    }

    .container {
      max-width: 900px;
      margin: 60px auto;
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .profile {
      text-align: center;
    }

    .profile img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
    }

    h2 {
      color: #333;
    }

    .details p {
      margin: 8px 0;
      color: #555;
    }

    .services-list {
      margin-top: 15px;
      padding: 15px;
      background: #f8f8f8;
      border-left: 4px solid #000;
      border-radius: 6px;
    }

    .services-list h3 {
      margin-bottom: 8px;
      color: #111;
    }

    .services-list ul {
      list-style-type: disc;
      margin-left: 20px;
      color: #444;
    }

    .no-data {
      text-align: center;
      color: #666;
      padding: 50px;
    }

    .btn {
      background: #000;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
    }
  </style>
</head>

<body>

  <div class="navbar">
    <div><strong>Welcome, <?= $_SESSION['user_name']; ?></strong></div>
    <div>
      <a href="contractor_add_service.php">Your Services</a>
      <a href="edit_service.php" class="btn">Edit Details</a>

      <a href="contractor_reviews.php">Reviews</a>
      <a href="contractor_bookings.php">My Bookings</a>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="container">
    <?php if ($contractor): ?>
      <div class="profile">
        <img src="uploads/<?= htmlspecialchars($contractor['profile_photo']); ?>" alt="Profile Photo">
        <h2><?= htmlspecialchars($contractor['service_name']); ?></h2>
      </div>

      <div class="details">
        <p><strong>Experience:</strong> <?= htmlspecialchars($contractor['experience']); ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($contractor['phone']); ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($contractor['address']); ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($contractor['description']); ?></p>
      </div>

      <div class="services-list">
        <h3>Services Offered:</h3>
        <ul>
          <?php
          $services = explode(',', $contractor['services']);
          foreach ($services as $service) {
            echo "<li>" . htmlspecialchars(trim($service)) . "</li>";
          }
          ?>
        </ul>
      </div>

      <div class="work-photo">
        <h3>Sample Work</h3>
        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;">
          <?php
          if (!empty($contractor['work_photos'])) {
            $photos = explode(',', $contractor['work_photos']);
            foreach ($photos as $photo) {
              if (!empty(trim($photo))) {
                echo '<img src="uploads/' . htmlspecialchars(trim($photo)) . '" alt="Work Photo" width="200" height="150" style="object-fit:cover; border-radius:8px; border:1px solid #ddd;">';
              }
            }
          } else {
            echo '<p style="color:#777;">No work photos available yet.</p>';
          }
          ?>
        </div>
      </div>


    <?php else: ?>
      <div class="no-data">
        <h3>You haven’t added your service details yet.</h3>
        <a href="contractor_add_service.php" class="btn">Add Your Service</a>
      </div>
    <?php endif; ?>
  </div>

</body>

</html>