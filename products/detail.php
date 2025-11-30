<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Get product ID from URL
$product_id = $_GET['id'] ?? '';
$page_title = "Product Details - StepStyle";
$body_class = "product-detail-page";

if (empty($product_id)) {
    header('Location: /products/categories.php');
    exit;
}

// Dummy data untuk produk (sesuai dengan data di index.php)
$products_data = [
    1 => [
        'id' => 1,
        'name' => 'Puma MB.05',
        'brand' => 'PUMA',
        'price' => 110.00,
        'original_price' => 0,
        'image_url' => 'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/312131/01/sv02/fnd/PNA/fmt/png/PUMA-x-LAMELO-BALL-MB.05-Voltage-Basketball-Shoes',
        'rating' => 4.5,
        'review_count' => 45,
        'stock_quantity' => 15,
        'featured' => true,
        'on_sale' => false,
        'new_arrival' => false,
        'description' => 'The PUMA MB.05 Voltage Basketball Shoes are designed for elite performance on the court. Featuring advanced cushioning technology and superior traction, these shoes provide the support and comfort needed for intense gameplay. The bold voltage colorway makes a statement both on and off the court.',
        'short_description' => 'Elite basketball shoes with advanced cushioning and superior traction',
        'category' => 'Basketball',
        'sizes' => ['US 8', 'US 9', 'US 10', 'US 11', 'US 12'],
        'colors' => ['Voltage Green', 'Black'],
        'weight' => '0.8',
        'dimensions' => '12 x 8 x 4 inches',
        'features' => ['Advanced Cushioning', 'Superior Traction', 'Breathable Mesh', 'Lightweight Design']
    ],
    2 => [
        'id' => 2,
        'name' => 'Vans Skate Loafer',
        'brand' => 'VANS',
        'price' => 60.00,
        'original_price' => 0,
        'image_url' => 'https://assets.vans.eu/images/t_img/c_fill,g_center,f_auto,h_815,w_652,e_unsharp_mask:100/dpr_2.0/v1753291890/VN0A5DXUBKA-ALT2/Skate-Loafer-Shoes.jpg',
        'rating' => 4.5,
        'review_count' => 203,
        'stock_quantity' => 25,
        'featured' => true,
        'on_sale' => false,
        'new_arrival' => false,
        'description' => 'The Vans Skate Loafer combines classic loafer style with skate functionality. Featuring durable suede construction, padded collars for comfort, and the iconic waffle outsole for superior grip. Perfect for both skating and casual wear.',
        'short_description' => 'Classic loafer style meets skate functionality',
        'category' => 'Skateboarding',
        'sizes' => ['US 7', 'US 8', 'US 9', 'US 10', 'US 11'],
        'colors' => ['Black', 'Brown'],
        'weight' => '0.7',
        'dimensions' => '11 x 7 x 4 inches',
        'features' => ['Durable Suede', 'Waffle Outsole', 'Padded Collar', 'Classic Design']
    ],
    // ... data produk lainnya (3-12) dengan format yang sama ...
];

// Check if product exists
if (!isset($products_data[$product_id])) {
    header('Location: /products/categories.php');
    exit;
}

$product = $products_data[$product_id];
$page_title = $product['name'] . " - StepStyle";

// Get additional images (dummy data)
$product_images = [
    ['image_url' => $product['image_url'], 'alt_text' => $product['name'] . ' - Main View', 'is_primary' => true],
    ['image_url' => $product['image_url'], 'alt_text' => $product['name'] . ' - Side View', 'is_primary' => false],
    ['image_url' => $product['image_url'], 'alt_text' => $product['name'] . ' - Back View', 'is_primary' => false],
    ['image_url' => $product['image_url'], 'alt_text' => $product['name'] . ' - Detail View', 'is_primary' => false]
];

// Get related products (dummy data)
$related_products = [];
foreach ($products_data as $id => $related_product) {
    if ($id != $product_id && $related_product['category'] == $product['category'] && count($related_products) < 4) {
        $related_products[] = $related_product;
    }
}

// Get reviews (dummy data)
$reviews = [
    [
        'user_name' => 'Alex Johnson',
        'avatar' => '',
        'rating' => 5,
        'title' => 'Amazing quality!',
        'comment' => 'These shoes are incredibly comfortable and well-made. Definitely worth the price. The fit is perfect and they look even better in person.',
        'created_at' => '2024-01-15',
        'verified' => true
    ],
    [
        'user_name' => 'Sarah Miller',
        'avatar' => '',
        'rating' => 4,
        'title' => 'Great shoes for daily use',
        'comment' => 'Love the design and comfort. Only wish they had more color options. The cushioning is excellent for all-day wear.',
        'created_at' => '2024-01-10',
        'verified' => true
    ],
    [
        'user_name' => 'Mike Chen',
        'avatar' => '',
        'rating' => 5,
        'title' => 'Perfect for basketball',
        'comment' => 'As a basketball player, these shoes provide excellent support and traction on the court. Highly recommended for serious players.',
        'created_at' => '2024-01-08',
        'verified' => false
    ]
];

