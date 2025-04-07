<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .dashboard-card {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .dashboard-card h2 {
            margin-bottom: 30px;
            font-weight: 600;
            color: #333;
        }

        .btn-custom {
            padding: 15px;
            font-size: 18px;
            border-radius: 12px;
            width: 100%;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        a.btn {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="dashboard-card">
    <h2>Welcome Admin, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h2>

    <div class="row g-3">
        <div class="col-md-6">
            <a href="admin_votes.php" class="btn btn-primary btn-custom">View Voting Results</a>
        </div>
        <div class="col-md-6">
            <a href="add_candidate.php" class="btn btn-success btn-custom">Add New Candidate</a>
        </div>
    </div>

    <div class="mt-4">
        <a href="logout.php" class="btn btn-danger btn-custom">Logout</a>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
