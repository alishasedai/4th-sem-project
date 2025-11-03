<?php
include './includes/db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Browse Our Services - Gypsum Portals</title>
  <link rel="stylesheet" href="./css/style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #fff;
      color: #222;
    }

    .hero {
      text-align: center;
      padding: 50px 20px 10px;
    }

    .hero h1 {
      font-size: 26px;
      font-weight: 600;
    }

    .hero p {
      color: #777;
      font-size: 15px;
      margin-top: 5px;
    }

    .contractor-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
      justify-content: center;
      padding: 40px 60px;
    }

    .contractor-card {
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      transition: 0.3s;
    }

    .contractor-card:hover {
      transform: translateY(-5px);
    }

    .contractor-card img.profile {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
      border: 2px solid #eee;
    }

    .contractor-card h3 {
      font-size: 17px;
      font-weight: 600;
      margin: 5px 0;
    }

    .rating {
      color: #ffb400;
      font-size: 15px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 4px;
      margin-bottom: 5px;
    }

    .rating span {
      color: #666;
      font-size: 13px;
    }

    .contractor-card p {
      color: #555;
      font-size: 14px;
      margin-bottom: 15px;
      line-height: 1.4;
    }

    .project-images {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }

    .project-images img {
      width: 120px;
      height: 90px;
      border-radius: 6px;
      object-fit: cover;
      border: 1px solid #ddd;
    }

    .card-buttons {
      display: flex;
      justify-content: space-between;
    }

    .card-buttons button {
      border: none;
      border-radius: 6px;
      padding: 8px 14px;
      font-size: 14px;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .card-buttons .view {
      background: #000;
      color: #fff;
    }

    .card-buttons .contact {
      background: #f5f5f5;
      color: #333;
    }

    .card-buttons .view:hover {
      background: #333;
    }

    .card-buttons .contact:hover {
      background: #e8e8e8;
    }
  </style>
</head>

<body>
  <?php include('./includes/header.php'); ?>

  <section class="hero">
    <h1>Find Expert Contractors</h1>
    <p>Connect with verified ceiling specialists in your area</p>
  </section>

  <section class="contractor-grid">
    <?php
    $sql = "SELECT cd.*, u.name AS contractor_name 
            FROM contractor_details cd
            JOIN users u ON cd.user_id = u.id
            ORDER BY cd.created_at DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0):
      while ($row = mysqli_fetch_assoc($result)):
    ?>
        <div class="contractor-card">
          <!-- Contractor Profile Photo -->
          <img src="uploads/<?= htmlspecialchars($row['profile_photo']); ?>" class="profile" alt="Contractor">

          <!-- Contractor Name -->
          <h3><?= htmlspecialchars($row['contractor_name']); ?></h3>

          <div class="rating">⭐ 4.9 <span>156 reviews</span></div>

          <!-- Contractor Description -->
          <p><?= htmlspecialchars($row['description']); ?></p>

          <!-- Contractor Work Photos -->
          <div class="project-images">
            <?php
            if (!empty($row['work_photos'])) {
              $photos = explode(',', $row['work_photos']);
              foreach ($photos as $photo) {
                if (!empty(trim($photo))) {
                  echo '<img src="uploads/' . htmlspecialchars(trim($photo)) . '" alt="Project Image">';
                }
              }
            } else {
              echo '<p style="color:#888; font-size:13px;">No project photos uploaded yet.</p>';
            }
            ?>
          </div>

          <!-- Buttons -->
          <div class="card-buttons">
            <button class="view">View Profile</button>
            <button class="contact">Contact</button>
          </div>
        </div>
      <?php endwhile;
    else: ?>
      <p style="text-align:center; color:#777;">No contractors available yet.</p>
    <?php endif; ?>
  </section>

  <?php include('./includes/footer.php'); ?>
</body>

</html>