<?php
// Di bagian atas file header.php, pastikan session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hitung cart dan wishlist count 
$cart_count = 0;
$wishlist_count = 0;
if (isset($_SESSION['user_id'])) {
    $cart_count = getCartItemCount($_SESSION['user_id']);
    $wishlist_count = count(getWishlistItems($_SESSION['user_id']));
}

// Set user name untuk display
$user_name = '';
if (isset($_SESSION['user_first_name'])) {
    $user_name = $_SESSION['user_first_name'];
    if (isset($_SESSION['user_last_name'])) {
        $user_name .= ' ' . $_SESSION['user_last_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StepStyle - Premium Footwear</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- HEADER CSS - PASTIKAN INI DILOAD -->
    <link rel="stylesheet" href="assets/css/header.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>

<header class="header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> +62 (555) 123-STEP</span>
                <span><i class="fas fa-envelope"></i> support@stepstyle.com</span>
            </div>
            <div class="top-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($user_name); ?>!</span>
                    <a href="user/profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <span class="welcome-text">Welcome to StepStyle!</span>
                    <a href="auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
                <a href="track-order.php"><i class="fas fa-shipping-fast"></i> Track Order</a>
                <a href="help.php"><i class="fas fa-question-circle"></i> Help Center</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <div class="header-main">
        <div class="container">
            <div class="header-content">
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>

                <!-- Logo -->
                <a href="index.php" class="logo">
                    <i class="fas fa-shoe-prints"></i>
                    StepStyle
                </a>

                <!-- Search Bar -->
                <div class="search-bar">
                    <form class="search-form" action="products/categories.php" method="GET">
                        <input type="text" placeholder="Search for brands, products..." name="search" id="search-input">
                        <button type="submit">
                            <i class="fas fa-search"></i>
                            <span class="search-text">Search</span>
                        </button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="user-actions">
                    <div class="action-icon wishlist-icon" onclick="window.location.href='user/wishlist.php'">
                        <i class="far fa-heart"></i>
                        <span class="badge wishlist-count"><?php echo $wishlist_count; ?></span>
                    </div>
                    <div class="action-icon cart-icon" onclick="window.location.href='user/cart.php'">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="badge cart-count"><?php echo $cart_count; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="nav-container">
        <div class="container">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a href="products/categories.php" class="nav-link">
                        <i class="fas fa-shoe-prints"></i>
                        Sneakers
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products/categories.php?filter=new" class="nav-link">
                        <i class="fas fa-star"></i>
                        New Arrivals
                    </a>
                </li>
                <li class="nav-item sale-link">
                    <a href="products/categories.php?filter=sale" class="nav-link">
                        <i class="fas fa-fire"></i>
                        Sale
                        <span class="sale-badge">50% OFF</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products/categories.php" class="nav-link">
                        <i class="fas fa-layer-group"></i>
                        Collections
                    </a>
                </li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link">
                        <i class="fas fa-info-circle"></i>
                        About
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</header>