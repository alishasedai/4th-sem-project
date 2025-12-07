<?php
include './includes/db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery</title>
  <link rel="stylesheet" href="./css/style.css">
  <link rel="stylesheet" href="./css/gallery.css">
</head>

<body>
  <?php include('./includes/header.php'); ?>

  <main class="gallery-section">
    <h2>Our Latest Contractor Designs</h2>

    <div class="gallery-grid">
      <?php
      // Fetch all contractors who have uploaded work photos
      $sql = "SELECT user_id, work_photos, service_name FROM contractor_details WHERE work_photos IS NOT NULL AND work_photos != '' ORDER BY created_at DESC";
      $result = mysqli_query($conn, $sql);

      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $photos = explode(',', $row['work_photos']);
          foreach ($photos as $photo) {
            $photo = trim($photo);
            if (!empty($photo)) {
              echo '<div class="gallery-item">';
              echo "<img src='uploads/$photo' alt='" . htmlspecialchars($row['service_name']) . "'>";
              echo '</div>';
            }
          }
        }
      } else {
        echo "<p style='text-align:center; color:#777;'>No designs uploaded yet.</p>";
      }
      ?>
    </div>
  </main>

  <?php include('./includes/footer.php'); ?>
</body>

</html>