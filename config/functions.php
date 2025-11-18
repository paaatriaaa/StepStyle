<?php
// Include required models
require_once 'database.php';

// Define base path for models
define('MODELS_PATH', __DIR__ . '/models/');

// Include model files
require_once MODELS_PATH . 'User.php';
require_once MODELS_PATH . 'Product.php';
require_once MODELS_PATH . 'Order.php';
require_once MODELS_PATH . 'OrderItem.php';
require_once MODELS_PATH . 'Cart.php';
require_once MODELS_PATH . 'Wishlist.php';
require_once MODELS_PATH . 'Review.php';

// Initialize database connection
$database = new Database();
$conn = $database->getConnection();

// ==================== HELPER FUNCTIONS ====================

/**
 * Generate star rating HTML
 */
function generateStarRating($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    $stars = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $stars .= '<i class="fas fa-star"></i>';
    }
    if ($halfStar) {
        $stars .= '<i class="fas fa-star-half-alt"></i>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $stars .= '<i class="far fa-star"></i>';
    }
    
    return $stars;
}

function formatPrice($price) {
    return '$' . number_format($price, 2);
}

/**
 * Calculate discount percentage
 */
function calculateDiscountPercentage($original_price, $current_price) {
    if ($original_price <= 0 || $current_price >= $original_price) {
        return 0;
    }
    return round((($original_price - $current_price) / $original_price) * 100);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ==================== AUTHENTICATION FUNCTIONS ====================

/**
 * Authenticate user using User model
 */
function authenticateUser($email, $password) {
    global $conn;
    
    try {
        $user = new User($conn);
        $user_data = $user->getUserByEmail($email);
        
        if ($user_data && $user->verifyPassword($password, $user_data['password_hash'])) {
            // Update last login
            $user->updateLastLogin($user_data['id']);
            
            return [
                'id' => $user_data['id'],
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                'email' => $user_data['email'],
                'role' => $user_data['role']
            ];
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user exists
 */
function userExists($email) {
    global $conn;
    
    try {
        $user = new User($conn);
        $user_data = $user->getUserByEmail($email);
        return $user_data !== false;
    } catch (Exception $e) {
        error_log("User exists check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Register new user using User model
 */
function registerUser($user_data) {
    global $conn;
    
    try {
        $user = new User($conn);
        
        // Set user properties
        $user->first_name = $user_data['first_name'];
        $user->last_name = $user_data['last_name'];
        $user->email = $user_data['email'];
        $user->phone = $user_data['phone'] ?? '';
        $user->password_hash = $user_data['password'];
        $user->newsletter = $user_data['newsletter'] ?? 0;
        
        // Attempt registration
        $result = $user->register();
        
        if ($result['success']) {
            return $result['user_id'];
        }
        
        error_log("User registration failed: " . $result['message']);
        return false;
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user by ID using User model
 */
function getUserById($user_id) {
    global $conn;
    
    try {
        $user = new User($conn);
        return $user->getUserById($user_id);
    } catch (Exception $e) {
        error_log("Get user by ID error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user profile using User model
 */
function updateUserProfile($user_id, $user_data) {
    global $conn;
    
    try {
        $user = new User($conn);
        return $user->updateProfile($user_id, $user_data);
    } catch (Exception $e) {
        error_log("Update profile error: " . $e->getMessage());
        return false;
    }
}

/**
 * Change user password
 */
function changePassword($user_id, $current_password, $new_password) {
    global $conn;
    
    try {
        $user = new User($conn);
        $user_data = $user->getUserById($user_id);
        
        if ($user_data && $user->verifyPassword($current_password, $user_data['password_hash'])) {
            // Update password
            $user->password_hash = $new_password;
            $user->id = $user_id;
            // Note: You might need to add an updatePassword method to User class
            return updateUserPassword($user_id, $new_password);
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Change password error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update user password directly
 */
function updateUserPassword($user_id, $new_password) {
    global $conn;
    
    try {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$hashed_password, $user_id]);
    } catch (Exception $e) {
        error_log("Update password error: " . $e->getMessage());
        return false;
    }
}

// ==================== PRODUCT FUNCTIONS ====================

/**
 * Get featured products using Product model
 */
function getFeaturedProducts($limit = 8) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->getFeaturedProducts($limit);
    } catch (Exception $e) {
        error_log("Error in getFeaturedProducts: " . $e->getMessage());
        return getFallbackFeaturedProducts();
    }
}

/**
 * Get new arrival products using Product model
 */
function getNewArrivals($limit = 8) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->getNewArrivals($limit);
    } catch (Exception $e) {
        error_log("Error in getNewArrivals: " . $e->getMessage());
        return getFallbackNewArrivals();
    }
}

/**
 * Get products on sale using Product model
 */
function getProductsOnSale($limit = 8) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->getProductsOnSale($limit);
    } catch (Exception $e) {
        error_log("Error in getProductsOnSale: " . $e->getMessage());
        return [];
    }
}

/**
 * Get product by ID using Product model
 */
function getProductById($id) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->getProductById($id);
    } catch (Exception $e) {
        error_log("Get product by ID error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all products with filters using Product model
 */
function getAllProducts($limit = null, $offset = 0) {
    global $conn;
    
    try {
        $product = new Product($conn);
        $filters = [];
        if ($limit) {
            $filters['limit'] = $limit;
            $filters['offset'] = $offset;
        }
        return $product->getProducts($filters);
    } catch (Exception $e) {
        error_log("Get all products error: " . $e->getMessage());
        return [];
    }
}

/**
 * Search products using Product model
 */
function searchProducts($search_term, $limit = 20) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->searchProducts($search_term, $limit);
    } catch (Exception $e) {
        error_log("Search products error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get filtered products using Product model
 */
function getFilteredProducts($filters = []) {
    global $conn;
    
    try {
        $product = new Product($conn);
        return $product->getProducts($filters);
    } catch (Exception $e) {
        error_log("Get filtered products error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get brands using Product model
 */
function getBrands() {
    global $conn;
    
    try {
        $product = new Product($conn);
        $brands = $product->getBrands();
        
        return array_map(function($brand) {
            return [
                'name' => $brand,
                'slug' => strtolower($brand),
                'count' => 1 // You might want to add count logic
            ];
        }, $brands);
    } catch (Exception $e) {
        error_log("Get brands error: " . $e->getMessage());
        return getFallbackBrands();
    }
}

/**
 * Get top brands for display
 */
function getTopBrands() {
    return [
        ['name' => 'Nike', 'icon' => 'fa-bolt', 'color' => '#000000'],
        ['name' => 'Adidas', 'icon' => 'fa-trefoil', 'color' => '#000000'],
        ['name' => 'Jordan', 'icon' => 'fa-basketball-ball', 'color' => '#ce1141'],
        ['name' => 'Puma', 'icon' => 'fa-paw', 'color' => '#000000'],
        ['name' => 'New Balance', 'icon' => 'fa-n', 'color' => '#ce0e2d'],
        ['name' => 'Vans', 'icon' => 'fa-v', 'color' => '#000000']
    ];
}

// ==================== CART FUNCTIONS ====================

/**
 * Get cart items using Cart model
 */
function getCartItems($user_id) {
    global $conn;
    
    try {
        // Cek jika class Cart ada
        if (!class_exists('Cart')) {
            return getFallbackCartItems($user_id);
        }
        
        $cart = new Cart($conn);
        return $cart->getCartItems($user_id);
    } catch (Exception $e) {
        error_log("Get cart items error: " . $e->getMessage());
        return getFallbackCartItems($user_id);
    }
}

/**
 * Calculate cart total using Cart model
 */
function calculateCartTotal($user_id) {
    global $conn;
    
    try {
        // Cek jika class Cart ada
        if (!class_exists('Cart')) {
            return getFallbackCartTotal($user_id);
        }
        
        $cart = new Cart($conn);
        $cart_items = $cart->getCartItems($user_id);
        $subtotal = $cart->getCartTotal($user_id);
        
        $shipping = $subtotal >= 50 ? 0 : 9.99;
        $tax = $subtotal * 0.08; // 8% tax
        $discount = $subtotal >= 100 ? $subtotal * 0.1 : 0; // 10% discount for orders over $100
        
        $total = $subtotal + $shipping + $tax - $discount;
        
        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'item_count' => $cart->getCartItemCount($user_id)
        ];
    } catch (Exception $e) {
        error_log("Calculate cart total error: " . $e->getMessage());
        return getFallbackCartTotal($user_id);
    }
}

/**
 * Get cart item count using Cart model
 */
function getCartItemCount($user_id) {
    global $conn;
    
    try {
        // Cek jika class Cart ada
        if (!class_exists('Cart')) {
            return count(getFallbackCartItems($user_id));
        }
        
        $cart = new Cart($conn);
        return $cart->getCartItemCount($user_id);
    } catch (Exception $e) {
        error_log("Get cart item count error: " . $e->getMessage());
        return count(getFallbackCartItems($user_id));
    }
}

// ==================== FALLBACK CART FUNCTIONS ====================

/**
 * Fallback cart items data
 */
function getFallbackCartItems($user_id) {
    // Return empty array untuk testing
    return [];
}

/**
 * Fallback cart total data
 */
function getFallbackCartTotal($user_id) {
    return [
        'subtotal' => 0,
        'shipping' => 0,
        'tax' => 0,
        'discount' => 0,
        'total' => 0,
        'item_count' => 0
    ];
}

/**
 * Fallback recommended products
 */
function getRecommendedProducts($limit = 4) {
    // Return some sample products for cart recommendations
    return [
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
    ];
}

// ==================== WISHLIST FUNCTIONS ====================

/**
 * Get wishlist items using Wishlist model
 */
function getWishlistItems($user_id) {
    global $conn;
    
    try {
        $wishlist = new Wishlist($conn);
        return $wishlist->getWishlistItems($user_id);
    } catch (Exception $e) {
        error_log("Get wishlist items error: " . $e->getMessage());
        return [];
    }
}

/**
 * Add item to wishlist using Wishlist model
 */
function addToWishlist($user_id, $product_id) {
    global $conn;
    
    try {
        $wishlist = new Wishlist($conn);
        return $wishlist->addToWishlist($user_id, $product_id);
    } catch (Exception $e) {
        error_log("Add to wishlist error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Unable to add item to wishlist'];
    }
}

/**
 * Remove item from wishlist using Wishlist model
 */
function removeFromWishlist($user_id, $product_id) {
    global $conn;
    
    try {
        $wishlist = new Wishlist($conn);
        return $wishlist->removeFromWishlist($user_id, $product_id);
    } catch (Exception $e) {
        error_log("Remove from wishlist error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Unable to remove item from wishlist'];
    }
}

/**
 * Check if item is in wishlist using Wishlist model
 */
function isInWishlist($user_id, $product_id) {
    global $conn;
    
    try {
        $wishlist = new Wishlist($conn);
        return $wishlist->isInWishlist($user_id, $product_id);
    } catch (Exception $e) {
        error_log("Check wishlist error: " . $e->getMessage());
        return false;
    }
}

/**
 * Calculate wishlist total
 */
function calculateWishlistTotal($wishlist_items) {
    $total = 0;
    foreach ($wishlist_items as $item) {
        $total += $item['price'];
    }
    return $total;
}

// ==================== ORDER FUNCTIONS ====================

/**
 * Get user orders using Order model
 */
function getUserOrders($user_id, $limit = 10) {
    global $conn;
    
    try {
        $order = new Order($conn);
        return $order->getOrdersByUser($user_id, $limit);
    } catch (Exception $e) {
        error_log("Get user orders error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get order by ID using Order model
 */
function getOrderById($order_id) {
    global $conn;
    
    try {
        $order = new Order($conn);
        return $order->getOrderById($order_id);
    } catch (Exception $e) {
        error_log("Get order by ID error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get order items using OrderItem model
 */
function getOrderItems($order_id) {
    global $conn;
    
    try {
        $order_item = new OrderItem($conn);
        return $order_item->getOrderItemsWithProducts($order_id);
    } catch (Exception $e) {
        error_log("Get order items error: " . $e->getMessage());
        return [];
    }
}

/**
 * Create new order using Order and OrderItem models
 */
function createOrder($order_data, $order_items) {
    global $conn;
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Create order
        $order = new Order($conn);
        $order->user_id = $order_data['user_id'];
        $order->total_amount = $order_data['total_amount'];
        $order->status = 'pending';
        $order->shipping_address = $order_data['shipping_address'];
        $order->billing_address = $order_data['billing_address'] ?? $order_data['shipping_address'];
        $order->payment_method = $order_data['payment_method'];
        $order->payment_status = 'pending';
        
        $order_result = $order->create();
        
        if (!$order_result['success']) {
            $conn->rollBack();
            return ['success' => false, 'message' => $order_result['message']];
        }
        
        $order_id = $order_result['order_id'];
        
        // Add order items
        $order_item = new OrderItem($conn);
        foreach ($order_items as $item) {
            $order_item->order_id = $order_id;
            $order_item->product_id = $item['product_id'];
            $order_item->quantity = $item['quantity'];
            $order_item->price = $item['price'];
            
            $item_result = $order_item->create();
            if (!$item_result['success']) {
                $conn->rollBack();
                return ['success' => false, 'message' => $item_result['message']];
            }
            
            // Update product stock
            $product = new Product($conn);
            $current_product = $product->getProductById($item['product_id']);
            if ($current_product) {
                $new_stock = $current_product['stock_quantity'] - $item['quantity'];
                $product->updateStock($item['product_id'], $new_stock);
            }
        }
        
        // Clear user's cart
        $cart = new Cart($conn);
        $cart->clearCart($order_data['user_id']);
        
        $conn->commit();
        return ['success' => true, 'order_id' => $order_id, 'order_number' => $order_result['order_number']];
        
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Create order error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Order creation failed'];
    }
}

// ==================== REVIEW FUNCTIONS ====================

/**
 * Get product reviews using Review model
 */
function getProductReviews($product_id, $status = 'approved', $limit = 10) {
    global $conn;
    
    try {
        $review = new Review($conn);
        return $review->getProductReviews($product_id, $status, $limit);
    } catch (Exception $e) {
        error_log("Get product reviews error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get product rating statistics using Review model
 */
function getProductRatingStats($product_id) {
    global $conn;
    
    try {
        $review = new Review($conn);
        return $review->getProductRatingStats($product_id);
    } catch (Exception $e) {
        error_log("Get product rating stats error: " . $e->getMessage());
        return [
            'total_reviews' => 0,
            'average_rating' => 0,
            'five_star' => 0,
            'four_star' => 0,
            'three_star' => 0,
            'two_star' => 0,
            'one_star' => 0
        ];
    }
}

/**
 * Submit review using Review model
 */
function submitReview($review_data) {
    global $conn;
    
    try {
        $review = new Review($conn);
        $review->user_id = $review_data['user_id'];
        $review->product_id = $review_data['product_id'];
        $review->rating = $review_data['rating'];
        $review->comment = $review_data['comment'];
        
        return $review->create();
    } catch (Exception $e) {
        error_log("Submit review error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Unable to submit review'];
    }
}

// ==================== ADMIN FUNCTIONS ====================

/**
 * Get admin dashboard statistics
 */
function getAdminStats() {
    global $conn;
    
    try {
        // Total products
        $product = new Product($conn);
        $products = $product->getProducts(['limit' => 1]);
        $total_products = count($products); // This is simplified
        
        // Total orders
        $order = new Order($conn);
        $orders = $order->getAllOrders(['limit' => 1]);
        $total_orders = count($orders); // This is simplified
        
        // Total users
        $user = new User($conn);
        // Note: You might need to add a count method to User class
        $total_users = 0;
        
        // Total revenue
        $order_stats = $order->getOrderStats('month');
        $total_revenue = 0;
        foreach ($order_stats as $stat) {
            $total_revenue += $stat['total_revenue'];
        }
        
        // Recent orders
        $recent_orders = $order->getAllOrders(['limit' => 5]);
        
        // Low stock products
        $low_stock_products = $product->getProducts(['limit' => 5]); // Simplified
        
        return [
            'total_products' => $total_products,
            'total_orders' => $total_orders,
            'total_users' => $total_users,
            'total_revenue' => $total_revenue,
            'recent_orders' => $recent_orders,
            'low_stock_products' => $low_stock_products
        ];
        
    } catch (Exception $e) {
        error_log("Admin stats error: " . $e->getMessage());
        return [
            'total_products' => 0,
            'total_orders' => 0,
            'total_users' => 0,
            'total_revenue' => 0,
            'recent_orders' => [],
            'low_stock_products' => []
        ];
    }
}

// ==================== FALLBACK DATA ====================

/**
 * Fallback featured products data
 */
function getFallbackFeaturedProducts() {
    return [
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
        ]
    ];
}

/**
 * Fallback new arrivals data
 */
function getFallbackNewArrivals() {
    return [
        [
            'id' => 6,
            'name' => 'Nike Dunk Low',
            'brand' => 'NIKE',
            'price' => 110.00,
            'original_price' => 0,
            'image_url' => 'https://sneakerbardetroit.com/wp-content/uploads/2023/05/Nike-Dunk-Low-White-Oil-Green-Cargo-Khaki-FN6882-100.jpeg',
            'rating' => 4.7,
            'review_count' => 89,
            'stock_quantity' => 12,
            'featured' => false,
            'on_sale' => false,
            'new_arrival' => true
        ]
    ];
}

/**
 * Fallback brands data
 */
function getFallbackBrands() {
    return [
        ['name' => 'Nike', 'slug' => 'nike', 'count' => 56],
        ['name' => 'Adidas', 'slug' => 'adidas', 'count' => 42],
        ['name' => 'Jordan', 'slug' => 'jordan', 'count' => 28]
    ];
}

// ==================== URL & REDIRECT FUNCTIONS ====================

/**
 * Get base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . rtrim($script, '/');
}

/**
 * Generate URL
 */
function url($path = '') {
    return getBaseUrl() . '/' . ltrim($path, '/');
}

/**
 * Generate asset URL
 */
function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect to URL
 */
function redirect($path) {
    header('Location: ' . url($path));
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Require admin access
 */
function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['error'] = 'Access denied. Admin privileges required.';
        redirect('index.php');
        exit();
    }
}

/**
 * Require user login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        $_SESSION['error'] = 'Please log in to access this page.';
        redirect('auth/login.php');
        exit();
    }
}

// ==================== SESSION MESSAGES ====================

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    $message = getFlashMessage();
    if ($message) {
        $alert_class = $message['type'] === 'error' ? 'alert-error' : 'alert-success';
        echo '<div class="alert ' . $alert_class . '">';
        echo '<i class="fas ' . ($message['type'] === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle') . '"></i>';
        echo $message['message'];
        echo '</div>';
    }
}
?>