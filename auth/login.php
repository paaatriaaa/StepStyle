<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('../index.php');
}

$error = '';
$success = '';

// Handle form submission
if ($_POST) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } elseif (!validateEmail($email)) {
        $error = "Please enter a valid email address";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Check if user exists
            $query = "SELECT id, name, email, password, role, avatar, email_verified FROM users WHERE email = :email AND email_verified = TRUE";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['avatar'] = $user['avatar'];
                    $_SESSION['logged_in'] = true;
                    
                    // Set remember me cookie
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
                        
                        setcookie('remember_token', $token, $expiry, '/');
                        
                        // Store token in database
                        $update_query = "UPDATE users SET remember_token = :token WHERE id = :id";
                        $update_stmt = $db->prepare($update_query);
                        $update_stmt->bindParam(':token', $token);
                        $update_stmt->bindParam(':id', $user['id']);
                        $update_stmt->execute();
                    }
                    
                    // Update last login
                    $update_query = "UPDATE users SET last_login = NOW() WHERE id = :id";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':id', $user['id']);
                    $update_stmt->execute();
                    
                    // Log login activity
                    $log_query = "INSERT INTO user_activity (user_id, activity_type, ip_address, user_agent) 
                                 VALUES (:user_id, 'login', :ip, :ua)";
                    $log_stmt = $db->prepare($log_query);
                    $log_stmt->bindParam(':user_id', $user['id']);
                    $log_stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
                    $log_stmt->bindParam(':ua', $_SERVER['HTTP_USER_AGENT']);
                    $log_stmt->execute();
                    
                    // Redirect to intended page or homepage
                    $redirect_url = isset($_SESSION['redirect_url']) ? $_SESSION['redirect_url'] : '../index.php';
                    unset($_SESSION['redirect_url']);
                    
                    $_SESSION['success'] = "Welcome back, " . htmlspecialchars($user['name']) . "!";
                    redirect($redirect_url);
                    
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "An error occurred. Please try again.";
        }
    }
}

// Demo credentials for testing
$demo_email = "demo@stepstyle.com";
$demo_password = "demo123";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StepStyle</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>
        .demo-credentials {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .demo-credentials h4 {
            margin: 0 0 10px 0;
            color: white;
        }
        .demo-credentials p {
            margin: 5px 0;
            font-family: monospace;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
        }
    </style>
</head>
<body class="auth-page">
    <div class="auth-background">
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="../index.php" class="auth-logo">
                    <i class="fas fa-shoe-prints"></i>
                    StepStyle
                </a>
                <h1>Welcome Back</h1>
                <p>Sign in to your account to continue shopping</p>
            </div>

            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <h4><i class="fas fa-flask"></i> Demo Credentials</h4>
                <p>Email: <?php echo $demo_email; ?></p>
                <p>Password: <?php echo $demo_password; ?></p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="login-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="<?php echo $demo_email; ?>" required 
                               placeholder="Enter your email" autocomplete="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" value="<?php echo $demo_password; ?>" required 
                               placeholder="Enter your password" autocomplete="current-password">
                        <button type="button" class="password-toggle" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="checkmark"></span>
                        Remember me for 30 days
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-auth" id="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div class="auth-divider">
                <span>Or continue with</span>
            </div>

            <div class="social-auth">
                <button type="button" class="btn btn-social btn-google" id="google-login">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button type="button" class="btn btn-social btn-facebook" id="facebook-login">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
            </div>

            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php" class="auth-link">Sign up here</a></p>
            </div>
        </div>

        <div class="auth-features">
            <div class="feature-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Shipping</h3>
                <p>Free delivery on orders over $50</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Payment</h3>
                <p>100% secure payment processing</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-undo"></i>
                <h3>Easy Returns</h3>
                <p>30-day return policy</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('login-form');
        const loginBtn = document.getElementById('login-btn');
        
        // Form submission handling
        loginForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
            submitBtn.disabled = true;
            
            // Re-enable after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
        
        // Social login buttons
        document.getElementById('google-login').addEventListener('click', function() {
            alert('Google login integration would be implemented here');
        });
        
        document.getElementById('facebook-login').addEventListener('click', function() {
            alert('Facebook login integration would be implemented here');
        });
        
        // Auto-fill demo credentials on click
        const demoSection = document.querySelector('.demo-credentials');
        demoSection.addEventListener('click', function() {
            document.getElementById('email').value = '<?php echo $demo_email; ?>';
            document.getElementById('password').value = '<?php echo $demo_password; ?>';
        });
    });
    </script>
</body>
</html>