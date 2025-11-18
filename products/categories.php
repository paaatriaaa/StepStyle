<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Shop All Categories - StepStyle';
$page_description = 'Browse our complete collection of sneakers and footwear. Filter by brand, category, price, and more to find your perfect pair.';
$body_class = 'categories-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Get filter parameters
$category_filter = $_GET['cat'] ?? '';
$brand_filter = $_GET['brand'] ?? '';
$price_filter = $_GET['price'] ?? '';
$sort_by = $_GET['sort'] ?? 'featured';
$search_query = $_GET['search'] ?? '';

// Data produk langsung di file ini (sama seperti di index.php)
$all_products = [
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
    ],
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

// Apply filters
$products = $all_products;

if ($category_filter) {
    $products = array_filter($products, function($product) use ($category_filter) {
        return stripos($product['name'], $category_filter) !== false || 
               stripos($product['brand'], $category_filter) !== false;
    });
}

if ($brand_filter) {
    $products = array_filter($products, function($product) use ($brand_filter) {
        return strtolower($product['brand']) === strtolower($brand_filter);
    });
}

if ($price_filter) {
    $products = array_filter($products, function($product) use ($price_filter) {
        switch ($price_filter) {
            case 'under50':
                return $product['price'] < 50;
            case '50-100':
                return $product['price'] >= 50 && $product['price'] <= 100;
            case '100-200':
                return $product['price'] >= 100 && $product['price'] <= 200;
            case 'over200':
                return $product['price'] > 200;
            default:
                return true;
        }
    });
}

if ($search_query) {
    $products = array_filter($products, function($product) use ($search_query) {
        return stripos($product['name'], $search_query) !== false || 
               stripos($product['brand'], $search_query) !== false;
    });
}

// Apply sorting
usort($products, function($a, $b) use ($sort_by) {
    switch ($sort_by) {
        case 'price-low':
            return $a['price'] <=> $b['price'];
        case 'price-high':
            return $b['price'] <=> $a['price'];
        case 'newest':
            return ($b['new_arrival'] ?? false) <=> ($a['new_arrival'] ?? false);
        case 'rating':
            return $b['rating'] <=> $a['rating'];
        case 'name':
            return $a['name'] <=> $b['name'];
        default: // featured
            return ($b['featured'] ?? false) <=> ($a['featured'] ?? false);
    }
});

// Data categories dan brands berdasarkan produk yang ada
$categories = [
    ['name' => 'Running', 'slug' => 'running', 'count' => 4],
    ['name' => 'Basketball', 'slug' => 'basketball', 'count' => 2],
    ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'count' => 5],
    ['name' => 'Skateboarding', 'slug' => 'skateboarding', 'count' => 2],
    ['name' => 'Training', 'slug' => 'training', 'count' => 3],
    ['name' => 'Sandals', 'slug' => 'sandals', 'count' => 1]
];

// Hitung brands dari produk yang ada
$brand_counts = [];
foreach ($all_products as $product) {
    $brand = $product['brand'];
    if (!isset($brand_counts[$brand])) {
        $brand_counts[$brand] = 0;
    }
    $brand_counts[$brand]++;
}

