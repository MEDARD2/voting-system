<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $max_winners = (int)$_POST['max_winners'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate input
    if (empty($title)) {
        $error = "Position title is required.";
    } elseif ($max_winners < 1) {
        $error = "Maximum winners must be at least 1.";
    } else {
        // Check if position title already exists
        $stmt = $conn->prepare("SELECT id FROM positions WHERE title = ?");
        $stmt->bind_param("s", $title);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "A position with this title already exists.";
        } else {
            // Create new position
            $stmt = $conn->prepare("INSERT INTO positions (title, description, max_winners, is_active) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $title, $description, $max_winners, $is_active);
            
            if ($stmt->execute()) {
                redirect('manage_positions.php', 'Position created successfully.', 'success');
            } else {
                $error = "Failed to create position.";
            }
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Add New Position</h4>
                    <a href="manage_positions.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Positions
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                                   required>
                            <div class="form-text">Enter the position title (e.g., President, Secretary)</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                            <div class="form-text">Provide a brief description of the position (optional)</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_winners" class="form-label">Maximum Winners <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="max_winners" name="max_winners" 
                                   value="<?php echo isset($_POST['max_winners']) ? htmlspecialchars($_POST['max_winners']) : '1'; ?>" 
                                   min="1" required>
                            <div class="form-text">Enter the number of candidates that can win this position</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   <?php echo (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Inactive positions won't appear in the voting form</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Position
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 