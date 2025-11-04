<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/profile.php');
    exit();
}

// Set page metadata
$page_title = 'Login - StepStyle';
$page_description = 'Login to your StepStyle account to access your profile, orders, and wishlist.';
$body_class = 'auth-page login-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Process login form
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        $user = authenticateUser($email, $password);
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Set remember me cookie if checked
            if (isset($_POST['remember'])) {
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/'); // 30 days
            }
            
            // Redirect to intended page or home
            $redirect_url = $_SESSION['redirect_url'] ?? '../index.php';
            unset($_SESSION['redirect_url']);
            
            header('Location: ' . $redirect_url);
            exit();
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    } catch (Exception $e) {
        $error = 'Login failed. Please try again later.';
    }
}

// Pre-fill email from cookie if exists
$remembered_email = $_COOKIE['user_email'] ?? '';
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
            <span>Login</span>
        </nav>

        <div class="auth-layout">
            <!-- Auth Content -->
            <div class="auth-content">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="auth-title">Welcome Back</h1>
                        <p class="auth-subtitle">Sign in to your StepStyle account</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php
                        switch ($_GET['success']) {
                            case 'registered':
                                echo 'Account created successfully! Please login.';
                                break;
                            case 'logout':
                                echo 'You have been logged out successfully.';
                                break;
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <form class="auth-form" method="POST" id="login-form">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" placeholder="Enter your email" 
                                       value="<?php echo htmlspecialchars($remembered_email); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" id="toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember" <?php echo $remembered_email ? 'checked' : ''; ?>>
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-large">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>

                        <div class="auth-divider">
                            <span>or continue with</span>
                        </div>

                        <div class="social-auth">
                            <button type="button" class="btn btn-social btn-google">
                                <i class="fab fa-google"></i>
                                Google
                            </button>
                            <button type="button" class="btn btn-social btn-facebook">
                                <i class="fab fa-facebook-f"></i>
                                Facebook
                            </button>
                            <button type="button" class="btn btn-social btn-apple">
                                <i class="fab fa-apple"></i>
                                Apple
                            </button>
                        </div>
                    </form>

                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php" class="auth-link">Sign up here</a></p>
                    </div>
                </div>
            </div>

            <!-- Auth Visual -->
            <div class="auth-visual">
                <div class="visual-content">
                    <div class="visual-icon">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <h2>Step Into Style</h2>
                    <p>Access your personalized shopping experience, track orders, and manage your wishlist.</p>
                    
                    <div class="features-list">
                        <div class="feature">
                            <i class="fas fa-bolt"></i>
                            <span>Fast checkout</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-heart"></i>
                            <span>Save favorites</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-truck"></i>
                            <span>Track orders</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-percentage"></i>
                            <span>Exclusive deals</span>
                        </div>
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
<script src="../assets/js/auth.js"></script>

</body>
</html>