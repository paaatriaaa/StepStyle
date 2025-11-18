<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header('Location: ../auth/login.php');
    exit();
}

// Set page metadata
$page_title = 'My Profile - StepStyle';
$page_description = 'Manage your StepStyle account profile, personal information, and preferences.';
$body_class = 'user-page profile-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Get current user data
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: ../auth/login.php');
    exit();
}

// Initialize variables
$success_message = '';
$error_message = '';
$active_tab = $_GET['tab'] ?? 'profile';

// Process profile update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Check if email is already taken by another user
        if ($email !== $user['email']) {
            if (userExists($email)) {
                $error_message = 'This email is already registered to another account.';
            }
        }
        
        if (empty($error_message)) {
            if (updateUserProfile($user['id'], [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'newsletter' => $newsletter
            ])) {
                $success_message = 'Profile updated successfully!';
                $user = getUserById($user['id']); // Refresh user data
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $user['email'];
            } else {
                $error_message = 'Failed to update profile. Please try again.';
            }
        }
    }
}

// Process password change form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill in all password fields.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'New password must be at least 8 characters long.';
    } else {
        if (changePassword($user['id'], $current_password, $new_password)) {
            $success_message = 'Password changed successfully!';
            $active_tab = 'security';
        } else {
            $error_message = 'Current password is incorrect.';
            $active_tab = 'security';
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/user.css">
    
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
            <span>My Profile</span>
        </nav>

        <div class="user-layout">
            <!-- User Sidebar -->
            <aside class="user-sidebar">
                <div class="user-info-card">
                    <div class="user-avatar">
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                        </div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                            <p><?php echo htmlspecialchars($user['email']); ?></p>
                            <span class="user-badge">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <nav class="user-menu">
                    <a href="?tab=profile" class="menu-item <?php echo $active_tab === 'profile' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i>
                        <span>Profile Information</span>
                    </a>
                    <a href="?tab=security" class="menu-item <?php echo $active_tab === 'security' ? 'active' : ''; ?>">
                        <i class="fas fa-lock"></i>
                        <span>Security</span>
                    </a>
                    <a href="orders.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="wishlist.php" class="menu-item">
                        <i class="fas fa-heart"></i>
                        <span>Wishlist</span>
                        <span class="badge">5</span>
                    </a>
                    <a href="addresses.php" class="menu-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Addresses</span>
                    </a>
                    <a href="../auth/logout.php" class="menu-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </nav>
            </aside>

            <!-- User Content -->
            <div class="user-content">
                <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
                <?php endif; ?>

                <!-- Profile Information Tab -->
                <?php if ($active_tab === 'profile'): ?>
                <div class="tab-content active" id="profile-tab">
                    <div class="tab-header">
                        <h2>Profile Information</h2>
                        <p>Update your personal information and preferences</p>
                    </div>

                    <form class="profile-form" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" 
                                       placeholder="First name" required>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" 
                                       placeholder="Last name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   placeholder="Email address" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                   placeholder="+1 (555) 123-4567">
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter" value="1" 
                                       <?php echo $user['newsletter'] ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Subscribe to newsletter for exclusive offers and updates
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                            <a href="?tab=security" class="btn btn-outline">
                                <i class="fas fa-lock"></i>
                                Change Password
                            </a>
                        </div>
                    </form>

                    <div class="account-stats">
                        <h3>Account Overview</h3>
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Total Orders</h4>
                                    <p class="stat-number">12</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Wishlist Items</h4>
                                    <p class="stat-number">5</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Reviews</h4>
                                    <p class="stat-number">8</p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-content">
                                    <h4>Member Since</h4>
                                    <p class="stat-text"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Security Tab -->
                <?php if ($active_tab === 'security'): ?>
                <div class="tab-content active" id="security-tab">
                    <div class="tab-header">
                        <h2>Security Settings</h2>
                        <p>Manage your password and account security</p>
                    </div>

                    <form class="security-form" method="POST">
                        <div class="form-group">
                            <label for="current_password">Current Password *</label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" 
                                       placeholder="Enter current password" required>
                                <button type="button" class="password-toggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password *</label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" 
                                       placeholder="Enter new password" required>
                                <button type="button" class="password-toggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-requirements">
                                <p>Password must be at least 8 characters long</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password *</label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Confirm new password" required>
                                <button type="button" class="password-toggle">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="change_password" class="btn btn-primary">
                                <i class="fas fa-key"></i>
                                Change Password
                            </button>
                            <a href="?tab=profile" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i>
                                Back to Profile
                            </a>
                        </div>
                    </form>

                    <div class="security-features">
                        <h3>Security Features</h3>
                        <div class="feature-list">
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <div class="feature-content">
                                    <h4>Two-Factor Authentication</h4>
                                    <p>Add an extra layer of security to your account</p>
                                    <button class="btn btn-outline btn-small" disabled>Coming Soon</button>
                                </div>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-history"></i>
                                <div class="feature-content">
                                    <h4>Login Activity</h4>
                                    <p>Last login: <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/user.js"></script>

<script>
// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const passwordToggles = document.querySelectorAll('.password-toggle');
    
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>

</body>
</html>