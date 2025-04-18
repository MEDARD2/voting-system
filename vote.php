<?php
require_once 'includes/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log responses
function logResponse($message, $type = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$type] $message";
    error_log($logMessage);
}

// Initialize variables
$error = '';
$success = '';
$errors = [];

// Check if user is logged in
if (!isLoggedIn()) {
    logResponse('User not logged in', 'warning');
    redirect('login.php', 'Please login to vote.', 'warning');
}

// Check if user is admin
if (isAdmin()) {
    $error = "Admins are not allowed to vote. Please use a regular user account to cast votes.";
    logResponse('Admin attempted to vote', 'warning');
}

// Check if user has already voted
$conn = getDBConnection();
$check_voted = $conn->prepare("SELECT has_voted FROM users WHERE id = ? FOR UPDATE");
$check_voted->bind_param("i", $_SESSION['user_id']);
$check_voted->execute();
$result = $check_voted->get_result();
$user = $result->fetch_assoc();

if ($user['has_voted']) {
    $error = "You have already cast your vote. Each voter is allowed to vote only once.";
    logResponse("User attempted to vote again: " . $_SESSION['user_id'], 'warning');
}

// Check voting time settings
if (!isset($error)) {
    $now = new DateTime();
    $start_time = new DateTime($voting_settings['start_time']);
    $end_time = new DateTime($voting_settings['end_time']);
    
    if (!$voting_settings['is_active']) {
        $error = "Voting is currently inactive.";
    } elseif ($now < $start_time) {
        $error = "Voting has not started yet. Please wait until " . $start_time->format('F j, Y g:i A');
    } elseif ($now > $end_time) {
        $error = "Voting has ended on " . $end_time->format('F j, Y g:i A');
    }
}

// Check database connection
$conn = getDBConnection();
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get all positions with their candidates
if (!isset($error)) {
    $positions = $conn->query("
        SELECT p.id, p.title, p.max_winners, 
               c.id as candidate_id, c.name as candidate_name, 
               c.bio, c.image_path
        FROM positions p
        LEFT JOIN candidates c ON p.id = c.position_id
        WHERE p.is_active = 1
        ORDER BY p.title, c.name
    ");

    // Organize positions and candidates
    $voting_data = [];
    while ($row = $positions->fetch_assoc()) {
        if (!isset($voting_data[$row['id']])) {
            $voting_data[$row['id']] = [
                'title' => $row['title'],
                'max_winners' => $row['max_winners'],
                'candidates' => []
            ];
        }
        if ($row['candidate_id']) {
            $voting_data[$row['id']]['candidates'][] = [
                'id' => $row['candidate_id'],
                'name' => $row['candidate_name'],
                'bio' => $row['bio'],
                'image_path' => $row['image_path']
            ];
        }
    }

    // Remove positions with no candidates
    $voting_data = array_filter($voting_data, function($position) {
        return !empty($position['candidates']);
    });
}

// Handle vote submission
// Add debugging logs to track the voting process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($error)) {
    // Debug: Log the POST data
    error_log("POST data received: " . print_r($_POST, true));

    // Check if votes array exists and is not empty
    if (!isset($_POST['votes']) || empty($_POST['votes'])) {
        error_log("No votes submitted");
        $error = "Please select at least one candidate to vote.";
    } else {
        $votes = $_POST['votes'];
        $has_selected_votes = false;

        // Check if any votes were selected
        foreach ($votes as $position_id => $candidate_ids) {
            if (!empty($candidate_ids)) {
                $has_selected_votes = true;
                foreach ($candidate_ids as $candidate_id) {
                    error_log("Selected vote - Position: $position_id, Candidate: $candidate_id");
                }
                break;
            }
        }

        if (!$has_selected_votes) {
            $error = "Please select at least one candidate to vote.";
            error_log("No candidates selected");
        } else {
            try {
                // Start transaction
                $conn->begin_transaction();

                // Record votes
                foreach ($votes as $position_id => $candidate_ids) {
                    foreach ($candidate_ids as $candidate_id) {
                        if (!empty($candidate_id)) {
                            // Insert vote
                            $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, position_id, created_at) VALUES (?, ?, ?, NOW())");
                            $stmt->bind_param("iii", $_SESSION['user_id'], $candidate_id, $position_id);
                            $stmt->execute();
                            error_log("Vote recorded for position ID: $position_id");
                        }
                    }
                }

                // Update user's voting status
                $update_stmt = $conn->prepare("UPDATE users SET has_voted = 1, last_vote_time = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $_SESSION['user_id']);
                $update_stmt->execute();

                // Commit transaction
                $conn->commit();

                // Set success message
                $_SESSION['vote_success'] = true;
                error_log("Vote successfully recorded for user: " . $_SESSION['user_id']);

                // Redirect to results page
                header("Location: results.php");
                exit();

            } catch (Exception $e) {
                // Rollback transaction on error
                $conn->rollback();
                $error = "Error: " . $e->getMessage();
                error_log("Vote submission error: " . $e->getMessage());
            }
        }
    }
}

