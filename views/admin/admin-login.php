<?php





?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Page</title>

  <link rel="stylesheet" href="../../css/admin_login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bungee+Spice&family=Ubuntu:wght@300;400;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">


</head>

<body>
  <div class="container fade-in">
    <div class="login-form">
      <h2>Admin Login</h2>
      <!-- <pre><?php print_r($_SESSION) ?> </pre> -->
      <form id="loginForm" action="/admin/login" method="POST">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
      </form>
      <div class="register">
        <p>Don't have an account? <a href="/admin/register">Register</a></p>
      </div>
      <?php

      if (session_status() == PHP_SESSION_NONE) {
        session_start();
      }

      // Check if there is a login message in session
      if (isset($_SESSION['login_message'])) {
        $loginMessage = $_SESSION['login_message'];
        unset($_SESSION['login_message']); // Clear the message after displaying it
      }

      // Check if there is a registration success message in session
      if (isset($_SESSION['register_success'])) {
        $registerSuccessMessage = $_SESSION['register_success'];
        unset($_SESSION['register_success']); // Clear the message after displaying it
      }

      // Display the login error message (if any)
      if (!empty($loginMessage)): ?>
        <div class="error-message"><?= htmlspecialchars($loginMessage) ?></div>
      <?php endif; ?>

      <?php
      // Display the registration success message (if any)
      if (!empty($registerSuccessMessage)): ?>
        <div class="success-message"><?= htmlspecialchars($registerSuccessMessage) ?></div>
      <?php endif; ?>


    </div>
    <div class="hero">
      <img src="../../assets/images/hero3.png" alt="SNS" class="hero-image">
      <h1 class="welcome">Welcome to Stack and Shop Admin </h1>
      <p>Sell your bricks, One sale at a time!</p>
    </div>
  </div>

</body>

</html>