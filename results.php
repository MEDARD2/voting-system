<?php
include('db.php');

// Get vote count for each candidate
$sql = "SELECT candidates.name, COUNT(votes.candidate_id) AS vote_count
        FROM candidates
        LEFT JOIN votes ON candidates.id = votes.candidate_id
        GROUP BY candidates.id";
$result = $conn->query($sql);

echo "<h2>Voting Results:</h2>";
while ($row = $result->fetch_assoc()) {
    echo $row['name'] . " (" . $row['party'] . "): " . $row['vote_count'] . " votes<br>";
}

?>
