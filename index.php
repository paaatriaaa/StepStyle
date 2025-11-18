<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'StepStyle - Premium Footwear & Sneakers';
$page_description = 'Discover the latest sneakers from top brands. Nike, Adidas, Jordan, and more. Free shipping on orders over $100.';
$body_class = 'home-page';

// Include configuration
require_once 'config/database.php';
require_once 'config/functions.php';

// Get featured products (dummy data untuk contoh)
$featured_products = [
    [
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
        'new_arrival' => false
    ],
    [
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
        'new_arrival' => false
    ],
    [
        'id' => 3,
        'name' => 'Converse Chuck 70',
        'brand' => 'CONVERSE',
        'price' => 85.00,
        'original_price' => 100.00,
        'image_url' => 'https://clothbase.s3.amazonaws.com/uploads/10c6f920-e854-4bc8-90c3-c2d86817751b/image.jpg',
        'rating' => 4.5,
        'review_count' => 156,
        'stock_quantity' => 3,
        'featured' => true,
        'on_sale' => true,
        'new_arrival' => false
    ],
    [
        'id' => 4,
        'name' => 'Reebok Court Advance',
        'brand' => 'REEBOK',
        'price' => 75.00,
        'original_price' => 0,
        'image_url' => 'https://reebokbr.vtexassets.com/arquivos/ids/161812/HR1485--1-.jpg?v=638115718439370000',
        'rating' => 4.5,
        'review_count' => 89,
        'stock_quantity' => 12,
        'featured' => true,
        'on_sale' => false,
        'new_arrival' => false
    ],
    [
        'id' => 5,
        'name' => 'Nike Alphafly 3',
        'brand' => 'NIKE',
        'price' => 150.00,
        'original_price' => 0,
        'image_url' => 'https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco/50484187-18b3-4373-8118-8ea0f0f37093/AIR+ZOOM+ALPHAFLY+NEXT%25+3+PRM.png',
        'rating' => 4.8,
        'review_count' => 312,
        'stock_quantity' => 8,
        'featured' => true,
        'on_sale' => false,
        'new_arrival' => false
    ],
    [
        'id' => 6,
        'name' => 'Adidas Adizero Boston 13',
        'brand' => 'ADIDAS',
        'price' => 180.00,
        'original_price' => 200.00,
        'image_url' => 'https://brand.assets.adidas.com/image/upload/f_auto,q_auto:best,fl_lossy/global_adizero_boston_eqt_13_running_fw25_launch_pdp_banner_split_3_d_70070e441e.jpg',
        'rating' => 4.9,
        'review_count' => 267,
        'stock_quantity' => 15,
        'featured' => true,
        'on_sale' => true,
        'new_arrival' => false
    ],
    [
        'id' => 7,
        'name' => 'New Balance 530',
        'brand' => 'NEW BALANCE',
        'price' => 80.00,
        'original_price' => 0,
        'image_url' => 'https://sneakerpeeker.es/hpeciai/596ef3ad9f37e0f8df4dbce283d8c17f/spa_pl_New-Balance-530-MR530SG-17383_1.jpg',
        'rating' => 4.4,
        'review_count' => 178,
        'stock_quantity' => 22,
        'featured' => true,
        'on_sale' => false,
        'new_arrival' => false
    ],
    [
        'id' => 8,
        'name' => 'Jordan 1 Retro',
        'brand' => 'JORDAN',
        'price' => 170.00,
        'original_price' => 190.00,
        'image_url' => 'https://sneakernews.com/wp-content/uploads/2021/01/air-jordan-1-retro-high-og-hyper-royal-555088-402-release-date-7.jpg',
        'rating' => 4.7,
        'review_count' => 445,
        'stock_quantity' => 5,
        'featured' => true,
        'on_sale' => true,
        'new_arrival' => false
    ]
];

