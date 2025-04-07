<?php
include('db.php');  // Database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture form input
    $name = $_POST['name'];
    $party = $_POST['party'];

    // Insert new candidate into the 'candidates' table
    $sql = "INSERT INTO candidates (name, party) VALUES ('$name', '$party')";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Candidate added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Candidate</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color:rgb(52, 19, 78);
            color: #495057;
        }

        .container {
            background-color: #ffffff;
            padding: 40px;
            margin-top: 50px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ccc;
            padding: 12px;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        button[type="submit"] {
            background-color: #007bff;
            border-color: #007bff;
            padding: 12px 20px;
            font-size: 16px;
            color: white;
            border-radius: 5px;
            width: 100%;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        .alert {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Candidate</h2>

    <!-- Form for adding candidates -->
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Candidate Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="party" class="form-label">Party</label>
            <input type="text" class="form-control" id="party" name="party" required>
        </div>

        <button type="submit">Add Candidate</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
