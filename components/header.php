<?php
// Initialize session and database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $cart_count = 0;
    $wishlist_count = 0;
    
    if (isset($_SESSION['user_id'])) {
        $cart_count = getCartCount($db, $_SESSION['user_id']);
        $wishlist_count = getWishlistCount($db, $_SESSION['user_id']);
    } else {
        $cart_count = getCartCount($db);
    }
} catch (Exception $e) {
    // Log error but don't break the page
    error_log("Header initialization error: " . $e->getMessage());
    $cart_count = 0;
    $wishlist_count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - StepStyle' : 'StepStyle - Premium Footwear'; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : 'Discover the latest sneakers from top brands. Nike, Adidas, Jordan, and more. Free shipping on orders over $100.'; ?>">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title : 'StepStyle - Premium Footwear'; ?>">
    <meta property="og:description" content="<?php echo isset($page_description) ? $page_description : 'Discover the latest sneakers from top brands'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="/assets/images/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body class="<?php echo isset($body_class) ? $body_class : ''; ?>">
    <!-- Loading Screen -->
    <div class="loading" id="global-loading">
        <div class="loader-container">
            <div class="loader"></div>
            <p>Loading StepStyle...</p>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="top-bar">
            <div class="container">
                <div class="contact-info">
                    <span><i class="fas fa-phone"></i> +1 (555) 123-4567</span>
                    <span><i class="fas fa-envelope"></i> support@stepstyle.com</span>
                </div>
                <div class="top-links">
                    <a href="/shipping-info.php"><i class="fas fa-truck"></i> Free Shipping on Orders $50+</a>
                    <a href="/size-guide.php"><i class="fas fa-ruler"></i> Size Guide</a>
                    <?php if (isLoggedIn()): ?>
                        <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
                        <?php if (isAdmin()): ?>
                            <a href="/admin/dashboard.php"><i class="fas fa-cog"></i> Admin</a>
                        <?php endif; ?>
                        <a href="/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="/auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="/auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="main-header">
            <div class="container">
                <div class="header-content">
                    <a href="/" class="logo">
                        <i class="fas fa-shoe-prints"></i>
                        StepStyle
                    </a>
                    
                    <div class="search-bar">
                        <form action="/products/search.php" method="GET" class="search-form">
                            <input type="text" name="q" placeholder="Search for brands, products, categories..." 
                                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                                   aria-label="Search products">
                            <button type="submit" aria-label="Search">
                                <i class="fas fa-search"></i>
                                <span class="search-text">Search</span>
                            </button>
                        </form>
                    </div>
                    
                    <div class="user-actions">
                        <?php if (isLoggedIn()): ?>
                            <div class="action-icon user-profile" onclick="window.location.href='/user/profile.php'" title="My Account">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php else: ?>
                            <div class="action-icon user-profile" onclick="window.location.href='/auth/login.php'" title="Login">
                                <i class="fas fa-user"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="action-icon wishlist-icon" onclick="window.location.href='/user/wishlist.php'" title="Wishlist">
                            <i class="far fa-heart"></i>
                            <?php if ($wishlist_count > 0): ?>
                                <span class="badge wishlist-count"><?php echo $wishlist_count; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="action-icon cart-icon" onclick="window.location.href='/user/cart.php'" title="Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="badge cart-count"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <?php include 'navigation.php'; ?>
    </header>

    <!-- Notifications -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="notification notification-success">
            <div class="notification-content">
                <i class="fas fa-check-circle"></i>
                <span class="notification-message"><?php echo $_SESSION['success']; ?></span>
                <button class="notification-close">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="notification notification-error">
            <div class="notification-content">
                <i class="fas fa-exclamation-circle"></i>
                <span class="notification-message"><?php echo $_SESSION['error']; ?></span>
                <button class="notification-close">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">