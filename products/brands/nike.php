<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

$database = new Database();
$db = $database->getConnection();

// Get brand info
$brand_slug = 'nike';
$brand_query = "SELECT * FROM brands WHERE slug = ?";
$brand_stmt = $db->prepare($brand_query);
$brand_stmt->execute([$brand_slug]);
$brand = $brand_stmt->fetch(PDO::FETCH_ASSOC);

// Get products for this brand
$products_query = "SELECT p.*, b.name as brand_name, c.name as category_name 
                   FROM products p 
                   LEFT JOIN brands b ON p.brand_id = b.id 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE b.slug = ? AND p.status = 'active' 
                   ORDER BY p.created_at DESC";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute([$brand_slug]);
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brand['name']; ?> - StepStyle</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <!-- Brand Header -->
    <section class="brand-hero">
        <div class="container">
            <div class="brand-header">
                <div class="brand-info">
                    <h1><?php echo $brand['name']; ?></h1>
                    <p class="brand-description"><?php echo $brand['description']; ?></p>
                    <div class="brand-stats">
                        <div class="stat">
                            <span class="stat-number"><?php echo count($products); ?></span>
                            <span class="stat-label">Products</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">4.8</span>
                            <span class="stat-label">Rating</span>
                        </div>
                        <div class="stat">
                            <span class="stat-number">2015</span>
                            <span class="stat-label">Established</span>
                        </div>
                    </div>
                </div>
                <div class="brand-logo-large">
                    <i class="fas fa-crown"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <div class="products-header">
                <h2><?php echo $brand['name']; ?> Collection</h2>
                <div class="products-controls">
                    <div class="view-options">
                        <button class="view-btn active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <div class="sort-options">
                        <select class="sort-select">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name">Name A-Z</option>
                            <option value="rating">Highest Rated</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="products-filters">
                <div class="filter-group">
                    <h4>Categories</h4>
                    <div class="filter-options">
                        <label class="filter-checkbox">
                            <input type="checkbox" name="category" value="running">
                            <span class="checkmark"></span>
                            Running
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="category" value="sneakers">
                            <span class="checkmark"></span>
                            Sneakers
                        </label>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="category" value="basketball">
                            <span class="checkmark"></span>
                            Basketball
                        </label>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Price Range</h4>
                    <div class="price-range">
                        <input type="range" min="0" max="500" value="500" class="range-slider">
                        <div class="price-values">
                            <span>$0</span>
                            <span>$500</span>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Size</h4>
                    <div class="size-options">
                        <?php 
                        $sizes = ['6', '7', '8', '9', '10', '11', '12'];
                        foreach ($sizes as $size): 
                        ?>
                        <label class="size-option">
                            <input type="checkbox" name="size" value="<?php echo $size; ?>">
                            <span class="size-box"><?php echo $size; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-secondary btn-filter">Apply Filters</button>
                    <button class="btn btn-text btn-clear">Clear All</button>
                </div>
            </div>

            <div class="products-grid" id="products-view">
                <?php foreach ($products as $product): 
                    $images = json_decode($product['images'], true);
                    $main_image = !empty($images) ? $images[0] : '../../assets/images/products/default.jpg';
                    $discount = $product['discount_price'] ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;
                    $colors = json_decode($product['colors'], true) ?: ['Black', 'White'];
                ?>
                <div class="product-card" data-category="<?php echo strtolower($product['category_name']); ?>" data-price="<?php echo $product['discount_price'] ?: $product['price']; ?>">
                    <div class="product-image">
                        <div class="image-placeholder">
                            <i class="fas fa-shoe-prints"></i>
                        </div>
                        <?php if($discount > 0): ?>
                        <span class="discount-badge">-<?php echo $discount; ?>%</span>
                        <?php endif; ?>
                        <div class="product-actions">
                            <button class="wishlist-btn" data-product="<?php echo $product['id']; ?>">
                                <i class="far fa-heart"></i>
                            </button>
                            <button class="quick-view" data-product="<?php echo $product['id']; ?>">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="color-options">
                            <?php foreach (array_slice($colors, 0, 3) as $color): ?>
                            <span class="color-dot" style="background-color: <?php echo getColorValue($color); ?>" title="<?php echo $color; ?>"></span>
                            <?php endforeach; ?>
                            <?php if (count($colors) > 3): ?>
                            <span class="color-more">+<?php echo count($colors) - 3; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="product-brand"><?php echo $product['brand_name']; ?></span>
                        <h3 class="product-name"><?php echo $product['name']; ?></h3>
                        <p class="product-description"><?php echo substr($product['description'], 0, 100); ?>...</p>
                        <div class="product-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= 4 ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count">(128)</span>
                        </div>
                        <div class="product-price">
                            <?php if($product['discount_price']): ?>
                                <span class="current-price"><?php echo formatPrice($product['discount_price']); ?></span>
                                <span class="old-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php else: ?>
                                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <button class="add-to-cart" data-product="<?php echo $product['id']; ?>">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More -->
            <div class="load-more">
                <button class="btn btn-outline btn-load-more">
                    <i class="fas fa-redo"></i>
                    Load More Products
                </button>
            </div>
        </div>
    </section>

    <?php include '../../includes/footer.php'; ?>

    <script src="../../assets/js/products.js"></script>
</body>
</html>

<?php
function getColorValue($color) {
    $colorMap = [
        'Black' => '#000000',
        'White' => '#ffffff',
        'Red' => '#e74c3c',
        'Blue' => '#3498db',
        'Green' => '#27ae60',
        'Gray' => '#95a5a6',
        'Navy' => '#2c3e50',
        'Multicolor' => 'linear-gradient(45deg, #667eea, #f093fb, #f5576c)'
    ];
    return $colorMap[$color] ?? '#cccccc';
}
?>