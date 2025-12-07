<?php
include './includes/db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Our Contractors</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/about.css">
</head>

<body>
  <?php include('./includes/header.php'); ?>

  <!-- mainSection start -->
  <section class="contractors-section">
    <h2>Find Expert Contractors</h2>
    <p>Connect with verified ceiling specialists in your area</p>

    <div class="contractor-grid">
      <?php
      // Fetch all contractors
      $sql = "SELECT cd.*, u.name AS contractor_name
              FROM contractor_details cd
              JOIN users u ON cd.user_id = u.id
              ORDER BY cd.created_at DESC";
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0):
          while ($row = mysqli_fetch_assoc($result)):

              $contractor_id = $row['user_id'];

              // Fetch review info
              $reviewQuery = "SELECT COUNT(*) AS total_reviews, AVG(rating) AS avg_rating
                              FROM reviews
                              WHERE contractor_id = $contractor_id";
              $reviewResult = mysqli_query($conn, $reviewQuery);
              $reviewData = mysqli_fetch_assoc($reviewResult);
              $total_reviews = $reviewData['total_reviews'] ?? 0;
              $avg_rating = $reviewData['avg_rating'] ? round($reviewData['avg_rating'], 1) : 0;

              // Work photos
              $work_photos = !empty($row['work_photos']) ? explode(',', $row['work_photos']) : [];
      ?>

      <!-- Contractor Card -->
      <div class="contractor-card">
        <img src="uploads/<?= htmlspecialchars($row['profile_photo']) ?>" alt="Contractor" class="profile-img">
        <h3><?= htmlspecialchars($row['contractor_name']) ?></h3>

        <!-- Dynamic Rating -->
        <div class="rating">
          <?php
          for ($i = 1; $i <= 5; $i++) {
              if ($i <= floor($avg_rating)) {
                  echo "⭐";
              } elseif ($i - $avg_rating < 1) {
                  echo "✰"; // half star
              } else {
                  echo "☆"; // empty star
              }
          }
          ?>
          <span><?= $avg_rating ?></span>
          <small><?= $total_reviews ?> review<?= $total_reviews != 1 ? 's' : '' ?></small>
        </div>

        <p><?= htmlspecialchars($row['description']) ?></p>

        <div class="work-images">
          <?php
          if (!empty($work_photos)) {
              foreach ($work_photos as $photo) {
                  $photo = trim($photo);
                  if ($photo != '') {
                      echo '<img src="uploads/' . htmlspecialchars($photo) . '" alt="Work Image">';
                  }
              }
          } else {
              echo '<p style="color:#888; font-size:13px;">No project photos uploaded yet.</p>';
          }
          ?>
        </div>

        <div class="buttons">
          <button class="view-profile">
            <a href="contractor_profile.php?id=<?= $row['user_id'] ?>" style="color:#fff; text-decoration:none;">View Profile</a>
          </button>
          <button class="contact">
            <a href="booking.php?contractor_id=<?= $row['user_id'] ?>" style="color:#333; text-decoration:none;">Contact</a>
          </button>
        </div>
      </div>

      <?php
          endwhile;
      else:
          echo "<p style='text-align:center; color:#777;'>No contractors available yet.</p>";
      endif;
      ?>
    </div>
  </section>

  <?php include('./includes/footer.php'); ?>
</body>

</html>