$brands = [];
foreach ($brand_counts as $brand_name => $count) {
    $brands[] = [
        'name' => $brand_name,
        'slug' => strtolower($brand_name),
        'count' => $count
    ];
}
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
    <link rel="stylesheet" href="../assets/css/categories.css">
    
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
            <span>Shop All</span>
        </nav>

        <div class="categories-layout">
            <!-- Sidebar Filters -->
            <aside class="filters-sidebar">
                <div class="filters-header">
                    <h3>Filters</h3>
                    <button class="clear-filters" id="clear-filters">
                        <i class="fas fa-times"></i>
                        Clear All
                    </button>
                </div>

                <!-- Categories Filter -->
                <div class="filter-section">
                    <div class="filter-header">
                        <h4>Categories</h4>
                        <button class="filter-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-content">
                        <div class="filter-options">
                            <?php foreach ($categories as $category): ?>
                            <div class="filter-option">
                                <input type="checkbox" 
                                       id="cat-<?php echo $category['slug']; ?>" 
                                       name="category" 
                                       value="<?php echo $category['slug']; ?>"
                                       <?php echo $category_filter === $category['slug'] ? 'checked' : ''; ?>>
                                <label for="cat-<?php echo $category['slug']; ?>">
                                    <span class="option-name"><?php echo $category['name']; ?></span>
                                    <span class="option-count">(<?php echo $category['count']; ?>)</span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Brands Filter -->
                <div class="filter-section">
                    <div class="filter-header">
                        <h4>Brands</h4>
                        <button class="filter-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-content">
                        <div class="filter-options">
                            <?php foreach ($brands as $brand): ?>
                            <div class="filter-option">
                                <input type="checkbox" 
                                       id="brand-<?php echo $brand['slug']; ?>" 
                                       name="brand" 
                                       value="<?php echo $brand['slug']; ?>"
                                       <?php echo $brand_filter === $brand['slug'] ? 'checked' : ''; ?>>
                                <label for="brand-<?php echo $brand['slug']; ?>">
                                    <span class="option-name"><?php echo $brand['name']; ?></span>
                                    <span class="option-count">(<?php echo $brand['count']; ?>)</span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Price Filter -->
                <div class="filter-section">
                    <div class="filter-header">
                        <h4>Price Range</h4>
                        <button class="filter-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-content">
                        <div class="filter-options">
                            <div class="filter-option">
                                <input type="radio" id="price-all" name="price" value="" <?php echo empty($price_filter) ? 'checked' : ''; ?>>
                                <label for="price-all">All Prices</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="price-under50" name="price" value="under50" <?php echo $price_filter === 'under50' ? 'checked' : ''; ?>>
                                <label for="price-under50">Under $50</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="price-50-100" name="price" value="50-100" <?php echo $price_filter === '50-100' ? 'checked' : ''; ?>>
                                <label for="price-50-100">$50 - $100</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="price-100-200" name="price" value="100-200" <?php echo $price_filter === '100-200' ? 'checked' : ''; ?>>
                                <label for="price-100-200">$100 - $200</label>
                            </div>
                            <div class="filter-option">
                                <input type="radio" id="price-over200" name="price" value="over200" <?php echo $price_filter === 'over200' ? 'checked' : ''; ?>>
                                <label for="price-over200">Over $200</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Size Filter -->
                <div class="filter-section">
                    <div class="filter-header">
                        <h4>Size</h4>
                        <button class="filter-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-content">
                        <div class="size-options">
                            <?php
                            $sizes = ['6', '7', '8', '9', '10', '11', '12', '13'];
                            foreach ($sizes as $size):
                            ?>
                            <button type="button" class="size-option" data-size="<?php echo $size; ?>">
                                <?php echo $size; ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Color Filter -->
                <div class="filter-section">
                    <div class="filter-header">
                        <h4>Color</h4>
                        <button class="filter-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-content">
                        <div class="color-options">
                            <?php
                            $colors = [
                                ['name' => 'Black', 'value' => 'black', 'hex' => '#000000'],
                                ['name' => 'White', 'value' => 'white', 'hex' => '#ffffff'],
                                ['name' => 'Red', 'value' => 'red', 'hex' => '#dc2626'],
                                ['name' => 'Blue', 'value' => 'blue', 'hex' => '#2563eb'],
                                ['name' => 'Green', 'value' => 'green', 'hex' => '#16a34a'],
                                ['name' => 'Gray', 'value' => 'gray', 'hex' => '#6b7280']
                            ];
                            foreach ($colors as $color):
                            ?>
                            <div class="color-option">
                                <input type="checkbox" id="color-<?php echo $color['value']; ?>" name="color" value="<?php echo $color['value']; ?>">
                                <label for="color-<?php echo $color['value']; ?>" title="<?php echo $color['name']; ?>">
                                    <span class="color-swatch" style="background-color: <?php echo $color['hex']; ?>;"></span>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="products-main">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="header-content">
                        <h1 class="page-title">
                            <?php if ($search_query): ?>
                                Search Results for "<?php echo htmlspecialchars($search_query); ?>"
                            <?php elseif ($category_filter): ?>
                                <?php echo ucfirst($category_filter); ?> Sneakers
                            <?php else: ?>
                                All Sneakers
                            <?php endif; ?>
                        </h1>
                        <p class="page-subtitle">
                            <?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> found
                        </p>
                    </div>

                    <div class="header-actions">
                        <div class="view-options">
                            <button class="view-option active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-option" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>

                        <div class="sort-options">
                            <label for="sort-by">Sort by:</label>
                            <select id="sort-by" class="sort-select">
                                <option value="featured" <?php echo $sort_by === 'featured' ? 'selected' : ''; ?>>Featured</option>
                                <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest</option>
                                <option value="price-low" <?php echo $sort_by === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
                                <option value="price-high" <?php echo $sort_by === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
                                <option value="rating" <?php echo $sort_by === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                                <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Active Filters -->
                <div class="active-filters" id="active-filters">
                    <?php if ($category_filter || $brand_filter || $price_filter): ?>
                    <div class="filters-list">
                        <span class="filters-label">Active filters:</span>
                        <?php if ($category_filter): ?>
                        <span class="filter-tag">
                            Category: <?php echo ucfirst($category_filter); ?>
                            <button class="remove-filter" data-filter="category">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                        <?php endif; ?>
                        <?php if ($brand_filter): ?>
                        <span class="filter-tag">
                            Brand: <?php echo ucfirst($brand_filter); ?>
                            <button class="remove-filter" data-filter="brand">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                        <?php endif; ?>
                        <?php if ($price_filter): ?>
                        <span class="filter-tag">
                            Price: <?php echo ucfirst(str_replace('-', ' - $', $price_filter)); ?>
                            <button class="remove-filter" data-filter="price">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Products Grid -->
                <div class="products-container">
                    <div class="products-grid" id="products-grid">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product_item): ?>
                                <div class="product-item">
                                    <?php 
                                    // Set product variable untuk component
                                    $product = $product_item; 
                                    include '../components/product-card.php'; 
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- No Results -->
                            <div class="no-results">
                                <div class="no-results-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h3>No products found</h3>
                                <p>Try adjusting your filters or search terms</p>
                                <button class="btn btn-primary" id="reset-search">
                                    <i class="fas fa-redo"></i>
                                    Reset Filters
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Load More -->
                    <?php if (count($products) >= 12): ?>
                    <div class="load-more-section">
                        <button class="btn btn-outline btn-large" id="load-more">
                            <i class="fas fa-plus"></i>
                            Load More Products
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/categories.js"></script>

</body>
</html>