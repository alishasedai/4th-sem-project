<?php
include './includes/db_connect.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>about page</title>
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
      <!-- Contractor Card -->
      <div class="contractor-card">
        <img src="images/profile1.jpg" alt="Contractor" class="profile-img">
        <h3>Luxury Ceiling Designs</h3>
        <div class="rating">
          ⭐ <span>4.9</span> <small>156 reviews</small>
        </div>
        <p>Premium wooden ceiling specialist with 10+ years creating bespoke ceiling solutions for luxury homes and high-end commercial spaces.</p>
        <div class="work-images">
          <img src="images/work1.jpg" alt="Work 1">
          <img src="images/work2.jpg" alt="Work 2">
        </div>t3F
        <div class="buttons">
          <button class="view-profile">View Profile</button>
          <button class="contact">Contact</button>
        </div>
      </div>

      <!-- Repeat 3 more cards -->
      <div class="contractor-card">
        <img src="images/profile1.jpg" alt="Contractor" class="profile-img">
        <h3>Luxury Ceiling Designs</h3>
        <div class="rating">
          ⭐ <span>4.9</span> <small>156 reviews</small>
        </div>
        <p>Premium wooden ceiling specialist with 10+ years creating bespoke ceiling solutions for luxury homes and high-end commercial spaces.</p>
        <div class="work-images">
          <img src="images/work1.jpg" alt="Work 1">
          <img src="images/work2.jpg" alt="Work 2">
        </div>
        <div class="buttons">
          <button class="view-profile">View Profile</button>
          <button class="contact">Contact</button>
        </div>
      </div>

      <div class="contractor-card">
        <img src="images/profile1.jpg" alt="Contractor" class="profile-img">
        <h3>Luxury Ceiling Designs</h3>
        <div class="rating">
          ⭐ <span>4.9</span> <small>156 reviews</small>
        </div>
        <p>Premium wooden ceiling specialist with 10+ years creating bespoke ceiling solutions for luxury homes and high-end commercial spaces.</p>
        <div class="work-images">
          <img src="images/work1.jpg" alt="Work 1">
          <img src="images/work2.jpg" alt="Work 2">
        </div>
        <div class="buttons">
          <button class="view-profile">View Profile</button>
          <button class="contact">Contact</button>
        </div>
      </div>

      <div class="contractor-card">
        <img src="images/profile1.jpg" alt="Contractor" class="profile-img">
        <h3>Luxury Ceiling Designs</h3>
        <div class="rating">
          ⭐ <span>4.9</span> <small>156 reviews</small>
        </div>
        <p>Premium wooden ceiling specialist with 10+ years creating bespoke ceiling solutions for luxury homes and high-end commercial spaces.</p>
        <div class="work-images">
          <img src="images/work1.jpg" alt="Work 1">
          <img src="images/work2.jpg" alt="Work 2">
        </div>
        <div class="buttons">
          <button class="view-profile">View Profile</button>
          <button class="contact">Contact</button>
        </div>
      </div>
    </div>
  </section>
  <?php include('./includes/footer.php'); ?>
</body>

</html>