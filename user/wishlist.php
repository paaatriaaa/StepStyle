<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'My Wishlist - StepStyle';
$page_description = 'Save your favorite products and create your wishlist. Never miss out on your dream sneakers.';
$body_class = 'wishlist-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Get wishlist items
$user_id = $_SESSION['user_id'] ?? 0;
$wishlist_items = getWishlistItems($user_id);
$wishlist_count = count($wishlist_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/wishlist.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
</head>
<body class="<?php echo $body_class; ?>">

<!-- Loading Screen -->
<div class="loading" id="global-loading">
    <div class="loader-container">
        <div class="loader"></div>
        <p>Loading StepStyle...</p>
    </div>
</div>

<!-- Header -->
<?php include '../components/header.php'; ?>

<!-- Mobile Navigation -->
<?php include '../components/navigation.php'; ?>

<main class="main-content">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="../index.php">Home</a>
            <i class="fas fa-chevron-right"></i>
            <span>My Wishlist</span>
        </nav>

        <div class="wishlist-layout">
            <!-- Wishlist Header -->
            <div class="wishlist-header">
                <div class="header-content">
                    <h1 class="page-title">My Wishlist</h1>
                    <p class="wishlist-subtitle">Save your favorite items for later</p>
                </div>
                <div class="wishlist-actions">
                    <button class="btn btn-outline" id="share-wishlist">
                        <i class="fas fa-share-alt"></i>
                        Share Wishlist
                    </button>
                    <?php if (!empty($wishlist_items)): ?>
                    <button class="btn btn-outline" id="clear-wishlist">
                        <i class="fas fa-trash"></i>
                        Clear All
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Wishlist Content -->
            <div class="wishlist-content">
                <?php if (!empty($wishlist_items)): ?>
                    <div class="wishlist-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $wishlist_count; ?></span>
                            <span class="stat-label">Items</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">$<?php echo number_format(calculateWishlistTotal($wishlist_items), 2); ?></span>
                            <span class="stat-label">Total Value</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count(array_filter($wishlist_items, function($item) { return $item['on_sale']; })); ?></span>
                            <span class="stat-label">On Sale</span>
                        </div>
                    </div>

                    <div class="wishlist-filters">
                        <div class="filter-group">
                            <label for="sort-wishlist">Sort by:</label>
                            <select id="sort-wishlist" class="filter-select">
                                <option value="date-added">Date Added</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="name">Product Name</option>
                                <option value="brand">Brand</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label for="filter-brand">Filter by brand:</label>
                            <select id="filter-brand" class="filter-select">
                                <option value="all">All Brands</option>
                                <option value="nike">Nike</option>
                                <option value="adidas">Adidas</option>
                                <option value="jordan">Jordan</option>
                                <option value="puma">Puma</option>
                                <option value="new-balance">New Balance</option>
                            </select>
                        </div>
                    </div>

                    <div class="wishlist-items">
                        <?php foreach ($wishlist_items as $item): ?>
                        <div class="wishlist-item" data-product-id="<?php echo $item['id']; ?>">
                            <div class="item-image">
                                <a href="../products/detail.php?id=<?php echo $item['id']; ?>">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php else: ?>
                                        <div class="item-image-placeholder">
                                            <i class="fas fa-shoe-prints"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                                <?php if ($item['on_sale']): ?>
                                    <span class="sale-badge">SALE</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-details">
                                <div class="item-info">
                                    <h3 class="item-name">
                                        <a href="../products/detail.php?id=<?php echo $item['id']; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </h3>
                                    <p class="item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                    <div class="item-rating">
                                        <div class="stars">
                                            <?php echo generateStarRating($item['rating']); ?>
                                        </div>
                                        <span class="rating-count">(<?php echo $item['review_count']; ?>)</span>
                                    </div>
                                </div>
                                
                                <div class="item-price">
                                    <span class="current-price">$<?php echo number_format($item['price'], 2); ?></span>
                                    <?php if ($item['original_price'] > $item['price']): ?>
                                        <span class="original-price">$<?php echo number_format($item['original_price'], 2); ?></span>
                                        <span class="discount-percent">
                                            Save <?php echo round((($item['original_price'] - $item['price']) / $item['original_price']) * 100); ?>%
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="stock-status">
                                    <?php if ($item['stock_quantity'] > 0): ?>
                                        <?php if ($item['stock_quantity'] <= 10): ?>
                                            <span class="low-stock">Only <?php echo $item['stock_quantity']; ?> left!</span>
                                        <?php else: ?>
                                            <span class="in-stock">In Stock</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="out-of-stock">Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="item-actions">
                                <?php if ($item['stock_quantity'] > 0): ?>
                                    <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary notify-me" data-product-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-bell"></i>
                                        Notify When Available
                                    </button>
                                <?php endif; ?>
                                
                                <button class="btn btn-outline remove-from-wishlist" data-product-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                    Remove
                                </button>
                                
                                <button class="btn btn-outline share-item" data-product-id="<?php echo $item['id']; ?>">
                                    <i class="fas fa-share"></i>
                                    Share
                                </button>
                            </div>
                            
                            <div class="item-meta">
                                <span class="added-date">Added on <?php echo date('M j, Y', strtotime($item['date_added'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <!-- Empty Wishlist State -->
                    <div class="empty-wishlist">
                        <div class="empty-wishlist-icon">
                            <i class="far fa-heart"></i>
                        </div>
                        <h2>Your wishlist is empty</h2>
                        <p>Start saving your favorite items to keep track of them.</p>
                        <div class="empty-actions">
                            <a href="../products/categories.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i>
                                Explore Products
                            </a>
                            <a href="../products/categories.php?filter=featured" class="btn btn-outline">
                                <i class="fas fa-star"></i>
                                View Featured
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Wishlist Recommendations -->
            <?php if (!empty($wishlist_items)): ?>
            <div class="recommendations-section">
                <h2 class="section-title">Based on Your Wishlist</h2>
                <div class="products-grid">
                    <?php
                    $recommended_products = getWishlistRecommendations(4);
                    foreach ($recommended_products as $product):
                        include '../components/product-card.php';
                    endforeach;
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/wishlist.js"></script>

</body>
</html>