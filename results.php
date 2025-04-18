<?php
require_once 'includes/header.php';

// Get total votes
$conn = getDBConnection();
$total_votes = $conn->query("SELECT COUNT(*) as total FROM votes")->fetch_assoc()['total'];

// Get votes per position with winners
$positions = $conn->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM candidates WHERE position_id = p.id) as total_candidates,
           (SELECT COUNT(*) FROM votes WHERE position_id = p.id) as total_votes
    FROM positions p
    WHERE p.is_active = TRUE
    ORDER BY p.title
");

// Get winners for each position
$winners = [];
$position_results = $conn->query("
    SELECT p.title as position_title,
           c.name as candidate_name,
           COUNT(v.id) as vote_count,
           p.max_winners
    FROM positions p
    JOIN candidates c ON p.id = c.position_id
    LEFT JOIN votes v ON c.id = v.candidate_id
    WHERE p.is_active = TRUE
    GROUP BY p.id, c.id
    ORDER BY p.title, vote_count DESC
");

while ($result = $position_results->fetch_assoc()) {
    $position_title = $result['position_title'];
    if (!isset($winners[$position_title])) {
        $winners[$position_title] = [];
    }
    $winners[$position_title][] = $result;
}

// Get all candidates with their votes
$candidates = $conn->query("
    SELECT c.*, p.title as position_title, COUNT(v.id) as vote_count 
    FROM candidates c 
    JOIN positions p ON c.position_id = p.id
    LEFT JOIN votes v ON c.id = v.candidate_id 
    GROUP BY c.id 
    ORDER BY p.title, vote_count DESC
");
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php if (isset($_SESSION['vote_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Your vote has been successfully recorded!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['vote_success']); ?>
            <?php endif; ?>
            
            <h2 class="text-center mb-4">Voting Results</h2>
            
            <?php if ($total_votes === 0): ?>
                <div class="alert alert-info">
                    No votes have been cast yet.
                </div>
            <?php else: ?>
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stats-card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Votes Cast</h5>
                                <p class="display-4"><?php echo $total_votes; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Candidates</h5>
                                <p class="display-4"><?php echo $candidates->num_rows; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stats-card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Positions</h5>
                                <p class="display-4"><?php echo $positions->num_rows; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Winners by Position -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Winners by Position</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($winners as $position_title => $position_candidates): 
                                $position_total_votes = array_sum(array_column($position_candidates, 'vote_count'));
                                $max_winners = $position_candidates[0]['max_winners'];
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0"><?php echo htmlspecialchars($position_title); ?></h5>
                                            <small class="text-muted">Total Votes: <?php echo $position_total_votes; ?></small>
                                        </div>
                                        <div class="card-body">
                                            <?php 
                                            $winner_count = 0;
                                            foreach ($position_candidates as $candidate): 
                                                $percentage = ($position_total_votes > 0) ? 
                                                    ($candidate['vote_count'] / $position_total_votes) * 100 : 0;
                                                $is_winner = $winner_count < $max_winners;
                                            ?>
                                                <div class="mb-3 <?php echo $is_winner ? 'border border-success rounded p-3' : ''; ?>">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0">
                                                            <?php echo htmlspecialchars($candidate['candidate_name']); ?>
                                                            <?php if ($is_winner): ?>
                                                                <span class="badge bg-success ms-2">Winner</span>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <span class="text-muted"><?php echo $candidate['vote_count']; ?> votes</span>
                                                    </div>
                                                    <div class="progress mt-2">
                                                        <div class="progress-bar" 
                                                             role="progressbar" 
                                                             style="width: <?php echo $percentage; ?>%"
                                                             aria-valuenow="<?php echo $percentage; ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            <?php echo number_format($percentage, 1); ?>%
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php 
                                                $winner_count++;
                                            endforeach; 
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Position Votes Chart -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Votes by Position</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="positionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Individual Candidate Results -->
                <div class="card">
                    <div class="card-header">
                        <h4>Detailed Candidate Results</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            $candidates->data_seek(0);
                            while ($candidate = $candidates->fetch_assoc()): 
                                $position_total_votes = $conn->query("
                                    SELECT COUNT(*) as total 
                                    FROM votes 
                                    WHERE position_id = {$candidate['position_id']}
                                ")->fetch_assoc()['total'];
                                $percentage = ($position_total_votes > 0) ? 
                                    ($candidate['vote_count'] / $position_total_votes) * 100 : 0;
                            ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <?php if (!empty($candidate['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($candidate['image_path']); ?>" 
                                                 class="card-img-top candidate-image" 
                                                 alt="<?php echo htmlspecialchars($candidate['name']); ?>">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <?php echo htmlspecialchars($candidate['position_title']); ?>
                                            </h6>
                                            <p class="card-text"><?php echo htmlspecialchars($candidate['bio']); ?></p>
                                            
                                            <div class="mt-3">
                                                <h6>Votes: <?php echo $candidate['vote_count']; ?> 
                                                    (<?php echo number_format($percentage, 1); ?>%)
                                                </h6>
                                                <div class="progress">
                                                    <div class="progress-bar" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $percentage; ?>%"
                                                         aria-valuenow="<?php echo $percentage; ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?php echo number_format($percentage, 1); ?>%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Position Votes Chart
    const positionData = <?php 
        $positions->data_seek(0);
        $chart_data = [];
        while ($position = $positions->fetch_assoc()) {
            $chart_data[] = [
                'label' => $position['title'],
                'value' => $position['total_votes'],
                'candidates' => $position['total_candidates']
            ];
        }
        echo json_encode($chart_data);
    ?>;

    new Chart(document.getElementById('positionChart'), {
        type: 'bar',
        data: {
            labels: positionData.map(item => item.label),
            datasets: [{
                label: 'Total Votes',
                data: positionData.map(item => item.value),
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Number of Candidates',
                data: positionData.map(item => item.candidates),
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Count'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += context.parsed.y;
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php
require_once 'includes/footer.php';
?>
