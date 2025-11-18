<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Set page metadata
$page_title = 'Create Account - StepStyle';
$page_description = 'Join StepStyle to get access to exclusive deals, fast checkout, and personalized recommendations.';
$body_class = 'auth-page register-page';

// Include configuration and functions
require_once '../config/database.php';
require_once '../config/functions.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Process registration form
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (!$terms) {
        $error = 'Please agree to the Terms of Service and Privacy Policy.';
    } else {
        // Attempt registration using functions
        $user_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
            'newsletter' => $newsletter
        ];
        
        $user_id = registerUser($user_data);
        
        if ($user_id) {
            // Auto login after registration
            $user = getUserById($user_id);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect to success page
                header('Location: ../index.php?registration=success');
                exit();
            } else {
                $error = 'Registration successful but user not found. Please try logging in.';
            }
        } else {
            $error = 'Registration failed. Email may already be registered.';
        }
    }
}

// Pre-fill form values
$first_name_val = htmlspecialchars($_POST['first_name'] ?? '');
$last_name_val = htmlspecialchars($_POST['last_name'] ?? '');
$email_val = htmlspecialchars($_POST['email'] ?? '');
$phone_val = htmlspecialchars($_POST['phone'] ?? '');
$newsletter_checked = isset($_POST['newsletter']) ? 'checked' : 'checked';
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
    
    <style>
        .alert-error {
            background: #fee;
            border: 1px solid #fcc;
            color: #c00;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: #efe;
            border: 1px solid #cfc;
            color: #0c0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .auth-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            min-height: 80vh;
        }
        .auth-content {
            padding: 2rem 0;
        }
        .auth-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        .auth-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-icon {
            position: absolute;
            left: 12px;
            color: #6b7280;
        }
        .input-group input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
        }
        .password-requirements {
            margin-top: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .form-options {
            margin: 1.5rem 0;
        }
        .newsletter-opt, .terms-agree {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .newsletter-opt input, .terms-agree input {
            margin-top: 0.25rem;
        }
        .newsletter-opt label, .terms-agree label {
            font-size: 0.9rem;
            color: #374151;
            line-height: 1.4;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-block {
            width: 100%;
        }
        .btn-large {
            padding: 15px 24px;
            font-size: 1.1rem;
        }
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .auth-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-link:hover {
            text-decoration: underline;
        }
        .auth-visual {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem;
            border-radius: 12px;
            height: fit-content;
        }
        .visual-content {
            text-align: center;
        }
        .visual-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .visual-content h2 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        .visual-content p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        .benefits-list {
            text-align: left;
        }
        .benefit {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .benefit i {
            font-size: 1.5rem;
            margin-top: 0.25rem;
            opacity: 0.9;
        }
        .benefit-content h4 {
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }
        .benefit-content p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            color: #3b82f6;
        }
        .breadcrumb span {
            color: #374151;
            font-weight: 500;
        }
        @media (max-width: 768px) {
            .auth-layout {
                grid-template-columns: 1fr;
            }
            .auth-visual {
                display: none;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                    <div class="alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert-success">
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
                                           value="<?php echo $first_name_val; ?>" 
                                           placeholder="First name" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <div class="input-group">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" id="last_name" name="last_name" 
                                           value="<?php echo $last_name_val; ?>" 
                                           placeholder="Last name" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <div class="input-group">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo $email_val; ?>" 
                                       placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-group">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="phone" name="phone" 
                                       value="<?php echo $phone_val; ?>" 
                                       placeholder="+1 (555) 123-4567">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">Password *</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="password" name="password" 
                                       placeholder="Create a password (min. 6 characters)" 
                                       minlength="6" required>
                                <button type="button" class="password-toggle" id="toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <small>Password must be at least 6 characters long</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm your password" required>
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="newsletter-opt">
                                <input type="checkbox" id="newsletter" name="newsletter" <?php echo $newsletter_checked; ?>>
                                <label for="newsletter">Send me exclusive offers and style tips</label>
                            </div>
                            <div class="terms-agree">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I agree to the <a href="../terms.php" class="auth-link">Terms of Service</a> and <a href="../privacy.php" class="auth-link">Privacy Policy</a></label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-large">
                            <i class="fas fa-user-plus"></i>
                            Create Account
                        </button>

                        <div class="auth-footer">
                            <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
                        </div>
                    </form>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password toggle
    const togglePassword = document.getElementById('toggle-password');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    }

    // Form validation
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const terms = document.getElementById('terms');
            
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                confirmPassword.focus();
                return false;
            }
            
            if (password.value.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                password.focus();
                return false;
            }
            
            if (!terms.checked) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy!');
                terms.focus();
                return false;
            }
        });
    }

    // Hide loading screen
    const loadingScreen = document.getElementById('global-loading');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 1000);
    }
});
</script>

</body>
</html>