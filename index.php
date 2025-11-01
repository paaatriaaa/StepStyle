<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

// Initialize database
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Initialize database schema if needed
    DatabaseSchema::initialize($db);
    
    // Get featured products
    $featured_query = "
        SELECT p.*, b.name as brand_name, b.slug as brand_slug,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url,
               (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as review_count
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_featured = TRUE AND p.is_published = TRUE
        ORDER BY p.created_at DESC
        LIMIT 8
    ";
    $featured_stmt = $db->prepare($featured_query);
    $featured_stmt->execute();
    $featured_products = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get new arrivals
    $new_arrivals_query = "
        SELECT p.*, b.name as brand_name, b.slug as brand_slug,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_published = TRUE
        ORDER BY p.created_at DESC
        LIMIT 6
    ";
    $new_arrivals_stmt = $db->prepare($new_arrivals_query);
    $new_arrivals_stmt->execute();
    $new_arrivals = $new_arrivals_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get featured brands
    $brands_query = "SELECT * FROM brands WHERE is_featured = TRUE ORDER BY name LIMIT 6";
    $brands_stmt = $db->prepare($brands_query);
    $brands_stmt->execute();
    $featured_brands = $brands_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Homepage initialization error: " . $e->getMessage());
    $featured_products = [];
    $new_arrivals = [];
    $featured_brands = [];
}

