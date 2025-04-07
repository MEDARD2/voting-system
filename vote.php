<?php
session_start();
include('db.php');

// Ensure the user is logged in before they can vote
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get list of candidates
$sql = "SELECT * FROM candidates";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture selected candidate ID
    $candidate_id = $_POST['candidate_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user has already voted
    $sql_check = "SELECT * FROM votes WHERE user_id='$user_id'";
    $check_result = $conn->query($sql_check);
    if ($check_result->num_rows > 0) {
        echo "<div class='alert alert-warning'>You have already voted.</div>";
    } else {
        // Insert vote into 'votes' table
        $sql_vote = "INSERT INTO votes (user_id, candidate_id) VALUES ('$user_id', '$candidate_id')";
        if ($conn->query($sql_vote) === TRUE) {
            // Redirect to a thank you page after voting
            header("Location: thank_you.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Your Candidate</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('images/Screenshot 2025-04-06 144921.png');
            font-family: Arial, sans-serif;
        }

        .vote-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .vote-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .vote-container .form-check {
            margin-bottom: 15px;
        }

        .vote-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .vote-container button:hover {
            background-color: #0056b3;
        }

        .alert {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="vote-container">
    <h2>Select Your Candidate</h2>
    <form method="POST">
        <?php while ($candidate = $result->fetch_assoc()) { ?>
            <div class="form-check">
                <input type="radio" class="form-check-input" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                <label class="form-check-label">
                    <?php echo $candidate['name'] . " (" . $candidate['party'] . ")"; ?>
                </label>
            </div>
        <?php } ?>
        <button type="submit">Vote</button>
    </form>
</div>

<!-- Bootstrap 5 JS (Optional, for interactivity) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
