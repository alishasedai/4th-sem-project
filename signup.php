<?php
include "./includes/db_connect.php";
session_start();

if (isset($_POST['submit'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $role = $_POST['role'];
  $phone = $_POST['number'];
  $address = $_POST['address'];
  $password = $_POST['password'];

  if (empty($name) || empty($email) || empty($role) || empty($phone) || empty($address) || empty($password)) {
    echo "<script>alert('All fields are required!');</script>";
  } else {
    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
      echo "<script>alert('Email already registered! Please login instead.');</script>";
    } else {
      // Hash the password
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Insert user data
      $sql = "INSERT INTO users (name, email, role, phone, address, password)
              VALUES ('$name', '$email', '$role', '$phone', '$address', '$hashed_password')";

      if (mysqli_query($conn, $sql)) {
        // ✅ Automatically log in the user
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;

        // Redirect based on role
        if ($role === 'contractor') {
          header("Location: contractor_dashboard.php");
        } else {
          header("Location: customer_dashboard.php");
        }
        exit();
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - Gypsum Services</title>
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <div class="container">
    <button class="back-btn"><a href="index.php">← Back To Home</a></button>

    <!-- Add the onsubmit function for JS validation -->
    <form class="form-box" onsubmit="validateSignupForm(event)" method="post">
      <h2>Create Account</h2>
      <p class="subtitle">Join Gypsum Services as a customer or contractor</p>

      <label>Full Name</label>
      <div class="input-box">
        <input type="text" id="signupName" placeholder="Enter your full name" name="name">
      </div>

      <label>Email</label>
      <div class="input-box">
        <input type="email" id="signupEmail" placeholder="Enter your email" name="email">
      </div>

      <label>I am a</label>
      <div class="input-box">
        <select id="signupRole" name="role">
          <option value="">-- Select Role --</option>
          <option value="customer">Customer</option>
          <option value="contractor">Contractor</option>
        </select>
      </div>

      <label>Phone Number</label>
      <div class="input-box">
        <input type="tel" id="signupPhone" placeholder="Enter your phone number" name="number">
      </div>

      <label>Address</label>
      <div class="input-box">
        <textarea id="signupAddress" placeholder="Enter your address" name="address"></textarea>
      </div>

      <label>Password</label>
      <div class="input-box">
        <input type="password" id="signupPassword" placeholder="Create a password" name="password">
      </div>

      <label>Confirm Password</label>
      <div class="input-box">
        <input type="password" id="signupConfirm" placeholder="Confirm your password">
      </div>

      <button type="submit" class="main-btn" name="submit">Create Account</button>

      <p class="switch-text">Already have an account? <a href="login.php">Sign In</a></p>
    </form>
  </div>

  <!-- <script src="./js/validation.js"></script> -->
</body>

</html>