$new_arrivals = [
    [
        'id' => 9,
        'name' => 'Nike Dunk Low',
        'brand' => 'NIKE',
        'price' => 110.00,
        'original_price' => 0,
        'image_url' => 'https://sneakerbardetroit.com/wp-content/uploads/2023/05/Nike-Dunk-Low-White-Oil-Green-Cargo-Khaki-FN6882-100.jpeg',
        'rating' => 4.6,
        'review_count' => 89,
        'stock_quantity' => 12,
        'featured' => false,
        'on_sale' => false,
        'new_arrival' => true
    ],
    [
        'id' => 10,
        'name' => 'Adidas Samba OG',
        'brand' => 'ADIDAS',
        'price' => 130.00,
        'original_price' => 150.00,
        'image_url' => 'https://www.consortium.co.uk/media/catalog/product/cache/1/image/040ec09b1e35df139433887a97daa66f/a/d/adidas-originals-samba-og-maroon-cream-white-gold-metallic-id0477_0006_6.jpg',
        'rating' => 4.5,
        'review_count' => 67,
        'stock_quantity' => 18,
        'featured' => false,
        'on_sale' => true,
        'new_arrival' => true
    ],
    [
        'id' => 11,
        'name' => 'Puma Speedcat',
        'brand' => 'PUMA',
        'price' => 75.00,
        'original_price' => 0,
        'image_url' => 'https://cdn02.plentymarkets.com/y556ywtxgskt/item/images/9559/full/puma--pum-339844-05--20.jpg',
        'rating' => 4.3,
        'review_count' => 34,
        'stock_quantity' => 25,
        'featured' => false,
        'on_sale' => false,
        'new_arrival' => true
    ],
    [
        'id' => 12,
        'name' => 'Converse Run Star Trainer',
        'brand' => 'CONVERSE',
        'price' => 110.00,
        'original_price' => 0,
        'image_url' => 'https://www.converse.com/dw/image/v2/BJJF_PRD/on/demandware.static/-/Sites-cnv-master-catalog-we/default/dw40d067f2/images/g_08/A10449C_G_08X1.jpg',
        'rating' => 4.7,
        'review_count' => 56,
        'stock_quantity' => 7,
        'featured' => false,
        'on_sale' => false,
        'new_arrival' => true
    ]
];

$top_brands = [
    [
        'name' => 'NIKE', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/nike-logo-black-and-white.png',
        'color' => '#000000'
    ],
    [
        'name' => 'ADIDAS', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/adidas-logo-black.png',
        'color' => '#000000'
    ],
    [
        'name' => 'JORDAN', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/jordan-logo-black.png',
        'color' => '#000000'
    ],
    [
        'name' => 'PUMA', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/puma-logo-black.png',
        'color' => '#000000'
    ],
    [
        'name' => 'NEW BALANCE', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/new-balance-logo-black.png',
        'color' => '#000000'
    ],
    [
        'name' => 'CONVERSE', 
        'logo_url' => 'https://cdn.freebiesupply.com/images/large/2x/converse-logo-black.png',
        'color' => '#000000'
    ]
];

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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
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
<?php include 'components/header.php'; ?>

<!-- Mobile Navigation -->
<?php include 'components/navigation.php'; ?>

<main class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">🔥 New Collection 2024</span>
                    <h1 class="hero-title">Step Into <span class="highlight">Style</span> With Premium Sneakers</h1>
                    <p class="hero-description">
                        Discover the latest trends in footwear from top brands. 
                        Limited editions, exclusive drops, and timeless classics all in one place.
                    </p>
                    <div class="hero-actions">
                        <a href="products/categories.php?filter=new" class="btn btn-primary pulse">
                            <i class="fas fa-bolt"></i>
                            Shop New Arrivals
                        </a>
                        <a href="products/categories.php?filter=sale" class="btn btn-secondary">
                            <i class="fas fa-percentage"></i>
                            View Sale (50% OFF)
                        </a>
                    </div>
                    <div class="hero-features">
                        <div class="feature">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Free Shipping $50+</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-undo"></i>
                            <span>30-Day Returns</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Authentic</span>
                        </div>
                    </div>
                </div>