// Optimize position queries by combining them
$positions_query = "
    SELECT 
        p.id, 
        p.title, 
        p.max_winners, 
        p.description,
        EXISTS (
            SELECT 1 
            FROM candidates c 
            WHERE c.position_id = p.id
        ) as has_candidates
    FROM positions p
    WHERE p.is_active = 1
    ORDER BY has_candidates DESC, p.id
";

$positions_result = $conn->query($positions_query);

if (!$positions_result) {
    $error = "Failed to load positions. Please try again later.";
    logResponse("Failed to get positions: " . $conn->error, 'error');
}

// Get positions with candidates
$positions_with_candidates = $conn->query("
    SELECT p.id, p.title, p.max_winners, p.description
    FROM positions p
    WHERE p.is_active = 1
    AND EXISTS (
        SELECT 1 FROM candidates c 
        WHERE c.position_id = p.id
    )
    ORDER BY p.id
");

if (!$positions_with_candidates) {
    $error = "Failed to load positions. Please try again later.";
    logResponse("Failed to get positions with candidates: " . $conn->error, 'error');
}

// Get positions without candidates
$positions_without_candidates = $conn->query("
    SELECT p.id, p.title, p.max_winners, p.description
    FROM positions p
    WHERE p.is_active = 1
    AND NOT EXISTS (
        SELECT 1 FROM candidates c 
        WHERE c.position_id = p.id
    )
    ORDER BY p.id
");

if (!$positions_without_candidates) {
    $error = "Failed to load positions. Please try again later.";
    logResponse("Failed to get positions without candidates: " . $conn->error, 'error');
}

// Display voting time information if no errors
if (!$error && isset($start_time) && isset($end_time)): ?>
    <div class="alert alert-info mb-4">
        <h5><i class="bi bi-clock"></i> Voting Time Information</h5>
        <p><strong>Start Time:</strong> <?php echo $start_time->format('F j, Y g:i A'); ?></p>
        <p><strong>End Time:</strong> <?php echo $end_time->format('F j, Y g:i A'); ?></p>
        <p><strong>Time Remaining:</strong> <span id="timeRemaining">Calculating...</span></p>
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote - Meddy Voting System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/vote.css">
    <style>
        /* Removed inline CSS */
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const showMoreButtons = document.querySelectorAll('.show-more-btn');

            showMoreButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const bioText = button.previousElementSibling;
                    const isExpanded = bioText.classList.toggle('expanded');
                    button.textContent = isExpanded ? 'Show Less' : 'Show More';
                });
            });
        });
    </script>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="voting-dashboard">
        <div class="dashboard-header">
            <h1>Vote for Your Candidates</h1>
            <p>Make your voice heard by selecting your preferred candidates below.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($error) && empty($success)): ?>
            <form method="POST" action="" class="voting-form" id="voteForm">
                <div class="positions-container">
                    <?php while ($position = $positions_with_candidates->fetch_assoc()): ?>
                        <div class="position-section">
                            <div class="position-header">
                                <h2><?php echo htmlspecialchars($position['title']); ?></h2>
                            </div>
                            <div class="candidates-grid">
                                <?php
                                $candidates = $conn->query("SELECT c.id, c.name, c.bio, c.image_path FROM candidates c WHERE c.position_id = " . $position['id'] . " ORDER BY c.name");
                                ?>
                                <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                    <div class="candidate-card">
                                        <img src="<?php echo htmlspecialchars($candidate['image_path']); ?>" class="candidate-image" alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                                        <div class="candidate-info">
                                            <h3><?php echo htmlspecialchars($candidate['name']); ?></h3>
                                            <p class="bio-text">
                                                <?php echo htmlspecialchars($candidate['bio']); ?>
                                            </p>
                                            <button type="button" class="show-more-btn">Show More</button>
                                            <div class="custom-checkbox">
                                                <input type="checkbox" name="votes[<?php echo $position['id']; ?>][]" id="candidate_<?php echo $candidate['id']; ?>" value="<?php echo $candidate['id']; ?>">
                                                <label for="candidate_<?php echo $candidate['id']; ?>">Select</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="submit-section">
                    <button type="submit" class="submit-button" id="submitVote">Submit Your Vote</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>


