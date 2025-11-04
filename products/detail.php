<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Get product ID from URL
$product_id = $_GET['id'] ?? '';
$page_title = "Product Details - StepStyle";
$body_class = "product-detail-page";
$additional_css = ['/assets/css/product-detail.css'];

if (empty($product_id)) {
    header('Location: /products/categories.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get product details
    $product_query = "
        SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as category_name, c.slug as category_slug,
               (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as review_count
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id = :id AND p.is_published = TRUE
    ";
    $product_stmt = $db->prepare($product_query);
    $product_stmt->bindParam(':id', $product_id);
    $product_stmt->execute();
    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: /products/categories.php');
        exit;
    }
    
    $page_title = $product['name'] . " - StepStyle";
    
    // Get product images
    $images_query = "
        SELECT * FROM product_images 
        WHERE product_id = :product_id 
        ORDER BY is_primary DESC, sort_order ASC
    ";
    $images_stmt = $db->prepare($images_query);
    $images_stmt->bindParam(':product_id', $product_id);
    $images_stmt->execute();
    $product_images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get product variants
    $variants_query = "
        SELECT * FROM product_variants 
        WHERE product_id = :product_id 
        ORDER BY size, color
    ";
    $variants_stmt = $db->prepare($variants_query);
    $variants_stmt->bindParam(':product_id', $product_id);
    $variants_stmt->execute();
    $product_variants = $variants_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get available sizes and colors
    $sizes = [];
    $colors = [];
    foreach ($product_variants as $variant) {
        if ($variant['size'] && !in_array($variant['size'], $sizes)) {
            $sizes[] = $variant['size'];
        }
        if ($variant['color'] && !in_array($variant['color'], $colors)) {
            $colors[] = $variant['color'];
        }
    }
    
    // Get related products
    $related_query = "
        SELECT p.*, b.name as brand_name,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.category_id = :category_id AND p.id != :product_id AND p.is_published = TRUE
        ORDER BY RAND()
        LIMIT 4
    ";
    $related_stmt = $db->prepare($related_query);
    $related_stmt->bindParam(':category_id', $product['category_id']);
    $related_stmt->bindParam(':product_id', $product_id);
    $related_stmt->execute();
    $related_products = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get product reviews
    $reviews_query = "
        SELECT r.*, u.name as user_name, u.avatar
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.id
        WHERE r.product_id = :product_id AND r.is_approved = TRUE
        ORDER BY r.created_at DESC
        LIMIT 5
    ";
    $reviews_stmt = $db->prepare($reviews_query);
    $reviews_stmt->bindParam(':product_id', $product_id);
    $reviews_stmt->execute();
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Product detail page error: " . $e->getMessage());
    $product = null;
    $product_images = [];
    $product_variants = [];
    $related_products = [];
    $reviews = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../components/header.php'; ?>
</head>
<body class="product-detail-page">
    <?php include '../components/header.php'; ?>

    <!-- Breadcrumb -->
    <section class="breadcrumb-section">
        <div class="container">
            <nav class="breadcrumb">
                <a href="/">Home</a>
                <span class="divider">/</span>
                <a href="/products/categories.php">Categories</a>
                <span class="divider">/</span>
                <a href="/products/category.php?slug=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
                <span class="divider">/</span>
                <a href="/products/brand.php?slug=<?php echo $product['brand_slug']; ?>"><?php echo htmlspecialchars($product['brand_name']); ?></a>
                <span class="divider">/</span>
                <span class="current"><?php echo htmlspecialchars($product['name']); ?></span>
            </nav>
        </div>
    </section>

    <section class="section product-detail-section">
        <div class="container">
            <div class="product-detail-layout">
                <!-- Product Images -->
                <div class="product-images">
                    <div class="main-image">
                        <?php if (!empty($product_images)): ?>
                            <img src="<?php echo $product_images[0]['image_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 id="main-product-image">
                        <?php else: ?>
                            <img src="/assets/images/products/placeholder.jpg" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 id="main-product-image">
                        <?php endif; ?>
                        
                        <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                            <div class="discount-badge">
                                -<?php echo calculateDiscount($product['compare_price'], $product['price']); ?>% OFF
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (count($product_images) > 1): ?>
                    <div class="image-thumbnails">
                        <?php foreach ($product_images as $index => $image): ?>
                            <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 data-image="<?php echo $image['image_url']; ?>">
                                <img src="<?php echo $image['image_url']; ?>" 
                                     alt="<?php echo htmlspecialchars($image['alt_text'] ?? $product['name']); ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div class="product-info">
                    <div class="product-header">
                        <div class="product-meta">
                            <a href="/products/brand.php?slug=<?php echo $product['brand_slug']; ?>" class="product-brand">
                                <?php echo htmlspecialchars($product['brand_name']); ?>
                            </a>
                            <span class="product-sku">SKU: <?php echo $product['sku'] ?? $product['id']; ?></span>
                        </div>
                        
                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        
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
                            <span class="rating-value"><?php echo number_format($rating, 1); ?></span>
                            <span class="rating-count">(<?php echo $product['review_count']; ?> reviews)</span>
                            <a href="#reviews" class="review-link">Write a review</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-pricing">
                        <div class="price-container">
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                <span class="compare-price"><?php echo formatPrice($product['compare_price']); ?></span>
                                <span class="save-amount">You save <?php echo formatPrice($product['compare_price'] - $product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="stock-status">
                            <?php if ($product['quantity'] > 0): ?>
                                <span class="in-stock">
                                    <i class="fas fa-check"></i> In Stock (<?php echo $product['quantity']; ?> available)
                                </span>
                            <?php else: ?>
                                <span class="out-of-stock">
                                    <i class="fas fa-times"></i> Out of Stock
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['short_description'] ?? $product['description']); ?></p>
                    </div>

                    <!-- Product Options -->
                    <form class="product-options-form" id="add-to-cart-form">
                        <?php if (!empty($sizes)): ?>
                        <div class="option-group">
                            <label class="option-label">Size:</label>
                            <div class="size-options">
                                <?php foreach ($sizes as $size): ?>
                                    <label class="size-option">
                                        <input type="radio" name="size" value="<?php echo htmlspecialchars($size); ?>" required>
                                        <span class="size-box"><?php echo htmlspecialchars($size); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($colors)): ?>
                        <div class="option-group">
                            <label class="option-label">Color:</label>
                            <div class="color-options">
                                <?php foreach ($colors as $color): ?>
                                    <label class="color-option">
                                        <input type="radio" name="color" value="<?php echo htmlspecialchars($color); ?>" required>
                                        <span class="color-dot" style="background-color: <?php echo htmlspecialchars($color); ?>"></span>
                                        <span class="color-name"><?php echo htmlspecialchars($color); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="option-group">
                            <label class="option-label">Quantity:</label>
                            <div class="quantity-selector">
                                <button type="button" class="quantity-btn minus">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>" class="quantity-input">
                                <button type="button" class="quantity-btn plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-actions">
                            <?php if ($product['quantity'] > 0): ?>
                                <button type="submit" class="btn btn-primary btn-add-to-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    Add to Cart
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-bell"></i>
                                    Notify When Available
                                </button>
                            <?php endif; ?>
                            
                            <button type="button" class="btn btn-outline btn-wishlist" data-product-id="<?php echo $product['id']; ?>">
                                <i class="far fa-heart"></i>
                                Add to Wishlist
                            </button>
                        </div>
                    </form>

                    <div class="product-features">
                        <div class="feature">
                            <i class="fas fa-shipping-fast"></i>
                            <div>
                                <strong>Free Shipping</strong>
                                <span>On orders over $50</span>
                            </div>
                        </div>
                        <div class="feature">
                            <i class="fas fa-undo"></i>
                            <div>
                                <strong>30-Day Returns</strong>
                                <span>Easy returns policy</span>
                            </div>
                        </div>
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>2-Year Warranty</strong>
                                <span>Manufacturer warranty</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs">
                <div class="tab-headers">
                    <button class="tab-header active" data-tab="description">Description</button>
                    <button class="tab-header" data-tab="specifications">Specifications</button>
                    <button class="tab-header" data-tab="reviews">Reviews (<?php echo $product['review_count']; ?>)</button>
                    <button class="tab-header" data-tab="shipping">Shipping & Returns</button>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active" id="description">
                        <div class="description-content">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </div>
                    </div>

                    <div class="tab-pane" id="specifications">
                        <div class="specifications-table">
                            <div class="spec-row">
                                <div class="spec-label">Brand</div>
                                <div class="spec-value"><?php echo htmlspecialchars($product['brand_name']); ?></div>
                            </div>
                            <div class="spec-row">
                                <div class="spec-label">Category</div>
                                <div class="spec-value"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            </div>
                            <?php if ($product['weight']): ?>
                            <div class="spec-row">
                                <div class="spec-label">Weight</div>
                                <div class="spec-value"><?php echo $product['weight']; ?> kg</div>
                            </div>
                            <?php endif; ?>
                            <?php if ($product['dimensions']): ?>
                            <div class="spec-row">
                                <div class="spec-label">Dimensions</div>
                                <div class="spec-value"><?php echo htmlspecialchars($product['dimensions']); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tab-pane" id="reviews">
                        <div class="reviews-section">
                            <div class="reviews-summary">
                                <div class="average-rating">
                                    <div class="rating-number"><?php echo number_format($product['avg_rating'] ?? 0, 1); ?></div>
                                    <div class="stars">
                                        <?php
                                        $rating = $product['avg_rating'] ?? 0;
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
                                    <div class="rating-count">Based on <?php echo $product['review_count']; ?> reviews</div>
                                </div>
                                
                                <?php if (isLoggedIn()): ?>
                                <button class="btn btn-primary" id="write-review-btn">
                                    <i class="fas fa-pen"></i> Write a Review
                                </button>
                                <?php else: ?>
                                <a href="/auth/login.php" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> Login to Review
                                </a>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($reviews)): ?>
                            <div class="reviews-list">
                                <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-avatar">
                                                <?php if ($review['avatar']): ?>
                                                    <img src="<?php echo $review['avatar']; ?>" alt="<?php echo htmlspecialchars($review['user_name']); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-user"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="reviewer-details">
                                                <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                                <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $review['rating']) {
                                                    echo '<i class="fas fa-star active"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <?php if ($review['title']): ?>
                                            <h4 class="review-title"><?php echo htmlspecialchars($review['title']); ?></h4>
                                        <?php endif; ?>
                                        <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="no-reviews">
                                <p>No reviews yet. Be the first to review this product!</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tab-pane" id="shipping">
                        <div class="shipping-info">
                            <h3>Shipping Information</h3>
                            <ul>
                                <li>Free standard shipping on orders over $50</li>
                                <li>Express shipping available for $9.99</li>
                                <li>Orders processed within 1-2 business days</li>
                                <li>Delivery within 3-7 business days</li>
                            </ul>
                            
                            <h3>Return Policy</h3>
                            <ul>
                                <li>30-day return policy from delivery date</li>
                                <li>Items must be in original condition with tags</li>
                                <li>Free returns for defective items</li>
                                <li>Refund processed within 5-7 business days</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <section class="section related-products-section">
        <div class="container">
            <h2 class="section-title">You Might Also Like</h2>
            <div class="products-grid">
                <?php foreach ($related_products as $related_product): ?>
                <div class="product-card" data-product-id="<?php echo $related_product['id']; ?>">
                    <div class="product-image">
                        <a href="/products/detail.php?id=<?php echo $related_product['id']; ?>" class="product-image-link">
                            <img src="<?php echo $related_product['image_url'] ?? '/assets/images/products/placeholder.jpg'; ?>" 
                                 alt="<?php echo htmlspecialchars($related_product['name']); ?>">
                        </a>
                        
                        <div class="product-actions">
                            <button class="action-btn wishlist-btn" data-product-id="<?php echo $related_product['id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="action-btn quick-view-btn" data-product-id="<?php echo $related_product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-meta">
                            <span class="product-brand"><?php echo htmlspecialchars($related_product['brand_name']); ?></span>
                        </div>
                        
                        <h3 class="product-name">
                            <a href="/products/detail.php?id=<?php echo $related_product['id']; ?>">
                                <?php echo htmlspecialchars($related_product['name']); ?>
                            </a>
                        </h3>
                        
                        <div class="product-price">
                            <span class="current-price"><?php echo formatPrice($related_product['price']); ?></span>
                        </div>
                        
                        <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $related_product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i>
                            Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include '../components/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Image thumbnail selection
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.addEventListener('click', function() {
                const mainImage = document.getElementById('main-product-image');
                const newImage = this.dataset.image;
                
                // Update main image
                mainImage.src = newImage;
                
                // Update active thumbnail
                document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Quantity controls
        const quantityInput = document.querySelector('.quantity-input');
        document.querySelector('.quantity-btn.minus').addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            if (value > 1) {
                quantityInput.value = value - 1;
            }
        });
        
        document.querySelector('.quantity-btn.plus').addEventListener('click', function() {
            let value = parseInt(quantityInput.value);
            const max = parseInt(quantityInput.max);
            if (value < max) {
                quantityInput.value = value + 1;
            }
        });
        
        // Tab functionality
        document.querySelectorAll('.tab-header').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                // Update active tab
                document.querySelectorAll('.tab-header').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show corresponding content
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Add to cart form
        const addToCartForm = document.getElementById('add-to-cart-form');
        addToCartForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const quantity = formData.get('quantity');
            const size = formData.get('size');
            const color = formData.get('color');
            
            // Simulate adding to cart
            if (window.StepStyle) {
                const productCard = document.querySelector('.product-card');
                window.StepStyle.addToCart(productCard);
            }
        });
        
        // Wishlist button
        document.querySelector('.btn-wishlist').addEventListener('click', function() {
            const productId = this.dataset.productId;
            if (window.StepStyle) {
                window.StepStyle.toggleWishlist({ dataset: { productId: productId } });
            }
        });
        
        // Write review button
        document.getElementById('write-review-btn')?.addEventListener('click', function() {
            // Show review modal (to be implemented)
            alert('Review feature coming soon!');
        });
    });
    </script>
</body>
</html>