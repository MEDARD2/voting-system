<?php
require_once 'includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('index.php', 'Access denied. Admin privileges required.', 'danger');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('admin_dashboard.php', 'Invalid candidate ID.', 'danger');
}

$candidate_id = (int)$_GET['id'];
$conn = getDBConnection();

// Get candidate details
$stmt = $conn->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    redirect('admin_dashboard.php', 'Candidate not found.', 'danger');
}

$candidate = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $position = sanitize($_POST['position']);
    $bio = sanitize($_POST['bio']);
    
    $errors = [];
    
    // Validate input
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    if (empty($position)) {
        $errors[] = "Position is required";
    }
    
    // Handle image upload
    $image_path = $candidate['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowed_types)) {
            $errors[] = "Only JPG, PNG, and GIF images are allowed";
        } elseif ($_FILES['image']['size'] > $max_size) {
            $errors[] = "Image size must be less than 5MB";
        } else {
            $upload_dir = 'assets/images/candidates/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // Delete old image if exists
                if (!empty($candidate['image_path']) && file_exists($candidate['image_path'])) {
                    unlink($candidate['image_path']);
                }
                $image_path = $target_path;
            } else {
                $errors[] = "Failed to upload image";
            }
        }
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE candidates SET name = ?, position = ?, bio = ?, image_path = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $position, $bio, $image_path, $candidate_id);
        
        if ($stmt->execute()) {
            logActivity($_SESSION['user_id'], "Updated candidate: $name");
            redirect('admin_dashboard.php', 'Candidate updated successfully.', 'success');
        } else {
            $errors[] = "Failed to update candidate";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Edit Candidate</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($candidate['name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" 
                                   value="<?php echo htmlspecialchars($candidate['position']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biography</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php 
                                echo htmlspecialchars($candidate['bio']); 
                            ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Candidate Image</label>
                            <?php if (!empty($candidate['image_path'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo htmlspecialchars($candidate['image_path']); ?>" 
                                         class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">Max file size: 5MB. Allowed formats: JPG, PNG, GIF</small>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Candidate</button>
                            <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?> 