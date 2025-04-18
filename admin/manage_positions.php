<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Handle position deletion
if (isset($_GET['delete'])) {
    $position_id = $_GET['delete'];
    
    // Check if position has candidates
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM candidates WHERE position_id = ?");
    $stmt->bind_param("i", $position_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate_count = $result->fetch_assoc()['count'];
    
    if ($candidate_count > 0) {
        $error = "Cannot delete position. Remove all candidates from this position first.";
    } else {
        // Delete position
        $stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
        $stmt->bind_param("i", $position_id);
        
        if ($stmt->execute()) {
            $success = "Position deleted successfully.";
        } else {
            $error = "Failed to delete position.";
        }
    }
}

// Handle position creation/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $max_winners = (int)$_POST['max_winners'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($title)) {
        $error = "Position title is required.";
    } else {
        if (isset($_POST['id'])) {
            // Update existing position
            $stmt = $conn->prepare("UPDATE positions SET title = ?, description = ?, max_winners = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("ssiii", $title, $description, $max_winners, $is_active, $_POST['id']);
        } else {
            // Create new position
            $stmt = $conn->prepare("INSERT INTO positions (title, description, max_winners, is_active) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssii", $title, $description, $max_winners, $is_active);
        }
        
        if ($stmt->execute()) {
            $success = isset($_POST['id']) ? "Position updated successfully." : "Position created successfully.";
        } else {
            $error = "Failed to " . (isset($_POST['id']) ? "update" : "create") . " position.";
        }
    }
}

// Get all positions with candidate count
$positions = $conn->query("
    SELECT p.*, COUNT(c.id) as candidate_count 
    FROM positions p 
    LEFT JOIN candidates c ON p.id = c.position_id 
    GROUP BY p.id 
    ORDER BY p.title
");
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Manage Positions</h4>
                    <a href="add_position.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Position
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($positions->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Maximum Winners</th>
                                        <th>Candidates</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($position = $positions->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($position['title']); ?></td>
                                            <td><?php echo htmlspecialchars($position['max_winners']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo $position['candidate_count']; ?> candidates
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_position.php?id=<?php echo $position['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <?php if ($position['candidate_count'] == 0): ?>
                                                    <a href="?delete=<?php echo $position['id']; ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Are you sure you want to delete this position?')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No positions found. 
                            <a href="add_position.php" class="alert-link">Add your first position</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Position Modal -->
<div class="modal fade" id="addPositionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="position_id">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="max_winners" class="form-label">Maximum Winners</label>
                        <input type="number" class="form-control" id="max_winners" name="max_winners" 
                               value="1" min="1" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Position</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPosition(position) {
    document.getElementById('modalTitle').textContent = 'Edit Position';
    document.getElementById('position_id').value = position.id;
    document.getElementById('title').value = position.title;
    document.getElementById('description').value = position.description;
    document.getElementById('max_winners').value = position.max_winners;
    document.getElementById('is_active').checked = position.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('addPositionModal')).show();
}
</script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?> 