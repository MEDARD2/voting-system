<?php
include('db.php');  // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the email already exists in the database
    $sql_check = "SELECT * FROM users WHERE email='$email'";
    $result_check = $conn->query($sql_check);

    // If the email already exists, display an error message
    if ($result_check->num_rows > 0) {
        echo "<div class='alert alert-danger'>Error: This email is already registered.</div>";
    } else {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the 'users' table
        $sql = "INSERT INTO users (email, password) VALUES ('$email', '$hashedPassword')";
        if ($conn->query($sql) === TRUE) {
            echo "<div class='alert alert-success'>New user registered successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!-- Registration Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('images/Screenshot 2025-04-06 144921.png');
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            color: #28a745;
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert {
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
        }

        button[type="submit"]:hover {
            background-color:rgb(26, 77, 65);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register New Account</h2>

    <!-- Registration form -->
    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <div class="form-footer">
        <a href="login.php">Already have an account? Login here</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
