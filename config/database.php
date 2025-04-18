<?php
require_once __DIR__ . '/config.php';

function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Check connection
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Only run test connection if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    try {
        $test_conn = getDBConnection();
        echo "Database connection successful!";
        $test_conn->close();
    } catch (Exception $e) {
        echo "Database connection failed: " . $e->getMessage();
    }
}
?> 