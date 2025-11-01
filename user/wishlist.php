<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    redirect('../auth/login.php');
}

// Demo wishlist data
$wishlist_items = [
    [
        'id' => 1,
        'product_id' => 1,
        'name' => 'Nike Air Max 270 React',
        'brand' => 'Nike',
        'price' => 15990,
        'original_price' => 18990,
        'image' => '../../assets/images/products/nike-air-max-270.jpg',
        'size_range' => 'US 6-12',
        'colors' => ['Black', 'White', 'Red'],
        'rating' => 4.5,
        'review_count' => 128,
        'in_stock' => true
    ],
    [
        'id' => 2,
        'product_id' => 3,
        'name' => 'Puma RS-X Toys',
        'brand' => 'Puma',
        'price' => 11990,
        'original_price' => 14990,
        'image' => '../../assets/images/products/puma-rs-x.jpg',
        'size_range' => 'US 6-11',
        'colors' => ['Multicolor', 'Black'],
        'rating' => 4.2,
        'review_count' => 89,
        'in_stock' => true
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - StepStyle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/wishlist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="wishlist-container">
        <div class="container">
            <div class="wishlist-header">
                <h1>My Wishlist</h1>
                <div class="wishlist-stats">
                    <span class="items-count"><?php echo count($wishlist_items); ?> items</span>
                    <div class="wishlist-actions">
                        <button class="btn btn-outline btn-share-wishlist">
                            <i class="fas fa-share"></i>
                            Share Wishlist
                        </button>
                        <button class="btn btn-outline btn-clear-all">
                            <i class="fas fa-trash"></i>
                            Clear All
                        </button>
                    </div>
                </div>
            </div>

            <?php if (empty($wishlist_items)): ?>
                <div class="empty-wishlist">
                    <div class="empty-icon">
                        <i class="far fa-heart"></i>
                    </div>
                    <h2>Your wishlist is empty</h2>
                    <p>Save your favorite items here for easy access later</p>
                    <a href="../products/categories/sneakers.php" class="btn btn-primary">
                        <i class="fas fa-shoe-prints"></i>
                        Explore Products
                    </a>
                </div>
            <?php else: ?>
                <div class="wishlist-content">
                    <div class="wishlist-items">
                        <?php foreach ($wishlist_items as $item): 
                            $discount = $item['original_price'] ? round((($item['original_price'] - $item['price']) / $item['original_price']) * 100) : 0;
                        ?>
                        <div class="wishlist-item" data-item-id="<?php echo $item['id']; ?>">
                            <div class="item-image">
                                <div class="image-placeholder">
                                    <i class="fas fa-shoe-prints"></i>
                                </div>
                                <?php if($discount > 0): ?>
                                <span class="discount-badge">-<?php echo $discount; ?>%</span>
                                <?php endif; ?>
                                <div class="item-actions">
                                    <button class="btn-remove-wishlist" data-item-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="item-details">
                                <span class="item-brand"><?php echo $item['brand']; ?></span>
                                <h3 class="item-name"><?php echo $item['name']; ?></h3>
                                
                                <div class="item-rating">
                                    <div class="stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= floor($item['rating']) ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-text">(<?php echo $item['review_count']; ?> reviews)</span>
                                </div>

                                <div class="item-options">
                                    <div class="size-options">
                                        <span class="option-label">Size:</span>
                                        <select class="size-select">
                                            <option value="">Select Size</option>
                                            <?php
                                            $sizes = explode('-', str_replace('US ', '', $item['size_range']));
                                            for ($i = $sizes[0]; $i <= $sizes[1]; $i++):
                                            ?>
                                            <option value="<?php echo $i; ?>">US <?php echo $i; ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="color-options">
                                        <span class="option-label">Color:</span>
                                        <div class="color-selectors">
                                            <?php foreach ($item['colors'] as $color): ?>
                                            <label class="color-selector">
                                                <input type="radio" name="color-<?php echo $item['id']; ?>" value="<?php echo $color; ?>">
                                                <span class="color-dot" style="background-color: <?php echo getColorValue($color); ?>" title="<?php echo $color; ?>"></span>
                                            </label>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="item-availability <?php echo $item['in_stock'] ? 'in-stock' : 'out-of-stock'; ?>">
                                    <i class="fas <?php echo $item['in_stock'] ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                    <span><?php echo $item['in_stock'] ? 'In Stock' : 'Out of Stock'; ?></span>
                                </div>
                            </div>

                            <div class="item-pricing">
                                <div class="price-container">
                                    <?php if($item['original_price']): ?>
                                        <span class="current-price"><?php echo formatPrice($item['price']); ?></span>
                                        <span class="original-price"><?php echo formatPrice($item['original_price']); ?></span>
                                    <?php else: ?>
                                        <span class="current-price"><?php echo formatPrice($item['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="item-actions-main">
                                    <button class="btn btn-primary btn-add-to-cart" data-product-id="<?php echo $item['product_id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                    <button class="btn btn-outline btn-move-to-cart" data-product-id="<?php echo $item['product_id']; ?>">
                                        <i class="fas fa-arrow-right"></i>
                                        Move to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="wishlist-sidebar">
                        <div class="sidebar-card">
                            <h3>Wishlist Summary</h3>
                            <div class="summary-stats">
                                <div class="stat">
                                    <span class="stat-value"><?php echo count($wishlist_items); ?></span>
                                    <span class="stat-label">Items</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-value">$<?php echo number_format(array_sum(array_column($wishlist_items, 'price')) / 100, 2); ?></span>
                                    <span class="stat-label">Total Value</span>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-add-all-to-cart">
                                <i class="fas fa-shopping-cart"></i>
                                Add All to Cart
                            </button>
                        </div>

                        <div class="sidebar-card">
                            <h3>Price Drop Alerts</h3>
                            <p>Get notified when items in your wishlist go on sale</p>
                            <button class="btn btn-outline btn-enable-alerts">
                                <i class="fas fa-bell"></i>
                                Enable Alerts
                            </button>
                        </div>

                        <div class="sidebar-card">
                            <h3>Share Your Wishlist</h3>
                            <p>Let friends and family know what you want</p>
                            <div class="share-options">
                                <button class="btn-share">
                                    <i class="fab fa-facebook-f"></i>
                                </button>
                                <button class="btn-share">
                                    <i class="fab fa-twitter"></i>
                                </button>
                                <button class="btn-share">
                                    <i class="fab fa-whatsapp"></i>
                                </button>
                                <button class="btn-share">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="wishlist-recommendations">
                    <h3>You Might Also Like</h3>
                    <div class="recommendation-grid">
                        <!-- Recommended products would go here -->
                        <div class="recommendation-item">
                            <div class="rec-image">
                                <i class="fas fa-shoe-prints"></i>
                            </div>
                            <div class="rec-info">
                                <span class="rec-brand">Adidas</span>
                                <span class="rec-name">Ultraboost 22</span>
                                <span class="rec-price">$199.99</span>
                            </div>
                        </div>
                        <div class="recommendation-item">
                            <div class="rec-image">
                                <i class="fas fa-shoe-prints"></i>
                            </div>
                            <div class="rec-info">
                                <span class="rec-brand">Converse</span>
                                <span class="rec-name">Chuck Taylor All Star</span>
                                <span class="rec-price">$55.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/wishlist.js"></script>
</body>
</html>