<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/vote.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .dashboard-card {
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .dashboard-card h3 {
            font-size: 1.5rem;
            color: var(--text-color);
        }

        .dashboard-card p {
            color: var(--secondary-color);
        }

        .dashboard-card a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: #fff;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }

        .dashboard-card a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>

    <div class="voting-dashboard">
        <div class="dashboard-header">
            <div class="header-content">
                <h1>Admin Dashboard</h1>
                <p>Manage the voting system efficiently</p>
            </div>
        </div>

        <div class="dashboard-container">
            <div class="dashboard-card">
                <i class="bi bi-person-plus"></i>
                <h3>Add Candidate</h3>
                <p>Add new candidates to the voting system.</p>
                <a href="add_candidate.php">Go</a>
            </div>

            <div class="dashboard-card">
                <i class="bi bi-people"></i>
                <h3>Manage Candidates</h3>
                <p>View, edit, or delete candidates.</p>
                <a href="admin/manage_candidates.php">Go</a>
            </div>

            <div class="dashboard-card">
                <i class="bi bi-list-task"></i>
                <h3>Manage Positions</h3>
                <p>Define and manage voting positions.</p>
                <a href="admin/manage_positions.php">Go</a>
            </div>

            <div class="dashboard-card">
                <i class="bi bi-clock"></i>
                <h3>Voting Time</h3>
                <p>Set and manage voting time periods.</p>
                <a href="admin/voting_time.php">Go</a>
            </div>

            <div class="dashboard-card">
                <i class="bi bi-bar-chart"></i>
                <h3>View Results</h3>
                <p>Check the voting results.</p>
                <a href="results.php">Go</a>
            </div>

            <div class="dashboard-card">
                <i class="bi bi-gear"></i>
                <h3>Settings</h3>
                <p>Configure system settings.</p>
                <a href="config/config.php">Go</a>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>
