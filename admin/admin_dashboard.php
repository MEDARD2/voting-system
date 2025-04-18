<?php
require_once '../includes/header.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../index.php', 'Access denied. Admin only.', 'danger');
}

// Get database connection
$conn = getDBConnection();

// Get counts for dashboard
$users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$positions_count = $conn->query("SELECT COUNT(*) as count FROM positions")->fetch_assoc()['count'];
$candidates_count = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
$votes_count = $conn->query("SELECT COUNT(*) as count FROM votes")->fetch_assoc()['count'];

// Get voting settings
$settings_result = $conn->query("SELECT * FROM voting_settings ORDER BY id DESC LIMIT 1");
$settings = $settings_result ? $settings_result->fetch_assoc() : null;
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Admin Dashboard</h2>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <h2><?php echo $users_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Positions</h5>
                    <h2><?php echo $positions_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Candidates</h5>
                    <h2><?php echo $candidates_count; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Votes</h5>
                    <h2><?php echo $votes_count; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Voting Time Status -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Voting Time Settings</h5>
                    <a href="voting_time.php" class="btn btn-primary">
                        <i class="bi bi-gear"></i> Manage Voting Time
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($settings): ?>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Start Time:</strong> <?php echo date('F j, Y g:i A', strtotime($settings['start_time'])); ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>End Time:</strong> <?php echo date('F j, Y g:i A', strtotime($settings['end_time'])); ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Status:</strong> 
                                    <span class="badge <?php echo $settings['is_active'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $settings['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> No voting time settings found. 
                            <a href="voting_time.php" class="alert-link">Click here to set up voting time settings</a>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Positions</h5>
                    <p class="card-text">Add, edit, or remove voting positions.</p>
                    <a href="manage_positions.php" class="btn btn-primary">
                        <i class="bi bi-list-ul"></i> Manage Positions
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Manage Candidates</h5>
                    <p class="card-text">Add, edit, or remove candidates.</p>
                    <a href="manage_candidates.php" class="btn btn-primary">
                        <i class="bi bi-people"></i> Manage Candidates
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">View Results</h5>
                    <p class="card-text">View current voting results.</p>
                    <a href="results.php" class="btn btn-primary">
                        <i class="bi bi-bar-chart"></i> View Results
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 