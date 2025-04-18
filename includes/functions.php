<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

session_start();

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] === true || $_SESSION['is_admin'] == 1);
}

// Generate secure random token
function generateToken() {
    return bin2hex(random_bytes(32));
}

// Redirect with message
function redirect($url, $message = '', $type = '') {
    if (!empty($message)) {
        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $type;
    }
    header("Location: $url");
    exit();
}

// Display flash message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return "<div class='alert alert-$type'>$message</div>";
    }
    return '';
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Get user by ID
function getUserById($id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Log activity
function logActivity($user_id, $action) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
    $stmt->close();
}

/**
 * Check if the current logged-in user has already voted
 * @return bool Returns true if the user has voted, false otherwise
 */
function hasVoted() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $conn = getDBConnection();
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("SELECT has_voted FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['has_voted'] == 1;
    }
    
    return false;
}
?> 