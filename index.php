<?php include('./includes/db_connect.php'); ?>
<?php
$query = "SELECT * FROM contractor_details ORDER BY id DESC LIMIT 8"; // show 8 latest contractors
$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gypsum Ceiling Services</title>
  <link rel="stylesheet" href="./css/style.css">
  <style>
    .customer-card button a {
      color: white;
      text-decoration: none;
    }
  </style>

</head>

<body>
  <?php include('./includes/header.php'); ?>

  <section class="hero">
    <h1>Transform your spaces with exquisite ceiling designs</h1>
    <p>Connect with verified contractors for professional gypsum and false ceiling services. Quality craftsmanship, transparent pricing, and reliable service guaranteed.</p>
    <div class="hero-buttons">
      <button><a href="services.php">Browse Services </a></button>
      <button>Find Contractors</button>
    </div>
  </section>

  <section class="choose">
    <h2>Why Choose Our Platform?</h2>
    <p>We make finding and booking ceiling services simple, transparent, and reliable</p>
    <div class="choose-boxes">
      <div class="choose-item">
        <h4>Verified Contractors</h4>
        <p>All contractors are thoroughly vetted and verified for quality and reliability</p>
      </div>
      <div class="choose-item">
        <h4>Easy Booking</h4>
        <p>Book services online with transparent pricing and flexible scheduling</p>
      </div>
      <div class="choose-item">
        <h4>24/7 Support</h4>
        <p>Get help anytime with our dedicated customer support team</p>
      </div>
    </div>
  </section>

  <section class="services">
    <h2>Our Services</h2>
    <p>Professional ceiling solutions for every space and budget</p>

    <div class="service-grid">
      <!-- 6 identical cards -->
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
      <div class="card"><img src="https://via.placeholder.com/300x230" alt="">
        <div class="card-content">
          <h4>Gypsum False Ceiling</h4>
          <p>Modern gypsum board ceiling installations with perfect finishing</p><button>Book Now</button>
        </div>
      </div>
    </div>
  </section>

  <section class="customers">
    <h2>Top Rated Customers</h2>
    <p>Work with experienced professionals who deliver exceptional results</p>

    <div class="customer-grid">
      <section class="customers">
        <h2>Top Rated Contractors</h2>
        <p>Work with experienced professionals who deliver exceptional results</p>

        <div class="customer-grid">
          <?php
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              $name = htmlspecialchars($row['service_name']);
              $photo = !empty($row['profile_photo']) ? "uploads/" . htmlspecialchars($row['profile_photo']) : "https://via.placeholder.com/80";
              $experience = htmlspecialchars($row['experience']);
              $description = htmlspecialchars($row['description']);
              $id = htmlspecialchars($row['user_id']); // to link to their profile page
          ?>

              <div class="customer-card">
                <img src="<?= $photo ?>" alt="<?= $name ?>">
                <h4><?= $name ?></h4>
                <p><?= $description ?></p>
                <div class="star">⭐ 4.9 (234 reviews)</div>
                <p><strong>Experience:</strong> <?= $experience ?> years</p>
                <button onclick="window.location.href='contractor_profile.php?id=<?= $id ?>'"> <a href="contractor_profile.php">View Profile</a></button>
              </div>

          <?php
            }
          } else {
            echo "<p>No contractors found yet.</p>";
          }
          ?>
        </div>
      </section>
    </div>
  </section>
  <?php include('./includes/footer.php'); ?>

</body>

</html>
<!--
Hi ChatGPT, yesterday we were working on my BCA project for a Gypsum Services portal. I was creating a booking system with customer and contractor dashboards.
Here are the files I have so far (uploaded previously):

booking.php

customer_dashboard.php

contractor_dashboard.php

contractor_bookings.php

I want to:

Add a booking_time column to my bookings table in MySQL.

Update booking.php form so customers can select date and time.

Update the contractor_bookings.php to show date and time correctly without errors.

Show the booking status (pending, confirmed, rejected, completed) in the contractor dashboard.

Please help me modify the code and SQL queries to implement this. My database table structure is as I uploaded previously (booking table has id, customer_id, contractor_id, service, date, notes, status, photos).
        -->