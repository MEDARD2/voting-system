<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
session_start();

// Get voting settings
$conn = getDBConnection();
$settings_result = $conn->query("SELECT * FROM voting_settings ORDER BY id DESC LIMIT 1");
$voting_settings = $settings_result ? $settings_result->fetch_assoc() : null;

// Calculate voting status and time remaining
$now = new DateTime();
$voting_active = false;
$time_remaining = '';
$voting_status = '';

if ($voting_settings) {
    $start_time = new DateTime($voting_settings['start_time']);
    $end_time = new DateTime($voting_settings['end_time']);
    
    if ($voting_settings['is_active']) {
        if ($now < $start_time) {
            $voting_status = 'Voting will start in:';
            $interval = $now->diff($start_time);
        } elseif ($now >= $start_time && $now <= $end_time) {
            $voting_active = true;
            $voting_status = 'Voting ends in:';
            $interval = $now->diff($end_time);
        } else {
            $voting_status = 'Voting has ended';
        }
    } else {
        $voting_status = 'Voting is currently inactive';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Add base URL for proper path resolution -->
    <base href="/">
    
    <style>
        /* Navigation Styles */
        .navbar {
            padding: 1.25rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .navbar-brand:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-item {
            margin: 0 0.5rem;
        }

        .navbar-nav .nav-link {
            font-size: 1.2rem;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .navbar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .navbar-dark.bg-primary {
            background: linear-gradient(45deg, #0d6efd, #0a58ca) !important;
        }

        .navbar-toggler {
            padding: 0.75rem;
            font-size: 1.25rem;
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 8px;
        }

        .navbar-toggler:focus {
            box-shadow: 0 0 0 3px rgba(255,255,255,0.25);
        }

        .navbar-toggler-icon {
            width: 1.5em;
            height: 1.5em;
        }

        /* Welcome message styling */
        .navbar-nav .nav-item .nav-link:has(span) {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            margin-right: 1rem;
        }

        /* Active link styling */
        .navbar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
            font-weight: 600;
        }

        /* Dropdown styling if needed */
        .dropdown-menu {
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 12px;
            padding: 1rem 0;
        }

        .dropdown-item {
            font-size: 1.1rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }

        @media (max-width: 992px) {
            .navbar-collapse {
                padding: 1rem 0;
            }

            .navbar-nav .nav-item {
                margin: 0.5rem 0;
            }

            .navbar-nav .nav-link {
                padding: 1rem 1.5rem;
            }
        }

        .candidate-image {
            width: 100%;
            height: 250px;
            object-fit: contain;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }
        
        .candidate-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .candidate-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            padding: 1.25rem;
        }
        
        .candidate-card .card-text {
            flex-grow: 1;
            margin-bottom: 1rem;
        }
        
        .candidate-card .vote-button {
            margin-top: auto;
            width: 100%;
        }
        
        .img-thumbnail {
            width: 100%;
            max-width: 200px;
            height: 200px;
            object-fit: contain;
            background-color: #f8f9fa;
            padding: 5px;
            border-radius: 4px;
        }
        
        .progress {
            height: 25px;
            margin-top: 10px;
        }
        
        .progress-bar {
            font-size: 14px;
            line-height: 25px;
        }

        .card-img-top {
            border-bottom: 1px solid rgba(0,0,0,.125);
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }

        .voting-timer {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
        }
        .countdown {
            font-size: 1.2em;
            font-weight: bold;
            color: #0d6efd;
        }
        .voting-inactive {
            color: #dc3545;
        }
        .voting-active {
            color: #198754;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-check-square-fill me-2"></i><?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/index.php') ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-fill me-2"></i>Home
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/vote.php') ? 'active' : ''; ?>" href="vote.php">
                                <i class="bi bi-check-circle-fill me-2"></i>Vote
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/results.php') ? 'active' : ''; ?>" href="results.php">
                                <i class="bi bi-bar-chart-fill me-2"></i>Results
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/admin/admin_dashboard.php') !== false) ? 'active' : ''; ?>" href="admin/admin_dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/admin/add_candidate.php') !== false) ? 'active' : ''; ?>" href="admin/add_candidate.php">
                                    <i class="bi bi-person-plus-fill me-2"></i>Add Candidate
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/admin/manage_positions.php') !== false) ? 'active' : ''; ?>" href="admin/manage_positions.php">
                                    <i class="bi bi-list-check me-2"></i>Manage Positions
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], '/admin/voting_time.php') !== false) ? 'active' : ''; ?>" href="admin/voting_time.php">
                                    <i class="bi bi-clock-fill me-2"></i>Voting Time Settings
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="bi bi-person-circle me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/login.php') ? 'active' : ''; ?>" href="login.php">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($_SERVER['PHP_SELF'] == '/register.php') ? 'active' : ''; ?>" href="register.php">
                                <i class="bi bi-person-plus me-2"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php 
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php echo displayMessage(); ?> 
    </div>

    <?php if ($voting_settings && !isAdmin()): ?>
    <div class="container">
        <div class="voting-timer">
            <div id="voting-status" class="<?php echo $voting_active ? 'voting-active' : 'voting-inactive'; ?>">
                <?php echo $voting_status; ?>
            </div>
            <?php if ($voting_settings['is_active'] && ($now < $end_time)): ?>
                <div id="countdown" class="countdown" 
                     data-start="<?php echo $start_time->format('Y-m-d H:i:s'); ?>"
                     data-end="<?php echo $end_time->format('Y-m-d H:i:s'); ?>">
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <script>
    function updateCountdown() {
        const countdownElement = document.getElementById('countdown');
        if (!countdownElement) return;

        const now = new Date().getTime();
        const startTime = new Date(countdownElement.dataset.start).getTime();
        const endTime = new Date(countdownElement.dataset.end).getTime();
        
        let distance;
        let statusElement = document.getElementById('voting-status');
        
        if (now < startTime) {
            distance = startTime - now;
            statusElement.textContent = 'Voting will start in:';
            statusElement.className = 'voting-inactive';
        } else if (now < endTime) {
            distance = endTime - now;
            statusElement.textContent = 'Voting ends in:';
            statusElement.className = 'voting-active';
        } else {
            statusElement.textContent = 'Voting has ended';
            statusElement.className = 'voting-inactive';
            countdownElement.style.display = 'none';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }

    // Update countdown every second
    if (document.getElementById('countdown')) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
    </script>
</body>
</html> 