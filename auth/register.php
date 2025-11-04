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
$page_title = 'Create Account - StepStyle';
$page_description = 'Join StepStyle to get access to exclusive deals, fast checkout, and personalized recommendations.';
$body_class = 'auth-page register-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Process registration form
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    try {
        // Validate inputs
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            // Check if user already exists
            if (userExists($email)) {
                $error = 'An account with this email already exists.';
            } else {
                // Create new user
                $user_id = registerUser([
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $password,
                    'newsletter' => $newsletter
                ]);
                
                if ($user_id) {
                    // Auto-login after registration
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                    $_SESSION['user_role'] = 'customer';
                    
                    header('Location: ../index.php?registration=success');
                    exit();
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        }
    } catch (Exception $e) {
        $error = 'Registration failed. Please try again later.';
    }
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
    <link href="https://fonts.googleapis/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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
            <span>Create Account</span>
        </nav>

        <div class="auth-layout">
            <!-- Auth Content -->
            <div class="auth-content">
                <div class="auth-card">
                    <div class="auth-header">
                        <h1 class="auth-title">Join StepStyle</h1>
                        <p class="auth-subtitle">Create your account to start shopping</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $success; ?>
                    </div>
                    <?php endif; ?>

                    <form class="auth-form" method="POST" id="register-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <div class="input-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" 
                                           placeholder="First name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <div class="input-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" 
                                           placeholder="Last name" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-group">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                       placeholder="+1 (555) 123-4567">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" placeholder="Create a password" required>
                                <button type="button" class="password-toggle" id="toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" data-strength="0"></div>
                                </div>
                                <span class="strength-text">Password strength</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="newsletter-opt">
                                <input type="checkbox" id="newsletter" name="newsletter" <?php echo isset($_POST['newsletter']) ? 'checked' : 'checked'; ?>>
                                <label for="newsletter">Send me exclusive offers and style tips</label>
                            </div>
                            <div class="terms-agree">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I agree to the <a href="../terms.php">Terms of Service</a> and <a href="../privacy.php">Privacy Policy</a></label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-large">
                            <i class="fas fa-user-plus"></i>
                            Create Account
                        </button>

                        <div class="auth-divider">
                            <span>or sign up with</span>
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
                        <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
                    </div>
                </div>
            </div>

            <!-- Auth Visual -->
            <div class="auth-visual">
                <div class="visual-content">
                    <div class="visual-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h2>Become a Style Insider</h2>
                    <p>Join thousands of sneakerheads and get access to exclusive benefits.</p>
                    
                    <div class="benefits-list">
                        <div class="benefit">
                            <i class="fas fa-rocket"></i>
                            <div class="benefit-content">
                                <h4>Fast Checkout</h4>
                                <p>Save your details for quicker purchases</p>
                            </div>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-gem"></i>
                            <div class="benefit-content">
                                <h4>Early Access</h4>
                                <p>Be the first to get new releases</p>
                            </div>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-percentage"></i>
                            <div class="benefit-content">
                                <h4>Member Discounts</h4>
                                <p>Exclusive deals and promotions</p>
                            </div>
                        </div>
                        <div class="benefit">
                            <i class="fas fa-heart"></i>
                            <div class="benefit-content">
                                <h4>Wishlist</h4>
                                <p>Save and track your favorite items</p>
                            </div>
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