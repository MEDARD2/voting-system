<?php
require_once 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1 class="display-4">Welcome to <?php echo SITE_NAME; ?></h1>
            <p class="lead">A secure and user-friendly online voting system</p>
            
            <?php if (!isLoggedIn()): ?>
                <div class="mt-4">
                    <a href="login.php" class="btn btn-primary btn-lg me-3">Login</a>
                    <a href="register.php" class="btn btn-success btn-lg">Register</a>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <a href="vote.php" class="btn btn-primary btn-lg me-3">Cast Your Vote</a>
                    <a href="results.php" class="btn btn-info btn-lg">View Results</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?> 