// Calculate rating percentages
$rating_stats = [
    '5_star' => 65,
    '4_star' => 25,
    '3_star' => 7,
    '2_star' => 2,
    '1_star' => 1
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/product-detail.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">

    <style>
        /* Additional Styles for Enhanced Product Detail */
        .product-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .image-gallery {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .image-gallery img {
            transition: transform 0.3s ease;
        }

        .image-gallery img:hover {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ff4757;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }

        .floating-cart-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .floating-cart-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .feature-highlights {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }

        .feature-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 1rem;
        }

        .rating-breakdown {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1rem 0;
        }

        .rating-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            margin: 0.5rem 0;
            overflow: hidden;
        }

        .rating-fill {
            height: 100%;
            background: linear-gradient(135deg, #ffd700, #ffa500);
            border-radius: 10px;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .color-option.selected {
            border: 3px solid #667eea;
            transform: scale(1.1);
        }

        .size-option.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .zoom-container {
            position: relative;
            overflow: hidden;
            cursor: zoom-in;
        }

        .zoom-result {
            position: absolute;
            top: 0;
            right: -420px;
            width: 400px;
            height: 400px;
            border: 1px solid #ddd;
            background-repeat: no-repeat;
            display: none;
            z-index: 1000;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        .social-share {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }

        .share-btn {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .share-btn:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="<?php echo $body_class; ?>">

<!-- Loading Screen -->
<div class="loading" id="global-loading">
    <div class="loader-container">
        <div class="loader"></div>
        <p>Loading Product...</p>
    </div>
</div>

<!-- Header -->
<?php include '../components/header.php'; ?>

<!-- Product Hero Section -->
<section class="product-hero">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="hero-subtitle">Premium Quality • Authentic • Free Shipping</p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav class="breadcrumb">
            <a href="/"><i class="fas fa-home"></i> Home</a>
            <span class="divider">/</span>
            <a href="/products/categories.php">Products</a>
            <span class="divider">/</span>
            <a href="/products/categories.php?cat=<?php echo strtolower($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a>
            <span class="divider">/</span>
            <span class="current"><?php echo htmlspecialchars($product['name']); ?></span>
        </nav>
    </div>
</section>

<section class="section product-detail-section">
    <div class="container">
        <div class="product-detail-layout">
            <!-- Product Images with Enhanced Gallery -->
            <div class="product-images">
                <div class="image-gallery">
                    <?php if (!empty($product_images)): ?>
                        <div class="main-image zoom-container">
                            <img src="<?php echo $product_images[0]['image_url']; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 id="main-product-image"
                                 class="zoom-image">
                            <div class="zoom-result" id="zoom-result"></div>
                        </div>
                    <?php else: ?>
                        <img src="/assets/images/products/placeholder.jpg" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             id="main-product-image">
                    <?php endif; ?>
                    
                    <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                        <div class="product-badge pulse-animation">
                            <i class="fas fa-fire"></i> SAVE <?php echo calculateDiscountPercentage($product['original_price'], $product['price']); ?>%
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($product['new_arrival']): ?>
                        <div class="product-badge" style="background: #00b894; top: 60px;">
                            <i class="fas fa-star"></i> NEW ARRIVAL
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

                <!-- Social Share -->
                <div class="social-share">
                    <button class="share-btn" onclick="shareProduct('facebook')">
                        <i class="fab fa-facebook-f"></i> Share
                    </button>
                    <button class="share-btn" onclick="shareProduct('twitter')">
                        <i class="fab fa-twitter"></i> Tweet
                    </button>
                    <button class="share-btn" onclick="shareProduct('pinterest')">
                        <i class="fab fa-pinterest"></i> Pin
                    </button>
                </div>
            </div>

            <!-- Product Info with Sticky Sidebar -->
            <div class="product-info">
                <div class="sticky-sidebar">
                    <div class="product-header">
                        <div class="product-meta">
                            <span class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></span>
                            <span class="product-sku">SKU: STP<?php echo str_pad($product['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        
                        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                        
                        <?php if ($product['rating']): ?>
                        <div class="product-rating">
                            <div class="stars">
                                <?php echo generateStarRating($product['rating']); ?>
                            </div>
                            <span class="rating-value"><?php echo number_format($product['rating'], 1); ?></span>
                            <span class="rating-count">(<?php echo $product['review_count']; ?> reviews)</span>
                            <a href="#reviews" class="review-link">Write a review</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-pricing">
                        <div class="price-container">
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <span class="compare-price"><?php echo formatPrice($product['original_price']); ?></span>
                                <span class="save-amount">You save <?php echo formatPrice($product['original_price'] - $product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="stock-status">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="in-stock">
                                    <i class="fas fa-check"></i> In Stock (<?php echo $product['stock_quantity']; ?> available)
                                </span>
                            <?php else: ?>
                                <span class="out-of-stock">
                                    <i class="fas fa-times"></i> Out of Stock
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="product-description">
                        <p><?php echo htmlspecialchars($product['short_description']); ?></p>
                    </div>

                    <!-- Product Options -->
                    <form class="product-options-form" id="add-to-cart-form">
                        <?php if (!empty($product['sizes'])): ?>
                        <div class="option-group">
                            <label class="option-label">Size:</label>
                            <div class="size-options">
                                <?php foreach ($product['sizes'] as $size): ?>
                                    <label class="size-option">
                                        <input type="radio" name="size" value="<?php echo htmlspecialchars($size); ?>" required>
                                        <span class="size-box"><?php echo htmlspecialchars($size); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($product['colors'])): ?>
                        <div class="option-group">
                            <label class="option-label">Color:</label>
                            <div class="color-options">
                                <?php foreach ($product['colors'] as $color): ?>
                                    <label class="color-option">
                                        <input type="radio" name="color" value="<?php echo htmlspecialchars($color); ?>" required>
                                        <span class="color-dot" style="background-color: <?php echo getColorHex($color); ?>"></span>
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
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="quantity-input">
                                <button type="button" class="quantity-btn plus">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-actions">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button type="submit" class="btn btn-primary btn-add-to-cart pulse-animation">
                                    <i class="fas fa-shopping-cart"></i>
                                    Add to Cart - <?php echo formatPrice($product['price']); ?>
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

                    <!-- Feature Highlights -->
                    <div class="feature-highlights">
                        <div class="feature-card">
                            <i class="fas fa-shipping-fast"></i>
                            <div>
                                <strong>Free Shipping</strong>
                                <span>On orders over $50</span>
                            </div>
                        </div>
                        <div class="feature-card">
                            <i class="fas fa-undo"></i>
                            <div>
                                <strong>30-Day Returns</strong>
                                <span>Easy returns policy</span>
                            </div>
                        </div>
                        <div class="feature-card">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <strong>2-Year Warranty</strong>
                                <span>Manufacturer warranty</span>
                            </div>
                        </div>
                        <div class="feature-card">
                            <i class="fas fa-lock"></i>
                            <div>
                                <strong>Secure Payment</strong>
                                <span>100% protected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Product Tabs -->
        <div class="product-tabs">
            <div class="tab-headers">
                <button class="tab-header active" data-tab="description">
                    <i class="fas fa-file-alt"></i> Description
                </button>
                <button class="tab-header" data-tab="specifications">
                    <i class="fas fa-list"></i> Specifications
                </button>
                <button class="tab-header" data-tab="reviews">
                    <i class="fas fa-star"></i> Reviews (<?php echo $product['review_count']; ?>)
                </button>
                <button class="tab-header" data-tab="shipping">
                    <i class="fas fa-truck"></i> Shipping & Returns
                </button>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="description">
                    <div class="description-content">
                        <h3>Product Overview</h3>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        
                        <?php if (isset($product['features'])): ?>
                        <div class="features-list">
                            <h4>Key Features:</h4>
                            <ul>
                                <?php foreach ($product['features'] as $feature): ?>
                                    <li><i class="fas fa-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="tab-pane" id="specifications">
                    <div class="specifications-table">
                        <div class="spec-row">
                            <div class="spec-label">Brand</div>
                            <div class="spec-value"><?php echo htmlspecialchars($product['brand']); ?></div>
                        </div>
                        <div class="spec-row">
                            <div class="spec-label">Category</div>
                            <div class="spec-value"><?php echo htmlspecialchars($product['category']); ?></div>
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
                                <div class="rating-number"><?php echo number_format($product['rating'] ?? 0, 1); ?></div>
                                <div class="stars">
                                    <?php echo generateStarRating($product['rating']); ?>
                                </div>
                                <div class="rating-count">Based on <?php echo $product['review_count']; ?> reviews</div>
                            </div>
                            
                            <!-- Rating Breakdown -->
                            <div class="rating-breakdown">
                                <?php foreach ($rating_stats as $stars => $percentage): ?>
                                <div class="rating-row">
                                    <span class="star-count"><?php echo str_replace('_', ' ', $stars); ?></span>
                                    <div class="rating-bar">
                                        <div class="rating-fill" style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <span class="percentage"><?php echo $percentage; ?>%</span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (true): // Assume user is logged in for demo ?>
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
                                            <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?>
                                                <?php if ($review['verified']): ?>
                                                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Purchase</span>
                                                <?php endif; ?>
                                            </div>
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
                        <div class="shipping-options">
                            <h3>🚚 Shipping Information</h3>
                            <div class="shipping-card">
                                <i class="fas fa-shipping-fast"></i>
                                <div>
                                    <strong>Standard Shipping</strong>
                                    <span>Free on orders over $50 • 3-7 business days</span>
                                </div>
                            </div>
                            <div class="shipping-card">
                                <i class="fas fa-rocket"></i>
                                <div>
                                    <strong>Express Shipping</strong>
                                    <span>$9.99 • 1-2 business days</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="return-policy">
                            <h3>🔄 Return Policy</h3>
                            <ul>
                                <li><i class="fas fa-check"></i> 30-day return policy from delivery date</li>
                                <li><i class="fas fa-check"></i> Items must be in original condition with tags</li>
                                <li><i class="fas fa-check"></i> Free returns for defective items</li>
                                <li><i class="fas fa-check"></i> Refund processed within 5-7 business days</li>
                            </ul>
                        </div>
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
        <h2 class="section-title">You Might Also Like 🔥</h2>
        <p class="section-subtitle">Discover similar products that match your style</p>
        <div class="products-grid">
            <?php foreach ($related_products as $related_product): ?>
            <div class="product-card">
                <div class="product-image">
                    <a href="/products/detail.php?id=<?php echo $related_product['id']; ?>" class="product-image-link">
                        <img src="<?php echo $related_product['image_url']; ?>" 
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
                    
                    <?php if ($related_product['original_price'] && $related_product['original_price'] > $related_product['price']): ?>
                        <div class="discount-badge">
                            -<?php echo calculateDiscountPercentage($related_product['original_price'], $related_product['price']); ?>%
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-info">
                    <div class="product-meta">
                        <span class="product-brand"><?php echo htmlspecialchars($related_product['brand']); ?></span>
                    </div>
                    
                    <h3 class="product-name">
                        <a href="/products/detail.php?id=<?php echo $related_product['id']; ?>">
                            <?php echo htmlspecialchars($related_product['name']); ?>
                        </a>
                    </h3>
                    
                    <div class="product-price">
                        <span class="current-price"><?php echo formatPrice($related_product['price']); ?></span>
                        <?php if ($related_product['original_price'] && $related_product['original_price'] > $related_product['price']): ?>
                            <span class="compare-price"><?php echo formatPrice($related_product['original_price']); ?></span>
                        <?php endif; ?>
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

<!-- Floating Add to Cart Button -->
<button class="floating-cart-btn" onclick="document.getElementById('add-to-cart-form').requestSubmit()">
    <i class="fas fa-shopping-cart"></i>
    Add to Cart - <?php echo formatPrice($product['price']); ?>
</button>

<?php include '../components/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced Image Gallery
    const mainImage = document.getElementById('main-product-image');
    const thumbnails = document.querySelectorAll('.thumbnail');
    const zoomResult = document.getElementById('zoom-result');

    // Thumbnail selection
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const newImage = this.dataset.image;
            
            // Update main image
            mainImage.src = newImage;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Image zoom functionality
    if (mainImage && zoomResult) {
        mainImage.addEventListener('mousemove', function(e) {
            zoomResult.style.display = 'block';
            const zoom = 2;
            const {left, top, width, height} = this.getBoundingClientRect();
            const x = ((e.pageX - left) / width) * 100;
            const y = ((e.pageY - top) / height) * 100;
            zoomResult.style.backgroundImage = `url('${this.src}')`;
            zoomResult.style.backgroundSize = `${width * zoom}px ${height * zoom}px`;
            zoomResult.style.backgroundPosition = `${x}% ${y}%`;
        });

        mainImage.addEventListener('mouseleave', function() {
            zoomResult.style.display = 'none';
        });
    }

    // Enhanced quantity controls
    const quantityInput = document.querySelector('.quantity-input');
    const minusBtn = document.querySelector('.quantity-btn.minus');
    const plusBtn = document.querySelector('.quantity-btn.plus');

    minusBtn?.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        if (value > 1) {
            quantityInput.value = value - 1;
            updateFloatingButton();
        }
    });
    
    plusBtn?.addEventListener('click', function() {
        let value = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.max);
        if (value < max) {
            quantityInput.value = value + 1;
            updateFloatingButton();
        }
    });

    // Size and color selection with visual feedback
    document.querySelectorAll('.size-option, .color-option').forEach(option => {
        option.addEventListener('click', function() {
            const input = this.querySelector('input');
            input.checked = true;
            
            // Update active state with animation
            if (this.classList.contains('size-option')) {
                document.querySelectorAll('.size-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.style.transform = 'scale(1)';
                });
            } else {
                document.querySelectorAll('.color-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.style.transform = 'scale(1)';
                });
            }
            
            this.classList.add('selected');
            this.style.transform = 'scale(1.1)';
        });
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
    
    // Add to cart form with enhanced feedback
    const addToCartForm = document.getElementById('add-to-cart-form');
    addToCartForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const quantity = formData.get('quantity');
        const size = formData.get('size');
        const color = formData.get('color');
        
        // Show enhanced success message
        showNotification(`🎉 Added to cart! ${quantity} x ${size} ${color}`, 'success');
        
        // Update cart count in header
        updateCartCount();
        
        // Add animation to cart button
        const cartBtn = document.querySelector('.btn-add-to-cart');
        cartBtn.innerHTML = '<i class="fas fa-check"></i> Added to Cart!';
        cartBtn.style.background = '#00b894';
        
        setTimeout(() => {
            cartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart - <?php echo formatPrice($product['price']); ?>';
            cartBtn.style.background = '';
        }, 2000);
    });
    
    // Wishlist button with enhanced feedback
    document.querySelector('.btn-wishlist').addEventListener('click', function() {
        const productId = this.dataset.productId;
        const isActive = this.classList.contains('active');
        
        this.classList.toggle('active');
        this.querySelector('i').classList.toggle('far');
        this.querySelector('i').classList.toggle('fas');
        
        if (!isActive) {
            showNotification('❤️ Added to wishlist!', 'success');
            this.style.background = '#ff4757';
            this.style.color = 'white';
        } else {
            showNotification('💔 Removed from wishlist', 'info');
            this.style.background = '';
            this.style.color = '';
        }
    });
    
    // Write review button
    document.getElementById('write-review-btn')?.addEventListener('click', function() {
        showNotification('📝 Review feature coming soon!', 'info');
    });

    // Update floating button price
    function updateFloatingButton() {
        const quantity = parseInt(quantityInput.value);
        const price = <?php echo $product['price']; ?>;
        const total = quantity * price;
        const floatingBtn = document.querySelector('.floating-cart-btn');
        floatingBtn.innerHTML = `<i class="fas fa-shopping-cart"></i> Add to Cart - $${total.toFixed(2)}`;
    }

    // Notification system
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check' : 'info'}-circle"></i>
                ${message}
            </div>
        `;
        
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            background: type === 'success' ? '#00b894' : '#3742fa',
            color: 'white',
            padding: '15px 20px',
            borderRadius: '10px',
            boxShadow: '0 5px 15px rgba(0,0,0,0.2)',
            zIndex: '10000',
            transform: 'translateX(400px)',
            transition: 'transform 0.3s ease'
        });
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Simulate cart count update
    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const currentCount = parseInt(cartCount.textContent) || 0;
            cartCount.textContent = currentCount + 1;
            cartCount.classList.add('pulse-animation');
            setTimeout(() => cartCount.classList.remove('pulse-animation'), 600);
        }
    }
});

// Social share functions
function shareProduct(platform) {
    const productName = '<?php echo addslashes($product['name']); ?>';
    const productUrl = window.location.href;
    
    let shareUrl = '';
    switch(platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(productName)}&url=${encodeURIComponent(productUrl)}`;
            break;
        case 'pinterest':
            shareUrl = `https://pinterest.com/pin/create/button/?url=${encodeURIComponent(productUrl)}&description=${encodeURIComponent(productName)}`;
            break;
    }
    
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

// Helper function untuk color hex
function getColorHex(colorName) {
    const colorMap = {
        'Black': '#000000',
        'White': '#FFFFFF',
        'Red': '#FF0000',
        'Blue': '#0000FF',
        'Green': '#008000',
        'Voltage Green': '#00FF00',
        'Brown': '#8B4513',
        'Maroon': '#800000',
        'Cream': '#FFFDD0',
        'Silver': '#C0C0C0',
        'Khaki': '#F0E68C',
        'Pink': '#FFC0CB',
        'Hyper Royal': '#4169E1'
    };
    return colorMap[colorName] || '#CCCCCC';
}
</script>

</body>
</html>