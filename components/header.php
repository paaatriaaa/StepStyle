<?php
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$wishlist_count = isset($_SESSION['wishlist']) ? count($_SESSION['wishlist']) : 0;
?>
<header class="header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="contact-info">
                <span><i class="fas fa-phone"></i> +1 (555) 123-STEP</span>
                <span><i class="fas fa-envelope"></i> support@stepstyle.com</span>
            </div>
            <div class="top-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-text">Welcome, <?php echo $_SESSION['user_name']; ?>!</span>
                    <a href="user/profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <span class="welcome-text">Welcome to StepStyle!</span>
                    <a href="auth/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="auth/register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
                <a href="/track-order.php"><i class="fas fa-shipping-fast"></i> Track Order</a>
                <a href="/help.php"><i class="fas fa-question-circle"></i> Help Center</a>
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
                <a href="/" class="logo">
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
                    
                    <!-- Search Suggestions -->
                    <div class="search-suggestions" id="search-suggestions">
                        <div class="suggestions-header">
                            <h4>Popular Searches</h4>
                        </div>
                        <div class="suggestions-list" id="suggestions-list">
                            <!-- Suggestions will be loaded via AJAX -->
                        </div>
                        <div class="suggestions-footer">
                            <a href="products/categories.php" class="view-all-results">View All Products</a>
                        </div>
                    </div>
                </div>

                <!-- User Actions -->
                <div class="user-actions">
                    <div class="action-icon wishlist-icon" onclick="window.location.href='user/wishlist.php'">
                        <i class="far fa-heart"></i>
                        <span class="badge wishlist-count"><?php echo $wishlist_count; ?></span>
                    </div>
                    <div class="action-icon cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                        <span class="badge cart-count"><?php echo $cart_count; ?></span>
                        
                        <!-- Cart Preview -->
                        <div class="cart-preview">
                            <div class="cart-preview-header">
                                <h4>Your Cart (<?php echo $cart_count; ?>)</h4>
                            </div>
                            <div class="cart-preview-items" id="cart-preview-items">
                                <?php if ($cart_count > 0): ?>
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <div class="cart-preview-item">
                                        <div class="item-info">
                                            <div class="item-name"><?php echo $item['name']; ?></div>
                                            <div class="item-price">$<?php echo $item['price']; ?></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="empty-cart">Your cart is empty</div>
                                <?php endif; ?>
                            </div>
                            <div class="cart-preview-footer">
                                <a href="user/cart.php" class="btn btn-primary btn-block">View Cart</a>
                            </div>
                        </div>
                    </div>
                    <div class="action-icon user-profile">
                        <i class="far fa-user"></i>
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
                    <a href="/" class="nav-link active">
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
                    <div class="dropdown-menu">
                        <div class="dropdown-content">
                            <div class="dropdown-section">
                                <h4>Categories</h4>
                                <div class="category-links">
                                    <a href="products/categories.php?cat=running" class="category-link">
                                        <i class="fas fa-running"></i>
                                        Running Shoes
                                    </a>
                                    <a href="products/categories.php?cat=basketball" class="category-link">
                                        <i class="fas fa-basketball-ball"></i>
                                        Basketball Shoes
                                    </a>
                                    <a href="products/categories.php?cat=lifestyle" class="category-link">
                                        <i class="fas fa-user"></i>
                                        Lifestyle
                                    </a>
                                    <a href="products/categories.php?cat=skateboarding" class="category-link">
                                        <i class="fas fa-skating"></i>
                                        Skateboarding
                                    </a>
                                </div>
                                <a href="products/categories.php" class="view-all">View All Categories</a>
                            </div>
                            <div class="dropdown-section">
                                <h4>Popular Brands</h4>
                                <div class="brands-grid">
                                    <a href="products/brand.php?brand=nike" class="brand-link">
                                        <div class="brand-logo-small" style="background: #000;">
                                            <i class="fas fa-n"></i>
                                        </div>
                                        <span>Nike</span>
                                    </a>
                                    <a href="products/brand.php?brand=adidas" class="brand-link">
                                        <div class="brand-logo-small" style="background: #000;">
                                            <i class="fas fa-a"></i>
                                        </div>
                                        <span>Adidas</span>
                                    </a>
                                    <a href="products/brand.php?brand=jordan" class="brand-link">
                                        <div class="brand-logo-small" style="background: #c90c0f;">
                                            <i class="fas fa-basketball-ball"></i>
                                        </div>
                                        <span>Jordan</span>
                                    </a>
                                    <a href="products/brand.php?brand=puma" class="brand-link">
                                        <div class="brand-logo-small" style="background: #000;">
                                            <i class="fas fa-p"></i>
                                        </div>
                                        <span>Puma</span>
                                    </a>
                                </div>
                                <a href="products/brand.php" class="view-all-brands">View All Brands</a>
                            </div>
                        </div>
                    </div>
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

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay"></div>