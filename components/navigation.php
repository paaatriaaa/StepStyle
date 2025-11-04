<!-- Mobile Navigation -->
<div class="mobile-nav">
    <div class="mobile-nav-header">
        <h3>Menu</h3>
        <button class="mobile-nav-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="mobile-nav-content">
        <div class="mobile-nav-section">
            <h4>Main Menu</h4>
            <a href="/" class="mobile-nav-link active">
                <i class="fas fa-home"></i>
                Home
            </a>
            
            <div class="mobile-nav-accordion">
                <button class="mobile-nav-accordion-header">
                    <span>
                        <i class="fas fa-shoe-prints"></i>
                        Sneakers
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="mobile-nav-accordion-content">
                    <a href="products/categories.php?cat=running" class="mobile-nav-sublink">Running Shoes</a>
                    <a href="products/categories.php?cat=basketball" class="mobile-nav-sublink">Basketball Shoes</a>
                    <a href="products/categories.php?cat=lifestyle" class="mobile-nav-sublink">Lifestyle</a>
                    <a href="products/categories.php?cat=skateboarding" class="mobile-nav-sublink">Skateboarding</a>
                </div>
            </div>
            
            <a href="products/categories.php?filter=new" class="mobile-nav-link">
                <i class="fas fa-star"></i>
                New Arrivals
            </a>
            
            <a href="products/categories.php?filter=sale" class="mobile-nav-link">
                <i class="fas fa-fire"></i>
                Sale
                <span class="mobile-badge">50% OFF</span>
            </a>
            
            <a href="products/categories.php" class="mobile-nav-link">
                <i class="fas fa-layer-group"></i>
                Collections
            </a>
            
            <a href="about.php" class="mobile-nav-link">
                <i class="fas fa-info-circle"></i>
                About
            </a>
        </div>
        
        <div class="mobile-nav-section">
            <h4>Account</h4>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/profile.php" class="mobile-nav-link">
                    <i class="fas fa-user"></i>
                    My Profile
                </a>
                <a href="user/cart.php" class="mobile-nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    My Cart
                    <span class="mobile-badge"><?php echo $cart_count; ?></span>
                </a>
                <a href="user/wishlist.php" class="mobile-nav-link">
                    <i class="far fa-heart"></i>
                    Wishlist
                    <span class="mobile-badge"><?php echo $wishlist_count; ?></span>
                </a>
                <a href="auth/logout.php" class="mobile-nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            <?php else: ?>
                <a href="auth/login.php" class="mobile-nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
                <a href="auth/register.php" class="mobile-nav-link">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
            <?php endif; ?>
        </div>
        
        <div class="mobile-nav-section">
            <h4>Support</h4>
            <a href="contact.php" class="mobile-nav-link">
                <i class="fas fa-envelope"></i>
                Contact Us
            </a>
            <a href="shipping.php" class="mobile-nav-link">
                <i class="fas fa-shipping-fast"></i>
                Shipping Info
            </a>
            <a href="returns.php" class="mobile-nav-link">
                <i class="fas fa-undo"></i>
                Returns
            </a>
            <a href="faq.php" class="mobile-nav-link">
                <i class="fas fa-question-circle"></i>
                FAQ
            </a>
        </div>
    </div>
</div>