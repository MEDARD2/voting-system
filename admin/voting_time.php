<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

$conn = getDBConnection();
$error = '';
$success = '';

// Get current voting settings
$settings = $conn->query("SELECT * FROM voting_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validate times
    if (strtotime($start_time) >= strtotime($end_time)) {
        $error = "End time must be after start time.";
    } else {
        $stmt = $conn->prepare("UPDATE voting_settings SET start_time = ?, end_time = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("ssii", $start_time, $end_time, $is_active, $settings['id']);
        
        if ($stmt->execute()) {
            $success = "Voting time settings updated successfully.";
            // Refresh settings
            $settings = $conn->query("SELECT * FROM voting_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
        } else {
            $error = "Failed to update voting time settings.";
        }
    }
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Manage Voting Time</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start Time</label>
                            <input type="datetime-local" class="form-control" id="start_time" name="start_time" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($settings['start_time'])); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End Time</label>
                            <input type="datetime-local" class="form-control" id="end_time" name="end_time" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($settings['end_time'])); ?>" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                   <?php echo $settings['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_active">Enable Voting</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                    
                    <div class="mt-4">
                        <h5>Current Status</h5>
                        <div class="card">
                            <div class="card-body">
                                <p><strong>Start Time:</strong> <?php echo date('F j, Y g:i A', strtotime($settings['start_time'])); ?></p>
                                <p><strong>End Time:</strong> <?php echo date('F j, Y g:i A', strtotime($settings['end_time'])); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge <?php echo $settings['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $settings['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </p>
                                <p><strong>Time Remaining:</strong> 
                                    <span id="timeRemaining"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTimeRemaining() {
    const endTime = new Date('<?php echo $settings['end_time']; ?>').getTime();
    const now = new Date().getTime();
    const distance = endTime - now;
    
    if (distance < 0) {
        document.getElementById('timeRemaining').innerHTML = 'Voting has ended';
        return;
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    document.getElementById('timeRemaining').innerHTML = 
        `${days}d ${hours}h ${minutes}m ${seconds}s`;
}

// Update time remaining every second
setInterval(updateTimeRemaining, 1000);
updateTimeRemaining(); // Initial call
</script>

<?php require_once '../includes/footer.php'; ?> 