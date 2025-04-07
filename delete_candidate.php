<?php
session_start();
include('db.php'); // Include your database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

if (isset($_GET['id'])) {
    // Get the candidate ID from the URL
    $candidate_id = $_GET['id'];

    // Prepare the SQL to delete the candidate from the database
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $candidate_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "Candidate deleted successfully!";
        header("Location: voting_results.php"); // Redirect back to the results page
        exit();
    } else {
        echo "Error deleting candidate: " . $conn->error;
    }
} else {
    echo "Invalid candidate ID.";
}
?>
