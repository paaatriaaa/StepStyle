<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

$database = new Database();
$db = $database->getConnection();

// Get category info
$category_slug = 'running';
$category_query = "SELECT * FROM categories WHERE slug = ?";
$category_stmt = $db->prepare($category_query);
$category_stmt->execute([$category_slug]);
$category = $category_stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header("Location: /404.php");
    exit();
}

// Get products for this category
$products_query = "SELECT p.*, b.name as brand_name, c.name as category_name 
                   FROM products p 
                   LEFT JOIN brands b ON p.brand_id = b.id 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE c.slug = ? AND p.status = 'active' 
                   ORDER BY p.created_at DESC";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute([$category_slug]);
?>

<?php include '../../includes/header.php'; ?>

<div class="page-header">
    <div class="container">
        <h1><?php echo $category['name']; ?> Shoes</h1>
        <p><?php echo $category['description']; ?></p>
    </div>
</div>

<div class="container">
    <div class="products-section">
        <div class="products-header">
            <h2>Running Collection</h2>
            <div class="products-filter">
                <select id="filter-brand">
                    <option value="">All Brands</option>
                    <option value="nike">Nike</option>
                    <option value="adidas">Adidas</option>
                    <option value="puma">Puma</option>
                </select>
                <select id="filter-price">
                    <option value="">All Prices</option>
                    <option value="0-100">Under $100</option>
                    <option value="100-200">$100 - $200</option>
                    <option value="200-500">$200 - $500</option>
                </select>
            </div>
        </div>
        
        <div class="products-grid">
            <?php while ($product = $products_stmt->fetch(PDO::FETCH_ASSOC)): 
                $images = json_decode($product['images'], true);
                $main_image = !empty($images) ? $images[0] : '/assets/images/products/default.jpg';
                $discount = $product['discount_price'] ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;
            ?>
            <div class="product-card" data-brand="<?php echo strtolower($product['brand_name']); ?>" data-price="<?php echo $product['discount_price'] ?: $product['price']; ?>">
                <div class="product-image">
                    <img src="<?php echo $main_image; ?>" alt="<?php echo $product['name']; ?>">
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
                </div>
                <div class="product-info">
                    <span class="product-brand"><?php echo $product['brand_name']; ?></span>
                    <h3 class="product-name"><?php echo $product['name']; ?></h3>
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
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="/assets/js/product-filter.js"></script>
<?php include '../../includes/footer.php'; ?>