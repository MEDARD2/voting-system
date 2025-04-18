<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Get position ID from URL
$position_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch position data
$stmt = $conn->prepare("SELECT * FROM positions WHERE id = ?");
$stmt->bind_param("i", $position_id);
$stmt->execute();
$position = $stmt->get_result()->fetch_assoc();

if (!$position) {
    redirect('manage_positions.php', 'Position not found.', 'danger');
}

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
        // Check if title exists for other positions
        $stmt = $conn->prepare("SELECT id FROM positions WHERE title = ? AND id != ?");
        $stmt->bind_param("si", $title, $position_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "A position with this title already exists.";
        } else {
            // Update position
            $stmt = $conn->prepare("UPDATE positions SET title = ?, description = ?, max_winners = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("ssiii", $title, $description, $max_winners, $is_active, $position_id);
            
            if ($stmt->execute()) {
                redirect('manage_positions.php', 'Position updated successfully.', 'success');
            } else {
                $error = "Failed to update position.";
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
                    <h4 class="mb-0">Edit Position</h4>
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
                                   value="<?php echo htmlspecialchars($position['title']); ?>" 
                                   required>
                            <div class="form-text">Enter the position title (e.g., President, Secretary)</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3"><?php echo htmlspecialchars($position['description']); ?></textarea>
                            <div class="form-text">Provide a brief description of the position (optional)</div>
                        </div>

                        <div class="mb-3">
                            <label for="max_winners" class="form-label">Maximum Winners <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="max_winners" name="max_winners" 
                                   value="<?php echo htmlspecialchars($position['max_winners']); ?>" 
                                   min="1" required>
                            <div class="form-text">Enter the number of candidates that can win this position</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   <?php echo $position['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Active</label>
                            <div class="form-text">Inactive positions won't appear in the voting form</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Position
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 