<?php
require_once 'config/database.php';

$conn = getDBConnection();

// Check if positions table exists
$result = $conn->query("SHOW TABLES LIKE 'positions'");
if ($result->num_rows == 0) {
    die("Positions table does not exist. Please run the schema.sql file first.");
}

// Check if positions table has data
$result = $conn->query("SELECT COUNT(*) as count FROM positions");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    die("Positions table is empty. Please run the schema.sql file to insert default positions.");
}

// Display positions
$result = $conn->query("SELECT * FROM positions");
echo "<h2>Positions in Database:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Title</th><th>Description</th><th>Max Winners</th><th>Is Active</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['title'] . "</td>";
    echo "<td>" . $row['description'] . "</td>";
    echo "<td>" . $row['max_winners'] . "</td>";
    echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";

$conn->close();
?> 