<?php
echo "<h2>Thank you for voting!</h2>";
echo "<p>Your vote has been successfully recorded.</p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <h1>THANKS FOR BEING A GOOD VOTER</h1>
    <style>
        
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('images/Screenshot 2025-04-06 171713.png');
            color:rgb(147, 129, 39);
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
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            color: #28a745;
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 24px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Thank you for voting!</h2>
    <p>Your vote has been successfully recorded.</p>
    <a href="vote.php" class="btn">Go Back to Voting Page</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