<!-- Di bagian Hero Visual, ganti dengan: -->
<div class="hero-visual">
    <div class="sneaker-showcase">
        <div class="hero-sneaker">
            <img src="https://i.pinimg.com/originals/01/bd/29/01bd297dae05b4b2a82639e43a02a4e2.png" alt="Premium Sneakers" class="hero-sneaker-image">
        </div>
        <div class="sneaker-details">
            <span class="price"></span>
            <span class="label"></span>
        </div>
        <div class="sneaker-features">
            <i class="fas fa-star"></i>
            <span></span>
        </div>
    </div>
</div>
            </div>
        </div>
    </section>

<!-- Featured Brands Section -->
<section class="featured-brands section-padding">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">🔥 Trusted by Top Brands</h2>
            <p class="section-subtitle">Shop authentic footwear from world-renowned brands</p>
        </div>
        <div class="brands-grid">
            <?php foreach ($top_brands as $brand): ?>
            <div class="brand-card" data-brand="<?php echo $brand['name']; ?>">
                <div class="brand-logo-container">
                    <div class="brand-logo">
                        <?php if (isset($brand['logo_url'])): ?>
                            <img src="<?php echo $brand['logo_url']; ?>" 
                                 alt="<?php echo $brand['name']; ?>" 
                                 class="brand-logo-img"
                                 loading="lazy"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <?php endif; ?>
                        <div class="logo-fallback" style="<?php echo isset($brand['logo_url']) ? 'display: none;' : 'display: flex;'; ?>">
                            <?php if (isset($brand['icon'])): ?>
                                <i class="fas <?php echo $brand['icon']; ?>" style="font-size: 2rem;"></i>
                            <?php else: ?>
                                <span><?php echo $brand['name']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <h3 class="brand-title"><?php echo $brand['name']; ?></h3>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

    <!-- New Arrivals Section -->
    <section class="new-arrivals section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <div class="header-content">
                    <h2 class="section-title">🆕 New Arrivals</h2>
                    <p class="section-subtitle">Fresh kicks just dropped. Get them before they're gone!</p>
                </div>
                <a href="products/categories.php?filter=new" class="view-all-link">
                    View All
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="products-grid">
                <?php foreach ($new_arrivals as $product): ?>
                    <div class="product-item">
                        <?php 
                        // Set product variable untuk component
                        $product = $product; 
                        include 'components/product-card.php'; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">📁 Shop by Category</h2>
                <p class="section-subtitle">Find the perfect shoes for every occasion</p>
            </div>
            
            <div class="categories-grid">
                <?php
                $categories = [
                    ['name' => 'Running', 'icon' => 'fa-running', 'desc' => 'Performance shoes for every run'],
                    ['name' => 'Basketball', 'icon' => 'fa-basketball-ball', 'desc' => 'Court-ready performance'],
                    ['name' => 'Lifestyle', 'icon' => 'fa-user', 'desc' => 'Everyday comfort & style'],
                    ['name' => 'Skateboarding', 'icon' => 'fa-skating', 'desc' => 'Durable boardside performance']
                ];
                
                foreach ($categories as $category):
                ?>
                <div class="category-card">
                    <div class="category-image">
                        <div class="category-image-placeholder">
                            <i class="fas <?php echo $category['icon']; ?>"></i>
                        </div>
                        <div class="category-overlay"></div>
                    </div>
                    <div class="category-content">
                        <i class="fas <?php echo $category['icon']; ?> category-icon"></i>
                        <h3 class="category-title"><?php echo $category['name']; ?></h3>
                        <p class="category-description"><?php echo $category['desc']; ?></p>
                        <a href="products/categories.php?cat=<?php echo strtolower($category['name']); ?>" class="category-link">
                            Shop Now
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Sale Banner -->
    <section class="sale-banner section-padding">
        <div class="container">
            <div class="banner-content">
                <div class="banner-text">
                    <span class="banner-subtitle">🔥 Summer Sale</span>
                    <h2 class="banner-title">Up to <span class="highlight">50% OFF</span></h2>
                    <p class="banner-description">
                        Don't miss out on our biggest sale of the season. 
                        Limited time offer on selected styles. Hurry before they're gone!
                    </p>
                    <div class="countdown-timer">
                        <div class="timer-item">
                            <span class="timer-number" id="days">05</span>
                            <span class="timer-label">Days</span>
                        </div>
                        <div class="timer-item">
                            <span class="timer-number" id="hours">12</span>
                            <span class="timer-label">Hours</span>
                        </div>
                        <div class="timer-item">
                            <span class="timer-number" id="minutes">45</span>
                            <span class="timer-label">Minutes</span>
                        </div>
                        <div class="timer-item">
                            <span class="timer-number" id="seconds">30</span>
                            <span class="timer-label">Seconds</span>
                        </div>
                    </div>
                    <a href="products/categories.php?filter=sale" class="btn btn-primary btn-large pulse">
                        <i class="fas fa-fire"></i>
                        Shop Sale Now
                    </a>
                </div>
                <div class="banner-visual">
                    <div class="sale-sneaker">
                        <img src="https://media.sivasdescalzo.com/media/catalog/product/A/0/A02748C_sivasdescalzo-Converse-Chuck_70-1677143332-2.jpg?quality=70&auto=webp&fit=bounds&width=420" alt="Sale Sneaker" class="sale-sneaker-image">
                        <div class="discount-badge">50% OFF</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="featured-products section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">⭐ Featured Products</h2>
                <p class="section-subtitle">Curated selection of our most popular items</p>
            </div>
            
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-item">
                        <?php 
                        // Set product variable untuk component
                        $product = $product; 
                        include 'components/product-card.php'; 
                        ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">⭐ What Our Customers Say</h2>
                <p class="section-subtitle">Join thousands of satisfied sneakerheads</p>
            </div>
            
            <div class="testimonials-grid">
                <?php
                $testimonials = [
                    [
                        'name' => 'Sarah Johnson',
                        'role' => 'Sneaker Collector',
                        'text' => 'The quality and authenticity of the sneakers are unmatched. Fast shipping and great customer service!',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Mike Chen',
                        'role' => 'Basketball Player',
                        'text' => 'Finally found a store that has all the limited editions I\'ve been looking for. StepStyle is my go-to for rare kicks!',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Emily Rodriguez',
                        'role' => 'Fashion Blogger',
                        'text' => 'Excellent customer service and the return policy is hassle-free. Will definitely shop here again!',
                        'rating' => 4.5
                    ]
                ];
                
                foreach ($testimonials as $testimonial):
                ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <div class="stars">
                            <?php echo generateStarRating($testimonial['rating']); ?>
                        </div>
                        <p class="testimonial-text">"<?php echo $testimonial['text']; ?>"</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h4 class="author-name"><?php echo $testimonial['name']; ?></h4>
                            <span class="author-role"><?php echo $testimonial['role']; ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section bg-dark">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2 class="newsletter-title">📧 Stay in the Loop</h2>
                    <p class="newsletter-description">
                        Get exclusive access to new drops, special offers, and style tips. 
                        Be the first to know about limited releases.
                    </p>
                </div>
                <div class="newsletter-form">
                    <form class="subscribe-form" id="home-newsletter-form">
                        <div class="input-group">
                            <input type="email" placeholder="Enter your email address" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Subscribe
                            </button>
                        </div>
                        <div class="form-note">
                            By subscribing, you agree to our Privacy Policy
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include 'components/footer.php'; ?>

<!-- JavaScript -->
<script src="assets/js/main.js"></script>
<script src="assets/js/home.js"></script>

</body>
</html>