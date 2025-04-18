<?php
require_once 'includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('index.php', 'Access denied. Admin privileges required.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Get all active positions for the dropdown
$positions = $conn->query("SELECT * FROM positions WHERE is_active = 1 ORDER BY title");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $position_id = (int)$_POST['position_id'];
    $bio = sanitize($_POST['bio']);
    
    // Validate inputs
    if (empty($name) || empty($position_id)) {
        $error = "Name and position are required fields.";
    } else {
        // Handle image upload
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = "Only JPEG, PNG, and GIF images are allowed.";
            } elseif ($_FILES['image']['size'] > $max_size) {
                $error = "Image size must be less than 5MB.";
            } else {
                // Create candidates directory if it doesn't exist
                $upload_dir = 'assets/images/candidates/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $file_extension;
                $image_path = $upload_dir . $filename;
                
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                    $error = "Failed to upload image.";
                }
            }
        }
        
        if (empty($error)) {
            // Insert candidate into database
            $stmt = $conn->prepare("INSERT INTO candidates (name, position_id, bio, image_path) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $name, $position_id, $bio, $image_path);
            
            if ($stmt->execute()) {
                logActivity($_SESSION['user_id'], "Added new candidate: " . $name);
                $success = "Candidate added successfully!";
                // Clear form fields
                $name = $bio = '';
                $position_id = 0;
            } else {
                $error = "Failed to add candidate. Please try again.";
            }
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Add New Candidate</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Candidate Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="position_id" class="form-label">Position</label>
                            <select class="form-select" id="position_id" name="position_id" required>
                                <option value="">Select Position</option>
                                <?php while ($position = $positions->fetch_assoc()): ?>
                                    <option value="<?php echo $position['id']; ?>" <?php echo (isset($position_id) && $position_id == $position['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($position['title']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biography</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo isset($bio) ? htmlspecialchars($bio) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Candidate Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/gif">
                            <small class="text-muted">Max file size: 5MB. Allowed formats: JPEG, PNG, GIF</small>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Candidate
                            </button>
                            <a href="admin_dashboard.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
