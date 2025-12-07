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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


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
      color: #000;
      font-size: 15px;
      margin-top: 5px;
    }

    .search {
      display: flex;
      flex-direction: column;
      gap: 20px;
      justify-content: center;
      align-items: center;
      text-align: center;
      font-size: 21px;

    }

    .search .s {
      width: 680px;
      height: 60px;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 20px;
      background-color: #000;
      /* background-color: #1aaeaa; */
      border: 1px solid black;
      border-radius: 10px;
    }

    .s div button {
      background-color: #000;

    }

    .s div button i {
      font-size: 27px;
      color: #fff;
    }

    .search input {
      width: 600px;
      height: 47px;
      border-radius: 10px;
      background-color: #fff;
      margin-left: -30px;
      border: none;
      outline: none;

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

    .names {
      display: flex;
      gap: 30px;
      justify-content: center;
      margin-bottom: 20px;
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
      gap: 20px;
      margin-bottom: 15px;
      flex-wrap: wrap;
    }

    .project-images img {
      width: 150px;
      height: 120px;
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

    .card-buttons button a {
      color: white;
      text-decoration: none;
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
  <section class="search">

    <form action="" method="GET">
      <label for="">Search your desired services</label>
      <br><br>

      <div class="s">
        <input type="text" name="search_service" value="<?php if (isset($_GET['search_service'])) {
                                                          echo $_GET['search_service'];
                                                        } ?>" placeholder="search services">
        <div class="search_icon">
          <button><i class="fa-solid fa-magnifying-glass"></i></button>

        </div>
      </div>
    </form>
  </section>
  <section class="contractor-grid">
    <?php
    $sql = "SELECT cd.*, u.name AS contractor_name 
        FROM contractor_details cd
        JOIN users u ON cd.user_id = u.id";

    if (isset($_GET['search_service']) && !empty($_GET['search_service'])) {
      $filter = mysqli_real_escape_string($conn, $_GET['search_service']);
      $sql .= " WHERE CONCAT(cd.services,' ',cd.description,' ',cd.service_name) LIKE '%$filter%'";
    }

    $sql .= " ORDER BY cd.created_at DESC";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $show_buttons = true;
        include './includes/contractor_card.php';
      }
    } else {
      echo '<p style="text-align:center; color:#777;">No contractors found.</p>';
    }
    ?>
  </section>


  <?php include('./includes/footer.php'); ?>
</body>

</html>