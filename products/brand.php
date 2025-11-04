<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Get brand slug from URL
$brand_slug = $_GET['slug'] ?? '';
$page_title = "Brand - StepStyle";
$body_class = "brand-page";
$additional_css = ['/assets/css/products.css'];

if (empty($brand_slug)) {
    header('Location: /products/brands.php');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get brand info
    $brand_query = "SELECT * FROM brands WHERE slug = :slug";
    $brand_stmt = $db->prepare($brand_query);
    $brand_stmt->bindParam(':slug', $brand_slug);
    $brand_stmt->execute();
    $brand = $brand_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$brand) {
        header('Location: /products/brands.php');
        exit;
    }
    
    $page_title = $brand['name'] . " - StepStyle";
    
    // Get products for this brand
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = 12;
    $offset = ($page - 1) * $per_page;
    
    // Count total products
    $count_query = "
        SELECT COUNT(*) as total 
        FROM products p 
        WHERE p.brand_id = :brand_id AND p.is_published = TRUE
    ";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->bindParam(':brand_id', $brand['id']);
    $count_stmt->execute();
    $total_products = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get products with pagination
    $products_query = "
        SELECT p.*, 
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url,
               (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as avg_rating,
               (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = TRUE) as review_count
        FROM products p
        WHERE p.brand_id = :brand_id AND p.is_published = TRUE
        ORDER BY p.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    $products_stmt = $db->prepare($products_query);
    $products_stmt->bindParam(':brand_id', $brand['id']);
    $products_stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
    $products_stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $products_stmt->execute();
    $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pagination
    $total_pages = ceil($total_products / $per_page);
    $pagination = paginate($page, $per_page, $total_products);
    
    // Get related brands
    $related_brands_query = "
        SELECT * FROM brands 
        WHERE id != :brand_id AND is_featured = TRUE 
        ORDER BY RAND() 
        LIMIT 4
    ";
    $related_stmt = $db->prepare($related_brands_query);
    $related_stmt->bindParam(':brand_id', $brand['id']);
    $related_stmt->execute();
    $related_brands = $related_stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Brand page error: " . $e->getMessage());
    $brand = null;
    $products = [];
    $total_products = 0;
    $pagination = [];
    $related_brands = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../components/header.php'; ?>
</head>
<body class="brand-page">
    <?php include '../components/header.php'; ?>

    <!-- Brand Hero Section -->
    <section class="brand-hero">
        <div class="container">
            <div class="brand-header">
                <div class="brand-info">
                    <h1><?php echo htmlspecialchars($brand['name']); ?></h1>
                    <p class="brand-description"><?php echo htmlspecialchars($brand['description']); ?></p>
                    
                    <div class="brand-stats">
                        <div class="stat">
                            <div class="stat-number"><?php echo $total_products; ?></div>
                            <div class="stat-label">Products</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Authentic</div>
                        </div>
                        <div class="stat">
                            <div class="stat-number">⭐ 4.8</div>
                            <div class="stat-label">Rating</div>
                        </div>
                    </div>
                </div>
                
                <div class="brand-logo-large">
                    <?php if ($brand['logo']): ?>
                        <img src="<?php echo $brand['logo']; ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>">
                    <?php else: ?>
                        <div class="brand-logo-placeholder">
                            <?php echo strtoupper(substr($brand['name'], 0, 2)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="section products-section">
        <div class="container">
            <div class="products-header">
                <h2><?php echo htmlspecialchars($brand['name']); ?> Collection</h2>
                <div class="products-controls">
                    <div class="view-options">
                        <button class="view-btn grid-view active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn list-view" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    
                    <select class="sort-select" id="sort-products">
                        <option value="newest">Newest First</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="name">Name: A to Z</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-products">
                    <div class="empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <h3>No products found</h3>
                    <p>We couldn't find any products for this brand.</p>
                    <a href="/products/categories.php" class="btn btn-primary">Browse Categories</a>
                </div>
            <?php else: ?>
                <div class="products-grid" id="products-container">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                                <img src="<?php echo $product['image_url'] ?? '/assets/images/products/placeholder.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </a>
                            
                            <div class="product-badges">
                                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                    <div class="badge discount-badge">
                                        -<?php echo calculateDiscount($product['compare_price'], $product['price']); ?>%
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (strtotime($product['created_at']) > strtotime('-7 days')): ?>
                                    <div class="badge new-badge">NEW</div>
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
                                <span class="product-brand"><?php echo htmlspecialchars($brand['name']); ?></span>
                                
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
                                <?php echo htmlspecialchars($product['short_description'] ?? substr($product['description'] ?? '', 0, 100) . '...'); ?>
                            </p>
                            
                            <div class="product-price">
                                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                                    <span class="compare-price"><?php echo formatPrice($product['compare_price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-actions-bottom">
                                <?php if ($product['quantity'] > 0): ?>
                                    <button class="btn btn-primary add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary notify-me-btn" data-product-id="<?php echo $product['id']; ?>" disabled>
                                        <i class="fas fa-bell"></i>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['has_previous']): ?>
                        <a href="?slug=<?php echo $brand_slug; ?>&page=<?php echo $page - 1; ?>" class="pagination-btn">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    <?php endif; ?>
                    
                    <div class="pagination-numbers">
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="pagination-number active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="?slug=<?php echo $brand_slug; ?>&page=<?php echo $i; ?>" class="pagination-number">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="?slug=<?php echo $brand_slug; ?>&page=<?php echo $page + 1; ?>" class="pagination-btn">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Related Brands Section -->
    <?php if (!empty($related_brands)): ?>
    <section class="section related-brands-section">
        <div class="container">
            <h2 class="section-title">Related Brands</h2>
            <div class="brands-grid">
                <?php foreach ($related_brands as $related_brand): ?>
                <div class="brand-card">
                    <div class="brand-logo">
                        <?php echo strtoupper(substr($related_brand['name'], 0, 2)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($related_brand['name']); ?></h3>
                    <a href="/products/brand.php?slug=<?php echo $related_brand['slug']; ?>" class="brand-link">
                        Shop Now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php include '../components/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // View toggle
        const viewButtons = document.querySelectorAll('.view-btn');
        const productsContainer = document.getElementById('products-container');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                
                viewButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                if (view === 'grid') {
                    productsContainer.classList.remove('list-view');
                } else {
                    productsContainer.classList.add('list-view');
                }
            });
        });
        
        // Add to cart functionality
        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                if (window.StepStyle) {
                    window.StepStyle.addToCart(productCard);
                }
            });
        });
        
        // Wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                if (window.StepStyle) {
                    window.StepStyle.toggleWishlist(productCard);
                }
            });
        });
        
        // Sort functionality
        const sortSelect = document.getElementById('sort-products');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                // In a real app, this would reload the page with sort parameter or make AJAX call
                window.location.href = `?slug=<?php echo $brand_slug; ?>&sort=${sortBy}`;
            });
        }
    });
    </script>
</body>
</html>