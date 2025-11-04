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

// Get products based on filters
$products = getFilteredProducts($category_filter, $brand_filter, $price_filter, $sort_by, $search_query);
$categories = getCategories();
$brands = getBrands();
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
                            <?php foreach ($products as $product): ?>
                                <?php include '../components/product-card.php'; ?>
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