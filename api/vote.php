<?php
require_once '../classes/Vote.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    // Check if the user is logged in
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in to vote.']);
        exit;
    }

    // Get the user ID from the session
    $userId = $_SESSION['user_id'];

    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['votes']) || empty($data['votes'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No votes submitted.']);
        exit;
    }

    $votes = $data['votes'];

    // Initialize the Vote class
    $vote = new Vote($userId);

    // Submit the votes
    $vote->submitVotes($votes);

    // Log the successful vote submission
    logResponse("User $userId successfully submitted votes.", 'info');

    // Redirect to the results page with a success message
    echo json_encode(['success' => true, 'message' => 'Votes successfully submitted.', 'redirect' => '/results.php']);
    exit;

} catch (Exception $e) {
    // Log the error
    logResponse("Error during vote submission: " . $e->getMessage(), 'error');

    // Return an error response
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}