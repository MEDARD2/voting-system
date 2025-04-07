<?php
session_start();
session_destroy(); // Destroy the session
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="3;url=admin_login.php"> <!-- Redirect after 3 seconds -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logging Out...</title>

  <style>
    body {
      background: linear-gradient(to right, #00c6ff, #0072ff);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      color: #fff;
      text-align: center;
    }

    .message-box {
      background: rgba(255, 255, 255, 0.1);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }

    .spinner {
      margin: 20px auto;
      width: 50px;
      height: 50px;
      border: 5px solid rgba(255,255,255,0.3);
      border-top: 5px solid #fff;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    h1 {
      font-size: 28px;
      margin-bottom: 10px;
    }

    p {
      font-size: 18px;
    }
  </style>
</head>

<body>

<div class="message-box">
  <h1>Logging out...</h1>
  <div class="spinner"></div>
  <p>You will be redirected shortly.</p>
</div>

</body>
</html>
