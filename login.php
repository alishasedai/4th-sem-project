<?php
session_start();
include "./includes/db_connect.php";

// 🚫 Remove any redirect check here — we handle it only after successful login.

// Handle login form
if (isset($_POST['submit'])) {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  if (empty($email) || empty($password)) {
    echo "<script>alert('Please enter both email and password!');</script>";
  } else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role']; // make sure your column name is 'role'

        // Redirect after login if set
        if (isset($_SESSION['redirect_after_login'])) {
          $redirect_url = $_SESSION['redirect_after_login'];
          unset($_SESSION['redirect_after_login']);
          header("Location: $redirect_url");
          exit();
        } else {
          // Redirect based on role if no redirect URL
          if ($user['role'] === 'customer') {
            header("Location: index.php"); // Customer dashboard
            exit();
          } elseif ($user['role'] === 'contractor') {
            header("Location: contractor_dashboard.php"); // Contractor dashboard
            exit();
          } elseif ($user['role'] === 'super_admin') {
            header("Location: super_admin_dashboard.php"); // Super Admin dashboard
            exit();
          } else {
            // default fallback
            header("Location: index.php");
            exit();
          }
        }
      }
    } else {
      echo "<script>alert('No account found with that email!');</script>";
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Gypsum Services</title>
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <div class="container">
    <button class="back-btn"><a href="index.php">← Back To Home</a></button>

    <form class="form-box" method="POST" action="" onsubmit="validateLoginForm(event)">
      <h2>Welcome Back</h2>
      <p class="subtitle">Sign in to your Gypsum Services account</p>

      <label>Email</label>
      <div class="input-box">
        <input type="email" placeholder="Enter your Email" name="email" required>
      </div>

      <label>Password</label>
      <div class="input-box">
        <input type="password" placeholder="Enter your Password" name="password" required>
      </div>

      <button type="submit" class="main-btn" name="submit">Sign In</button>

      <p class="switch-text">Don't have an account? <a href="signup.php">Sign up</a></p>
    </form>
  </div>
</body>

</html>