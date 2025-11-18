<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Login - StepStyle';
$page_description = 'Login to your StepStyle account to access exclusive features and personalized shopping experience.';
$body_class = 'auth-page login-page';

// Include configuration and functions
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize variables
$email = $password = '';
$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Authenticate user
        $user = authenticateUser($email, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Set success message
            setFlashMessage('success', 'Welcome back, ' . $user['first_name'] . '!');
            
            // Redirect to intended page or home
            $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '../index.php';
            if (isset($_SESSION['redirect_url'])) {
                unset($_SESSION['redirect_url']);
            }
            
            header('Location: ' . $redirect_url);
            exit();
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
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
            <span>Login</span>
        </nav>

        <div class="auth-layout">
            <div class="auth-content">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h1 class="auth-title">Welcome Back</h1>
                        <p class="auth-subtitle">Sign in to your StepStyle account</p>
                    </div>

                    <!-- Display error messages -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display flash messages -->
                    <?php displayFlashMessage(); ?>

                    <form class="auth-form" method="POST" action="">
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($email); ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" class="form-control" 
                                       placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" id="passwordToggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" name="remember" value="1">
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                            <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-large btn-full">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>
                    </form>

                    <div class="auth-divider">
                        <span>or continue with</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="btn btn-google">
                            <i class="fab fa-google"></i>
                            Google
                        </button>
                        <button type="button" class="btn btn-facebook">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </button>
                    </div>

                    <div class="auth-footer">
                        <p>Don't have an account? <a href="register.php" class="auth-link">Create one here</a></p>
                    </div>
                </div>
            </div>

            <div class="auth-visual">
                <div class="visual-content">
                    <div class="visual-icon">
                        <i class="fas fa-shoe-prints"></i>
                    </div>
                    <h2>Step Into Style</h2>
                    <p>Access your personalized shopping experience, track your orders, and discover exclusive deals tailored just for you.</p>
                    
                    <div class="benefits-list">
                        <h3>Why create an account?</h3>
                        <ul>
                            <li><i class="fas fa-check"></i> Faster checkout process</li>
                            <li><i class="fas fa-check"></i> Track your orders</li>
                            <li><i class="fas fa-check"></i> Save items to wishlist</li>
                            <li><i class="fas fa-check"></i> Exclusive member deals</li>
                            <li><i class="fas fa-check"></i> Personalized recommendations</li>
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
<script>
// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');
    
    if (passwordToggle && passwordInput) {
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
});
</script>

</body>
</html>