<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Logout - StepStyle';
$page_description = 'Logout from your StepStyle account.';
$body_class = 'auth-page logout-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Process logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to home page
    header('Location: ../index.php?logout=success');
    exit();
}

// If user is not logged in, redirect to home
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body class="<?php echo $body_class; ?>">

<!-- Loading Screen -->
<div class="loading" id="global-loading">
    <div class="loader-container">
        <div class="loader"></div>
        <p>Loading StepStyle...</p>
    </div>
</div>

<!-- Header -->
<?php include '../components/header.php'; ?>

<!-- Mobile Navigation -->
<?php include '../components/navigation.php'; ?>

<main class="main-content">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="../index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>Logout</span>
        </nav>

        <div class="auth-layout">
            <div class="auth-content">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="logout-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <h1 class="auth-title">Logout</h1>
                        <p class="auth-subtitle">Are you sure you want to logout?</p>
                    </div>

                    <form class="auth-form" method="POST">
                        <div class="logout-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>You will be signed out of your StepStyle account.</p>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline btn-large" onclick="window.history.back()">
                                <i class="fas fa-arrow-left"></i>
                                Cancel
                            </button>
                            <button type="submit" name="confirm_logout" class="btn btn-primary btn-large">
                                <i class="fas fa-sign-out-alt"></i>
                                Yes, Logout
                            </button>
                        </div>
                    </form>

                    <div class="auth-footer">
                        <p>Changed your mind? <a href="../user/profile.php" class="auth-link">Back to profile</a></p>
                    </div>
                </div>
            </div>

            <div class="auth-visual">
                <div class="visual-content">
                    <div class="visual-icon">
                        <i class="fas fa-hand-wave"></i>
                    </div>
                    <h2>See You Soon!</h2>
                    <p>We hope to see you back soon. Don't forget to check out our latest collections and exclusive deals.</p>
                    
                    <div class="quick-links">
                        <h3>Before you go...</h3>
                        <ul>
                            <li><a href="../products/categories.php?filter=new"><i class="fas fa-fire"></i> New Arrivals</a></li>
                            <li><a href="../products/categories.php?filter=sale"><i class="fas fa-percentage"></i> Sale Items</a></li>
                            <li><a href="../about.php"><i class="fas fa-info-circle"></i> About StepStyle</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>

</body>
</html>