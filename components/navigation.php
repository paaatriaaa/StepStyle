<?php
// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
$categories = [];
$brands = [];

try {
    // Fetch categories for dropdown
    $category_stmt = $db->query("
        SELECT id, name, slug 
        FROM categories 
        WHERE is_active = TRUE 
        ORDER BY name ASC
        LIMIT 8
    ");
    $categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch featured brands for dropdown
    $brand_stmt = $db->query("
        SELECT id, name, slug 
        FROM brands 
        WHERE is_featured = TRUE 
        ORDER BY name ASC
        LIMIT 6
    ");
    $brands = $brand_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Navigation data fetch error: " . $e->getMessage());
}
?>
<nav class="nav-container">
    <div class="container">
        <ul class="nav-menu">
            <li class="nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <a href="/" class="nav-link">
                    <i class="fas fa-home"></i>
                    Home
                </a>
            </li>
            
            <li class="nav-item dropdown">
                <a href="/products/categories.php" class="nav-link dropdown-toggle">
                    <i class="fas fa-th-large"></i>
                    Categories
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-content">
                        <div class="dropdown-section">
                            <h4>Shop by Category</h4>
                            <div class="category-links">
                                <?php foreach ($categories as $category): ?>
                                    <a href="/products/category.php?slug=<?php echo $category['slug']; ?>" class="category-link">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                                <a href="/products/categories.php" class="view-all">View All Categories →</a>
                            </div>
                        </div>
                        <div class="dropdown-section">
                            <h4>Popular Collections</h4>
                            <div class="collection-links">
                                <a href="/products/new-arrivals.php" class="collection-link">
                                    <i class="fas fa-star"></i>
                                    New Arrivals
                                </a>
                                <a href="/products/best-sellers.php" class="collection-link">
                                    <i class="fas fa-fire"></i>
                                    Best Sellers
                                </a>
                                <a href="/products/sale.php" class="collection-link">
                                    <i class="fas fa-tag"></i>
                                    On Sale
                                </a>
                                <a href="/products/featured.php" class="collection-link">
                                    <i class="fas fa-gem"></i>
                                    Featured
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            
            <li class="nav-item dropdown">
                <a href="/products/brands.php" class="nav-link dropdown-toggle">
                    <i class="fas fa-copyright"></i>
                    Brands
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </a>
                <div class="dropdown-menu">
                    <div class="dropdown-content">
                        <div class="brands-grid">
                            <?php foreach ($brands as $brand): ?>
                                <a href="/products/brand.php?slug=<?php echo $brand['slug']; ?>" class="brand-link">
                                    <div class="brand-logo-small"><?php echo strtoupper(substr($brand['name'], 0, 2)); ?></div>
                                    <span><?php echo htmlspecialchars($brand['name']); ?></span>
                                </a>
                            <?php endforeach; ?>
                            <a href="/products/brands.php" class="view-all-brands">All Brands →</a>
                        </div>
                    </div>
                </div>
            </li>
            
            <li class="nav-item <?php echo $current_page === 'new-arrivals.php' ? 'active' : ''; ?>">
                <a href="/products/new-arrivals.php" class="nav-link">
                    <i class="fas fa-bolt"></i>
                    New Arrivals
                </a>
            </li>
            
            <li class="nav-item <?php echo $current_page === 'sale.php' ? 'active' : ''; ?>">
                <a href="/products/sale.php" class="nav-link sale-link">
                    <i class="fas fa-tag"></i>
                    Sale
                    <span class="sale-badge">HOT</span>
                </a>
            </li>
            
            <li class="nav-item <?php echo strpos($current_page, 'men') !== false ? 'active' : ''; ?>">
                <a href="/products/men.php" class="nav-link">
                    <i class="fas fa-male"></i>
                    Men
                </a>
            </li>
            
            <li class="nav-item <?php echo strpos($current_page, 'women') !== false ? 'active' : ''; ?>">
                <a href="/products/women.php" class="nav-link">
                    <i class="fas fa-female"></i>
                    Women
                </a>
            </li>
            
            <li class="nav-item <?php echo strpos($current_page, 'kids') !== false ? 'active' : ''; ?>">
                <a href="/products/kids.php" class="nav-link">
                    <i class="fas fa-child"></i>
                    Kids
                </a>
            </li>
            
            <li class="nav-item <?php echo $current_page === 'blog.php' ? 'active' : ''; ?>">
                <a href="/blog.php" class="nav-link">
                    <i class="fas fa-blog"></i>
                    Blog
                </a>
            </li>
        </ul>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </div>
</nav>

<!-- Mobile Navigation -->
<div class="mobile-nav-overlay"></div>
<nav class="mobile-nav">
    <div class="mobile-nav-header">
        <h3>Menu</h3>
        <button class="mobile-nav-close" aria-label="Close mobile menu">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="mobile-nav-content">
        <div class="mobile-nav-section">
            <a href="/" class="mobile-nav-link">
                <i class="fas fa-home"></i>
                Home
            </a>
            
            <div class="mobile-nav-accordion">
                <button class="mobile-nav-accordion-header">
                    <i class="fas fa-th-large"></i>
                    Categories
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="mobile-nav-accordion-content">
                    <?php foreach ($categories as $category): ?>
                        <a href="/products/category.php?slug=<?php echo $category['slug']; ?>" class="mobile-nav-sublink">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="mobile-nav-accordion">
                <button class="mobile-nav-accordion-header">
                    <i class="fas fa-copyright"></i>
                    Brands
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="mobile-nav-accordion-content">
                    <?php foreach ($brands as $brand): ?>
                        <a href="/products/brand.php?slug=<?php echo $brand['slug']; ?>" class="mobile-nav-sublink">
                            <?php echo htmlspecialchars($brand['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <a href="/products/new-arrivals.php" class="mobile-nav-link">
                <i class="fas fa-bolt"></i>
                New Arrivals
            </a>
            
            <a href="/products/sale.php" class="mobile-nav-link sale-link">
                <i class="fas fa-tag"></i>
                Sale
                <span class="sale-badge">HOT</span>
            </a>
            
            <a href="/products/men.php" class="mobile-nav-link">
                <i class="fas fa-male"></i>
                Men
            </a>
            
            <a href="/products/women.php" class="mobile-nav-link">
                <i class="fas fa-female"></i>
                Women
            </a>
            
            <a href="/products/kids.php" class="mobile-nav-link">
                <i class="fas fa-child"></i>
                Kids
            </a>
            
            <a href="/blog.php" class="mobile-nav-link">
                <i class="fas fa-blog"></i>
                Blog
            </a>
        </div>
        
        <div class="mobile-nav-section">
            <h4>Account</h4>
            <?php if (isLoggedIn()): ?>
                <a href="/user/profile.php" class="mobile-nav-link">
                    <i class="fas fa-user"></i>
                    My Profile
                </a>
                <a href="/user/orders.php" class="mobile-nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    My Orders
                </a>
                <a href="/user/wishlist.php" class="mobile-nav-link">
                    <i class="far fa-heart"></i>
                    Wishlist
                    <?php if ($wishlist_count > 0): ?>
                        <span class="mobile-badge"><?php echo $wishlist_count; ?></span>
                    <?php endif; ?>
                </a>
                <?php if (isAdmin()): ?>
                    <a href="/admin/dashboard.php" class="mobile-nav-link admin-link">
                        <i class="fas fa-cog"></i>
                        Admin Panel
                    </a>
                <?php endif; ?>
                <a href="/auth/logout.php" class="mobile-nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            <?php else: ?>
                <a href="/auth/login.php" class="mobile-nav-link">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
                <a href="/auth/register.php" class="mobile-nav-link">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
            <?php endif; ?>
        </div>
        
        <div class="mobile-nav-section">
            <h4>Support</h4>
            <a href="/help.php" class="mobile-nav-link">
                <i class="fas fa-question-circle"></i>
                Help Center
            </a>
            <a href="/contact.php" class="mobile-nav-link">
                <i class="fas fa-envelope"></i>
                Contact Us
            </a>
            <a href="/shipping-info.php" class="mobile-nav-link">
                <i class="fas fa-truck"></i>
                Shipping Info
            </a>
            <a href="/returns.php" class="mobile-nav-link">
                <i class="fas fa-undo"></i>
                Returns
            </a>
        </div>
    </div>
</nav>