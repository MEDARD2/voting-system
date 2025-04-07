<?php
session_start();
include('db.php'); // Connect to the database

// Fetch candidates data from the database
$sql = "SELECT * FROM candidates";
$result = $conn->query($sql);

// Initialize arrays to hold names, votes, and candidate IDs
$names = [];
$votes = [];
$ids = [];

if ($result->num_rows > 0) {
    // Fetch data from the database
    while ($row = $result->fetch_assoc()) {
        $names[] = $row['name'];  // Store candidate names
        $votes[] = $row['votes']; // Store candidate votes
        $ids[] = $row['id'];      // Store candidate IDs (for deletion)
    }
} else {
    echo "No candidates found in the database.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f4f4f4; padding: 30px;">

<div class="container mt-5">
    <h2 class="text-center">Voting Results</h2>

    <h3 class="mt-5">Candidates List</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Votes</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display each candidate with a delete button
            for ($i = 0; $i < count($names); $i++) {
                echo "<tr>
                        <td>{$names[$i]}</td>
                        <td>{$votes[$i]}</td>
                        <td><a href='delete_candidate.php?id={$ids[$i]}' class='btn btn-danger'>Delete</a></td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
