<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Handle candidate deletion
if (isset($_GET['delete'])) {
    $candidate_id = $_GET['delete'];
    
    // Get candidate image path before deletion
    $stmt = $conn->prepare("SELECT image_path FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $candidate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $candidate = $result->fetch_assoc();
    
    // Delete candidate
    $stmt = $conn->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->bind_param("i", $candidate_id);
    
    if ($stmt->execute()) {
        // Delete image file if it exists
        if (!empty($candidate['image_path'])) {
            $image_path = '../' . $candidate['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $success = "Candidate deleted successfully.";
    } else {
        $error = "Failed to delete candidate.";
    }
}

// Get all candidates with their positions
$candidates = $conn->query("
    SELECT c.*, p.title as position_title 
    FROM candidates c 
    JOIN positions p ON c.position_id = p.id 
    ORDER BY p.title, c.name
");
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Manage Candidates</h4>
                    <a href="add_candidate.php" class="btn btn-primary">
                        <i class="bi bi-person-plus"></i> Add New Candidate
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
                    
                    <?php if ($candidates->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Biography</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($candidate = $candidates->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?php if (!empty($candidate['image_path'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($candidate['image_path']); ?>" 
                                                         alt="<?php echo htmlspecialchars($candidate['name']); ?>"
                                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="text-center text-muted">
                                                        <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                            <td><?php echo htmlspecialchars($candidate['position_title']); ?></td>
                                            <td>
                                                <?php 
                                                $bio = htmlspecialchars($candidate['bio']);
                                                echo strlen($bio) > 100 ? substr($bio, 0, 100) . '...' : $bio;
                                                ?>
                                            </td>
                                            <td>
                                                <a href="edit_candidate.php?id=<?php echo $candidate['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="?delete=<?php echo $candidate['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('Are you sure you want to delete this candidate?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No candidates found. 
                            <a href="add_candidate.php" class="alert-link">Add your first candidate</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 