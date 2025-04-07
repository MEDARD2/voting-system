if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture selected candidate ID
    $candidate_id = $_POST['candidate_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user has already voted
    $sql_check = "SELECT * FROM votes WHERE user_id='$user_id'";
    $check_result = $conn->query($sql_check);
    if ($check_result->num_rows > 0) {
        echo "You have already voted.";
    } else {
        // Insert vote into 'votes' table
        $sql_vote = "INSERT INTO votes (user_id, candidate_id) VALUES ('$user_id', '$candidate_id')";
        if ($conn->query($sql_vote) === TRUE) {
            echo "Vote submitted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
