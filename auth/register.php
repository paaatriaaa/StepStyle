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
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree_terms = isset($_POST['terms']);
    $newsletter = isset($_POST['newsletter']);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Please fill in all required fields";
    } elseif (!validateEmail($email)) {
        $error = "Please enter a valid email address";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (!$agree_terms) {
        $error = "You must agree to the Terms of Service and Privacy Policy";
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Check if email already exists
            $check_query = "SELECT id FROM users WHERE email = :email";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $error = "An account with this email already exists";
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $verification_token = bin2hex(random_bytes(32));
                
                $insert_query = "
                    INSERT INTO users (name, email, password, verification_token, newsletter_subscribed, created_at) 
                    VALUES (:name, :email, :password, :token, :newsletter, NOW())
                ";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->bindParam(':name', $name);
                $insert_stmt->bindParam(':email', $email);
                $insert_stmt->bindParam(':password', $hashed_password);
                $insert_stmt->bindParam(':token', $verification_token);
                $insert_stmt->bindParam(':newsletter', $newsletter, PDO::PARAM_BOOL);
                
                if ($insert_stmt->execute()) {
                    $user_id = $db->lastInsertId();
                    
                    // Send verification email (in production)
                    // $this->sendVerificationEmail($email, $name, $verification_token);
                    
                    // Log registration
                    $log_query = "INSERT INTO user_activity (user_id, activity_type, ip_address, user_agent) 
                                 VALUES (:user_id, 'register', :ip, :ua)";
                    $log_stmt = $db->prepare($log_query);
                    $log_stmt->bindParam(':user_id', $user_id);
                    $log_stmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
                    $log_stmt->bindParam(':ua', $_SERVER['HTTP_USER_AGENT']);
                    $log_stmt->execute();
                    
                    // For demo purposes, auto-verify and login
                    $update_query = "UPDATE users SET email_verified = TRUE, verification_token = NULL WHERE id = :id";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':id', $user_id);
                    $update_stmt->execute();
                    
                    // Auto login for demo
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['role'] = 'user';
                    $_SESSION['avatar'] = '/assets/images/avatars/default.png';
                    $_SESSION['logged_in'] = true;
                    
                    $_SESSION['success'] = "Welcome to StepStyle, " . htmlspecialchars($name) . "! Your account has been created successfully.";
                    redirect('../index.php');
                    
                } else {
                    $error = "Failed to create account. Please try again.";
                }
            }
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - StepStyle</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
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
                <h1>Create Account</h1>
                <p>Join StepStyle for exclusive benefits and features</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="auth-form" id="register-form">
                <div class="form-group">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                               required placeholder="Enter your full name" autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required placeholder="Enter your email" autocomplete="email">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required 
                               placeholder="Create a password" autocomplete="new-password">
                        <button type="button" class="password-toggle" data-target="password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar">
                            <div class="strength-fill" data-strength="0"></div>
                        </div>
                        <span class="strength-text">Password strength</span>
                    </div>
                    <div class="password-requirements">
                        <small>Must be at least 8 characters long</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               placeholder="Confirm your password" autocomplete="new-password">
                    </div>
                    <div class="password-match" id="password-match" style="display: none;">
                        <small class="match-text"></small>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="terms" id="terms" required>
                        <span class="checkmark"></span>
                        I agree to the <a href="../terms.php" target="_blank">Terms of Service</a> and <a href="../privacy.php" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" name="newsletter" id="newsletter" checked>
                        <span class="checkmark"></span>
                        Subscribe to our newsletter for updates, new arrivals, and exclusive offers
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-auth" id="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </form>

            <div class="auth-divider">
                <span>Or continue with</span>
            </div>

            <div class="social-auth">
                <button type="button" class="btn btn-social btn-google" id="google-register">
                    <i class="fab fa-google"></i>
                    Google
                </button>
                <button type="button" class="btn btn-social btn-facebook" id="facebook-register">
                    <i class="fab fa-facebook-f"></i>
                    Facebook
                </button>
            </div>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
            </div>
        </div>

        <div class="auth-features">
            <div class="feature-card">
                <i class="fas fa-gift"></i>
                <h3>Welcome Offer</h3>
                <p>Get 15% off your first order</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-star"></i>
                <h3>Exclusive Deals</h3>
                <p>Access to members-only sales</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-heart"></i>
                <h3>Wishlist</h3>
                <p>Save your favorite items</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Fast Shipping</h3>
                <p>Free delivery on orders $50+</p>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('register-form');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatch = document.getElementById('password-match');
        const registerBtn = document.getElementById('register-btn');
        
        // Password strength checker
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            const strengthFill = document.querySelector('.strength-fill');
            const strengthText = document.querySelector('.strength-text');
            
            strengthFill.setAttribute('data-strength', strength.score);
            strengthFill.style.width = `${strength.score * 25}%`;
            strengthFill.style.background = strength.color;
            strengthText.textContent = strength.text;
            strengthText.style.color = strength.color;
        });
        
        // Password confirmation check
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (confirmPassword === '') {
                passwordMatch.style.display = 'none';
                return;
            }
            
            passwordMatch.style.display = 'block';
            
            if (password === confirmPassword) {
                passwordMatch.querySelector('.match-text').textContent = '✓ Passwords match';
                passwordMatch.querySelector('.match-text').style.color = '#27ae60';
            } else {
                passwordMatch.querySelector('.match-text').textContent = '✗ Passwords do not match';
                passwordMatch.querySelector('.match-text').style.color = '#e74c3c';
            }
        });
        
        // Form submission handling
        registerForm.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const termsChecked = document.getElementById('terms').checked;
            
            if (!termsChecked) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
            submitBtn.disabled = true;
        });
        
        // Social registration buttons
        document.getElementById('google-register').addEventListener('click', function() {
            alert('Google registration integration would be implemented here');
        });
        
        document.getElementById('facebook-register').addEventListener('click', function() {
            alert('Facebook registration integration would be implemented here');
        });
        
        // Password strength calculation
        function calculatePasswordStrength(password) {
            let score = 0;
            
            // Length check
            if (password.length >= 8) score++;
            if (password.length >= 12) score++;
            
            // Character variety checks
            if (/[a-z]/.test(password)) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^a-zA-Z\d]/.test(password)) score++;
            
            const strengthMap = {
                0: { text: 'Very Weak', color: '#e74c3c' },
                1: { text: 'Weak', color: '#e74c3c' },
                2: { text: 'Fair', color: '#f39c12' },
                3: { text: 'Good', color: '#3498db' },
                4: { text: 'Strong', color: '#27ae60' },
                5: { text: 'Very Strong', color: '#27ae60' },
                6: { text: 'Excellent', color: '#2ecc71' }
            };
            
            return strengthMap[score] || strengthMap[0];
        }
    });
    </script>
</body>
</html>