// Set page metadata
$page_title = "Premium Footwear Collection";
$page_description = "Discover the latest sneakers from top brands. Nike, Adidas, Jordan, and more. Free shipping on orders over $100.";
$additional_css = ['/assets/css/home.css'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'components/header.php'; ?>
</head>
<body class="home-page">
    <?php include 'components/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Step Into <span class="highlight">Style</span></h1>
                <p>Discover the latest collection of premium sneakers from top brands worldwide. Elevate your footwear game with our exclusive designs and unbeatable prices.</p>
                <div class="hero-buttons">
                    <a href="#new-arrivals" class="btn btn-primary">
                        <i class="fas fa-bolt"></i> Shop New Arrivals
                    </a>
                    <a href="#featured" class="btn btn-secondary">
                        <i class="fas fa-star"></i> View Featured
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="stat">
                        <div class="stat-number">10K+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Premium Products</div>
                    </div>
                    <div class="stat">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Customer Support</div>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="hero-shoe-container">
                    <div class="hero-shoe">👟</div>
                    <div class="shoe-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="section brands-section" id="brands">
        <div class="container">
            <h2 class="section-title">Featured Brands</h2>
            <p class="section-subtitle">Shop from the world's most trusted footwear brands</p>
            
            <div class="brands-grid">
                <?php foreach ($featured_brands as $brand): ?>
                <div class="brand-card" data-brand-slug="<?php echo $brand['slug']; ?>">
                    <div class="brand-logo">
                        <?php echo strtoupper(substr($brand['name'], 0, 2)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($brand['name']); ?></h3>
                    <p class="brand-description"><?php echo htmlspecialchars($brand['description'] ?? 'Premium footwear collection'); ?></p>
                    <a href="/products/brand.php?slug=<?php echo $brand['slug']; ?>" class="brand-link">
                        Shop Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="section-cta">
                <a href="/products/brands.php" class="btn btn-outline">
                    View All Brands <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section class="section new-arrivals-section" id="new-arrivals">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">New Arrivals</h2>
                <p class="section-subtitle">Discover the latest additions to our collection</p>
                <a href="/products/new-arrivals.php" class="view-all-link">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="products-grid">
                <?php foreach ($new_arrivals as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product-image">
                        <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                            <img src="<?php echo $product['image_url'] ?? '/assets/images/products/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        
                        <div class="product-badges">
                            <div class="badge new-badge">NEW</div>
                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <div class="badge discount-badge">
                                    -<?php echo calculateDiscount($product['compare_price'], $product['price']); ?>%
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-meta">
                            <a href="/products/brand.php?slug=<?php echo $product['brand_slug']; ?>" class="product-brand">
                                <?php echo htmlspecialchars($product['brand_name']); ?>
                            </a>
                        </div>
                        
                        <h3 class="product-name">
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        
                        <div class="product-price">
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <span class="compare-price"><?php echo formatPrice($product['compare_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section featured-section" id="featured">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Featured Products</h2>
                <p class="section-subtitle">Curated selection of our most popular items</p>
            </div>
            
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                <div class="product-card featured-product" data-product-id="<?php echo $product['id']; ?>">
                    <div class="product-image">
                        <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                            <img src="<?php echo $product['image_url'] ?? '/assets/images/products/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </a>
                        
                        <div class="product-badges">
                            <div class="badge featured-badge">
                                <i class="fas fa-star"></i> FEATURED
                            </div>
                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <div class="badge discount-badge">
                                    -<?php echo calculateDiscount($product['compare_price'], $product['price']); ?>%
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn quick-view-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-meta">
                            <a href="/products/brand.php?slug=<?php echo $product['brand_slug']; ?>" class="product-brand">
                                <?php echo htmlspecialchars($product['brand_name']); ?>
                            </a>
                            <?php if ($product['avg_rating']): ?>
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php
                                        $rating = $product['avg_rating'];
                                        $fullStars = floor($rating);
                                        $halfStar = ($rating - $fullStars) >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                        
                                        for ($i = 0; $i < $fullStars; $i++) {
                                            echo '<i class="fas fa-star active"></i>';
                                        }
                                        if ($halfStar) {
                                            echo '<i class="fas fa-star-half-alt active"></i>';
                                        }
                                        for ($i = 0; $i < $emptyStars; $i++) {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-count">(<?php echo $product['review_count'] ?? 0; ?>)</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="product-name">
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        
                        <p class="product-description">
                            <?php echo htmlspecialchars($product['short_description'] ?? $product['description'] ?? ''); ?>
                        </p>
                        
                        <div class="product-price">
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <span class="compare-price"><?php echo formatPrice($product['compare_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features-section">
        <div class="container">
            <h2 class="section-title">Why Choose StepStyle?</h2>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Free Shipping</h3>
                    <p>Free delivery on all orders over $50. Fast and reliable shipping to your doorstep.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Easy Returns</h3>
                    <p>30-day return policy. Not satisfied? Return your items for a full refund.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Secure Payment</h3>
                    <p>100% secure payment processing. Your financial information is always protected.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>24/7 Support</h3>
                    <p>Round-the-clock customer service. We're here to help whenever you need us.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3>Quality Guarantee</h3>
                    <p>Premium quality products backed by our satisfaction guarantee.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3>Best Prices</h3>
                    <p>Competitive pricing with regular sales and exclusive member discounts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="section newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>Stay in the Loop</h2>
                    <p>Subscribe to our newsletter and be the first to know about new arrivals, exclusive offers, and style tips.</p>
                </div>
                <div class="newsletter-form">
                    <form class="newsletter-signup" id="home-newsletter-form">
                        <div class="input-group">
                            <input type="email" placeholder="Enter your email address" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Subscribe
                            </button>
                        </div>
                        <div class="form-note">
                            By subscribing, you agree to our <a href="/privacy.php">Privacy Policy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>

    <!-- Homepage Specific Scripts -->
    <script>
    // Homepage specific functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Hero section animations
        const heroElements = document.querySelectorAll('.hero-content > *');
        heroElements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.2}s`;
        });
        
        // Brand cards hover effects
        const brandCards = document.querySelectorAll('.brand-card');
        brandCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.05)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Newsletter form handling
        const newsletterForm = document.getElementById('home-newsletter-form');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = this.querySelector('input[type="email"]').value;
                
                // Simulate subscription
                if (window.StepStyle) {
                    window.StepStyle.showNotification('🎉 Thank you for subscribing!', 'success');
                    this.reset();
                }
            });
        }
    });
    </script>
</body>
</html>