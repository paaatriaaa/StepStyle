<?php
// Start session and include required files
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Set page metadata
$page_title = 'My Profile - StepStyle';
$page_description = 'Manage your StepStyle account profile, view orders, and update preferences.';
$body_class = 'user-page profile-page';

// Get user data
$user_id = $_SESSION['user_id'];
$user = getUserById($user_id);

if (!$user) {
    session_destroy();
    header('Location: ../auth/login.php');
    exit();
}

// Update session with current user data
$_SESSION['user_first_name'] = $user['first_name'];
$_SESSION['user_last_name'] = $user['last_name'];
$_SESSION['user_email'] = $user['email'];

// Initialize variables
$success_message = '';
$error_message = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    $phone = sanitizeInput($_POST['phone']);
    
    // Validate inputs
    if (empty($first_name) || empty($last_name)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        $update_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone' => $phone
        ];
        
        if (updateUserProfile($user_id, $update_data)) {
            $success_message = 'Profile updated successfully!';
            // Refresh user data
            $user = getUserById($user_id);
            $_SESSION['user_first_name'] = $user['first_name'];
            $_SESSION['user_last_name'] = $user['last_name'];
        } else {
            $error_message = 'Failed to update profile. Please try again.';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill in all password fields.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } else {
        if (changePassword($user_id, $current_password, $new_password)) {
            $success_message = 'Password changed successfully!';
        } else {
            $error_message = 'Current password is incorrect.';
        }
    }
}

// Get user orders
$user_orders = getUserOrders($user_id, 5);

// Get wishlist items
$wishlist_items = getWishlistItems($user_id);
$wishlist_count = count($wishlist_items);

// Get cart count
$cart_count = getCartItemCount($user_id);
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
            <a href="profile.php">My Account</a>
            <i class="fas fa-chevron-right"></i>
            <span>Profile</span>
        </nav>

        <div class="user-layout">
            <!-- Sidebar -->
            <aside class="user-sidebar">
                <div class="user-info-card">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="user-badge">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                    </div>
                </div>

                <nav class="user-menu">
                    <a href="profile.php" class="menu-item active">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="orders.php" class="menu-item">
                        <i class="fas fa-shopping-bag"></i>
                        My Orders
                        <span class="menu-badge"><?php echo count($user_orders); ?></span>
                    </a>
                    <a href="wishlist.php" class="menu-item">
                        <i class="fas fa-heart"></i>
                        Wishlist
                        <span class="menu-badge"><?php echo $wishlist_count; ?></span>
                    </a>
                    <a href="addresses.php" class="menu-item">
                        <i class="fas fa-map-marker-alt"></i>
                        Addresses
                    </a>
                    <a href="settings.php" class="menu-item">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                    <a href="../auth/logout.php" class="menu-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="user-content">
                <!-- Welcome Section -->
                <div class="welcome-section">
                    <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h1>
                    <p>Manage your account settings and view your order history.</p>
                </div>

                <!-- Alert Messages -->
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

                <!-- Quick Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon orders">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo count($user_orders); ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon wishlist">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $wishlist_count; ?></h3>
                            <p>Wishlist Items</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon cart">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $cart_count; ?></h3>
                            <p>Cart Items</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon member">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Member</h3>
                            <p>Account Type</p>
                        </div>
                    </div>
                </div>

                <!-- Profile and Security Sections -->
                <div class="content-grid">
                    <!-- Profile Information -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2><i class="fas fa-user"></i> Profile Information</h2>
                            <p>Update your personal information</p>
                        </div>
                        <form class="profile-form" method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" 
                                           class="form-control" 
                                           value="<?php echo htmlspecialchars($user['first_name']); ?>" 
                                           required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" 
                                           class="form-control" 
                                           value="<?php echo htmlspecialchars($user['last_name']); ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" 
                                       disabled>
                                <small class="form-help">Email cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                       placeholder="Enter your phone number">
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Security Settings -->
                    <div class="content-card">
                        <div class="card-header">
                            <h2><i class="fas fa-shield-alt"></i> Security Settings</h2>
                            <p>Change your password</p>
                        </div>
                        <form class="security-form" method="POST">
                            <div class="form-group">
                                <label for="current_password" class="form-label">Current Password *</label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password" 
                                           class="form-control" 
                                           placeholder="Enter current password" required>
                                    <button type="button" class="password-toggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password" class="form-label">New Password *</label>
                                <div class="input-group">
                                    <input type="password" id="new_password" name="new_password" 
                                           class="form-control" 
                                           placeholder="Enter new password" required>
                                    <button type="button" class="password-toggle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-help">Password must be at least 6 characters long</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="confirm_password" 
                                           class="form-control" 
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
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-clock"></i> Recent Orders</h2>
                        <a href="orders.php" class="view-all-link">View All</a>
                    </div>
                    
                    <?php if (!empty($user_orders)): ?>
                        <div class="orders-list">
                            <?php foreach ($user_orders as $order): ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <div class="order-header">
                                            <h4>Order #<?php echo $order['order_number']; ?></h4>
                                            <span class="order-date"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
                                        </div>
                                        <div class="order-details">
                                            <span class="order-status status-<?php echo strtolower($order['status']); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                            <span class="order-total"><?php echo formatPrice($order['total_amount']); ?></span>
                                        </div>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-outline">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-bag"></i>
                            <h3>No orders yet</h3>
                            <p>Start shopping to see your orders here</p>
                            <a href="../products/categories.php" class="btn btn-primary">
                                Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/user.js"></script>

</body>
</html>