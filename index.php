<?php
require_once 'includes/header.php';

// Get some statistics
$conn = getDBConnection();
$total_voters = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 0")->fetch_assoc()['count'];
$total_positions = $conn->query("SELECT COUNT(*) as count FROM positions WHERE is_active = 1")->fetch_assoc()['count'];
$total_candidates = $conn->query("SELECT COUNT(*) as count FROM candidates")->fetch_assoc()['count'];
$total_votes = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM votes")->fetch_assoc()['count'];
?>

<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center py-5 bg-light rounded shadow-sm">
            <h1 class="display-4 mb-4">Welcome to <?php echo SITE_NAME; ?></h1>
            <p class="lead mb-4">A secure and transparent platform for conducting elections with integrity and efficiency.</p>
            <?php if (!isLoggedIn()): ?>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="login.php" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Vote
                    </a>
                    <a href="register.php" class="btn btn-outline-primary btn-lg px-4">
                        <i class="bi bi-person-plus"></i> Register Now
                    </a>
                </div>
            <?php else: ?>
                <a href="vote.php" class="btn btn-primary btn-lg px-4">
                    <i class="bi bi-check2-square"></i> Cast Your Vote
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2"><?php echo number_format($total_voters); ?></h3>
                    <p class="text-muted mb-0">Registered Voters</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <i class="bi bi-list-check text-success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2"><?php echo number_format($total_positions); ?></h3>
                    <p class="text-muted mb-0">Active Positions</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <i class="bi bi-person-badge text-info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2"><?php echo number_format($total_candidates); ?></h3>
                    <p class="text-muted mb-0">Total Candidates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <i class="bi bi-check2-circle text-warning" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2"><?php echo number_format($total_votes); ?></h3>
                    <p class="text-muted mb-0">Votes Cast</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center mb-4">
            <h2>Why Choose Our Voting System?</h2>
            <p class="text-muted">Experience a modern and secure voting platform</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-shield-check text-primary mb-3" style="font-size: 2rem;"></i>
                    <h4>Secure Voting</h4>
                    <p class="text-muted">Your vote is protected with advanced security measures and encryption.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up text-success mb-3" style="font-size: 2rem;"></i>
                    <h4>Real-time Results</h4>
                    <p class="text-muted">View election results and statistics as they happen.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-card">
                <div class="card-body text-center">
                    <i class="bi bi-phone text-info mb-3" style="font-size: 2rem;"></i>
                    <h4>Mobile Friendly</h4>
                    <p class="text-muted">Vote from any device, anywhere, at any time during the election period.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center mb-4">
            <h2>How It Works</h2>
            <p class="text-muted">Simple steps to cast your vote</p>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <h3 class="mb-0">1</h3>
                    </div>
                    <h5>Register</h5>
                    <p class="text-muted">Create your account with valid credentials</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <h3 class="mb-0">2</h3>
                    </div>
                    <h5>Login</h5>
                    <p class="text-muted">Sign in to access the voting system</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <div class="rounded-circle bg-info text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <h3 class="mb-0">3</h3>
                    </div>
                    <h5>Select</h5>
                    <p class="text-muted">Choose your preferred candidates</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm text-center hover-card">
                <div class="card-body">
                    <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <h3 class="mb-0">4</h3>
                    </div>
                    <h5>Submit</h5>
                    <p class="text-muted">Cast your vote securely</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="bg-primary text-white text-center p-5 rounded shadow">
                <h2 class="mb-3">Ready to Make Your Voice Heard?</h2>
                <p class="lead mb-4">Join our secure voting platform today and participate in shaping the future.</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-light btn-lg">
                        <i class="bi bi-person-plus"></i> Get Started Now
                    </a>
                <?php else: ?>
                    <a href="vote.php" class="btn btn-light btn-lg">
                        <i class="bi bi-check2-square"></i> Cast Your Vote
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
.bg-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}
.rounded-circle {
    width: 50px;
    height: 50px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php require_once 'includes/footer.php'; ?> 