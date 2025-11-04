<?php
// Function to generate star rating HTML
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

// Function to format price
function formatPrice($price) {
    return '$' . number_format($price, 2);
}

// Safe product data retrieval functions with fallbacks
function getFeaturedProducts($limit = 8) {
    try {
        // Fallback sample data for testing
        return [
            [
                'id' => 1,
                'name' => 'Nike Air Max 270',
                'brand' => 'Nike',
                'price' => 149.99,
                'original_price' => 179.99,
                'image_url' => 'assets/images/products/nike-air-max-270.jpg',
                'rating' => 4.5,
                'review_count' => 128,
                'stock_quantity' => 10,
                'featured' => true,
                'on_sale' => true,
                'new_arrival' => false
            ],
            [
                'id' => 2,
                'name' => 'Adidas Ultraboost 21',
                'brand' => 'Adidas',
                'price' => 180.00,
                'original_price' => 220.00,
                'image_url' => 'assets/images/products/adidas-ultraboost-21.jpg',
                'rating' => 4.8,
                'review_count' => 256,
                'stock_quantity' => 15,
                'featured' => true,
                'on_sale' => false,
                'new_arrival' => true
            ],
            [
                'id' => 3,
                'name' => 'Jordan 1 Retro High',
                'brand' => 'Jordan',
                'price' => 170.00,
                'original_price' => 190.00,
                'image_url' => '',
                'rating' => 4.7,
                'review_count' => 89,
                'stock_quantity' => 5,
                'featured' => true,
                'on_sale' => true,
                'new_arrival' => false
            ],
            [
                'id' => 4,
                'name' => 'New Balance 550',
                'brand' => 'New Balance',
                'price' => 120.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.3,
                'review_count' => 67,
                'stock_quantity' => 8,
                'featured' => true,
                'on_sale' => false,
                'new_arrival' => true
            ],
            [
                'id' => 9,
                'name' => 'Nike Air Force 1',
                'brand' => 'Nike',
                'price' => 100.00,
                'original_price' => 120.00,
                'image_url' => '',
                'rating' => 4.6,
                'review_count' => 342,
                'stock_quantity' => 25,
                'featured' => true,
                'on_sale' => true,
                'new_arrival' => false
            ],
            [
                'id' => 10,
                'name' => 'Adidas NMD R1',
                'brand' => 'Adidas',
                'price' => 130.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.4,
                'review_count' => 198,
                'stock_quantity' => 7,
                'featured' => true,
                'on_sale' => false,
                'new_arrival' => false
            ],
            [
                'id' => 11,
                'name' => 'Puma Cali',
                'brand' => 'Puma',
                'price' => 80.00,
                'original_price' => 95.00,
                'image_url' => '',
                'rating' => 4.2,
                'review_count' => 76,
                'stock_quantity' => 0,
                'featured' => true,
                'on_sale' => true,
                'new_arrival' => false
            ],
            [
                'id' => 12,
                'name' => 'Vans Sk8-Hi',
                'brand' => 'Vans',
                'price' => 65.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.5,
                'review_count' => 234,
                'stock_quantity' => 18,
                'featured' => true,
                'on_sale' => false,
                'new_arrival' => false
            ]
        ];
    } catch (Exception $e) {
        error_log("Error in getFeaturedProducts: " . $e->getMessage());
        return [];
    }
}

function getNewArrivals($limit = 4) {
    try {
        // Fallback sample data
        return [
            [
                'id' => 5,
                'name' => 'Puma RS-X',
                'brand' => 'Puma',
                'price' => 110.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.2,
                'review_count' => 45,
                'stock_quantity' => 12,
                'featured' => false,
                'on_sale' => false,
                'new_arrival' => true
            ],
            [
                'id' => 6,
                'name' => 'Vans Old Skool',
                'brand' => 'Vans',
                'price' => 60.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.6,
                'review_count' => 203,
                'stock_quantity' => 0,
                'featured' => false,
                'on_sale' => false,
                'new_arrival' => true
            ],
            [
                'id' => 7,
                'name' => 'Converse Chuck 70',
                'brand' => 'Converse',
                'price' => 85.00,
                'original_price' => 100.00,
                'image_url' => '',
                'rating' => 4.4,
                'review_count' => 156,
                'stock_quantity' => 3,
                'featured' => false,
                'on_sale' => true,
                'new_arrival' => true
            ],
            [
                'id' => 8,
                'name' => 'Reebok Classic',
                'brand' => 'Reebok',
                'price' => 75.00,
                'original_price' => 0,
                'image_url' => '',
                'rating' => 4.1,
                'review_count' => 89,
                'stock_quantity' => 20,
                'featured' => false,
                'on_sale' => false,
                'new_arrival' => true
            ]
        ];
    } catch (Exception $e) {
        error_log("Error in getNewArrivals: " . $e->getMessage());
        return [];
    }
}

function getTopBrands() {
    try {
        // Fallback sample data
        return [
            ['name' => 'Nike', 'icon' => 'fa-bolt', 'color' => '#000000'],
            ['name' => 'Adidas', 'icon' => 'fa-trefoil', 'color' => '#000000'],
            ['name' => 'Jordan', 'icon' => 'fa-basketball-ball', 'color' => '#ce1141'],
            ['name' => 'Puma', 'icon' => 'fa-paw', 'color' => '#000000'],
            ['name' => 'New Balance', 'icon' => 'fa-n', 'color' => '#ce0e2d'],
            ['name' => 'Vans', 'icon' => 'fa-v', 'color' => '#000000']
        ];
    } catch (Exception $e) {
        error_log("Error in getTopBrands: " . $e->getMessage());
        return [];
    }
}
// Cart Functions
function getCartItems() {
    // Simulate cart data
    return [
        [
            'id' => 1,
            'name' => 'Nike Air Max 270',
            'brand' => 'Nike',
            'price' => 149.99,
            'original_price' => 179.99,
            'image_url' => '../assets/images/products/nike-air-max-270.jpg',
            'size' => 'US 10',
            'color' => 'Black/White',
            'quantity' => 1,
            'stock_quantity' => 10
        ],
        [
            'id' => 2,
            'name' => 'Adidas Ultraboost 21',
            'brand' => 'Adidas',
            'price' => 180.00,
            'original_price' => 220.00,
            'image_url' => '../assets/images/products/adidas-ultraboost-21.jpg',
            'size' => 'US 9.5',
            'color' => 'Core Black',
            'quantity' => 2,
            'stock_quantity' => 15
        ]
    ];
}

function calculateCartTotal($cart_items) {
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    
    $shipping = $subtotal >= 50 ? 0 : 9.99;
    $tax = $subtotal * 0.08; // 8% tax
    $discount = $subtotal >= 100 ? $subtotal * 0.1 : 0; // 10% discount for orders over $100
    
    $total = $subtotal + $shipping + $tax - $discount;
    
    return [
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'tax' => $tax,
        'discount' => $discount,
        'total' => $total
    ];
}

// Wishlist Functions
function getWishlistItems() {
    return [
        [
            'id' => 3,
            'name' => 'Jordan 1 Retro High',
            'brand' => 'Jordan',
            'price' => 170.00,
            'original_price' => 190.00,
            'image_url' => '',
            'rating' => 4.7,
            'review_count' => 89,
            'stock_quantity' => 5,
            'on_sale' => true,
            'date_added' => '2024-01-15'
        ],
        [
            'id' => 4,
            'name' => 'New Balance 550',
            'brand' => 'New Balance',
            'price' => 120.00,
            'original_price' => 0,
            'image_url' => '',
            'rating' => 4.3,
            'review_count' => 67,
            'stock_quantity' => 0,
            'on_sale' => false,
            'date_added' => '2024-01-10'
        ]
    ];
}

function calculateWishlistTotal($wishlist_items) {
    $total = 0;
    foreach ($wishlist_items as $item) {
        $total += $item['price'];
    }
    return $total;
}

function getWishlistRecommendations($limit = 4) {
    return getFeaturedProducts($limit);
}

// Checkout Functions
function getCurrentUser() {
    return [
        'email' => 'customer@example.com',
        'phone' => '+1 (555) 123-4567',
        'full_name' => 'John Doe',
        'address' => '123 Main Street',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'zip_code' => '90001'
    ];
}

function getShippingMethods() {
    return [
        [
            'id' => 'standard',
            'name' => 'Standard Shipping',
            'description' => '5-7 business days',
            'cost' => 9.99,
            'default' => false
        ],
        [
            'id' => 'express',
            'name' => 'Express Shipping',
            'description' => '2-3 business days',
            'cost' => 19.99,
            'default' => false
        ],
        [
            'id' => 'free',
            'name' => 'Free Shipping',
            'description' => '5-7 business days',
            'cost' => 0,
            'default' => true
        ]
    ];
}

// Product Recommendations
function getRecommendedProducts($limit = 4) {
    return getFeaturedProducts($limit);
}
// Auth Functions
function authenticateUser($email, $password) {
    // Simulate user authentication
    $users = [
        [
            'id' => 1,
            'email' => 'demo@stepstyle.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name' => 'Demo',
            'last_name' => 'User',
            'phone' => '+1 (555) 123-4567'
        ]
    ];
    
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    
    return false;
}

function registerUser($user_data) {
    // Simulate user registration
    return [
        'id' => rand(1000, 9999),
        'email' => $user_data['email'],
        'first_name' => $user_data['first_name'],
        'last_name' => $user_data['last_name'],
        'phone' => $user_data['phone'] ?? ''
    ];
}

// Categories Functions
function getFilteredProducts($category = '', $brand = '', $price = '', $sort = 'featured', $search = '') {
    $all_products = array_merge(getFeaturedProducts(20), getNewArrivals(20));
    
    // Apply filters
    $filtered_products = array_filter($all_products, function($product) use ($category, $brand, $price, $search) {
        // Category filter
        if ($category && stripos($product['name'], $category) === false) {
            return false;
        }
        
        // Brand filter
        if ($brand && stripos($product['brand'], $brand) === false) {
            return false;
        }
        
        // Price filter
        if ($price) {
            switch ($price) {
                case 'under50':
                    if ($product['price'] >= 50) return false;
                    break;
                case '50-100':
                    if ($product['price'] < 50 || $product['price'] > 100) return false;
                    break;
                case '100-200':
                    if ($product['price'] < 100 || $product['price'] > 200) return false;
                    break;
                case 'over200':
                    if ($product['price'] <= 200) return false;
                    break;
            }
        }
        
        // Search filter
        if ($search) {
            $search_terms = strtolower($search);
            $product_text = strtolower($product['name'] . ' ' . $product['brand']);
            if (strpos($product_text, $search_terms) === false) {
                return false;
            }
        }
        
        return true;
    });
    
    // Apply sorting
    usort($filtered_products, function($a, $b) use ($sort) {
        switch ($sort) {
            case 'price-low':
                return $a['price'] <=> $b['price'];
            case 'price-high':
                return $b['price'] <=> $a['price'];
            case 'newest':
                return $b['id'] <=> $a['id'];
            case 'rating':
                return $b['rating'] <=> $a['rating'];
            case 'name':
                return strcmp($a['name'], $b['name']);
            default: // featured
                return $b['featured'] <=> $a['featured'];
        }
    });
    
    return array_values($filtered_products);
}

function getCategories() {
    return [
        ['name' => 'Running', 'slug' => 'running', 'count' => 45],
        ['name' => 'Basketball', 'slug' => 'basketball', 'count' => 32],
        ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'count' => 78],
        ['name' => 'Skateboarding', 'slug' => 'skateboarding', 'count' => 23],
        ['name' => 'Training', 'slug' => 'training', 'count' => 19],
        ['name' => 'Sandals', 'slug' => 'sandals', 'count' => 15]
    ];
}

function getBrands() {
    return [
        ['name' => 'Nike', 'slug' => 'nike', 'count' => 56],
        ['name' => 'Adidas', 'slug' => 'adidas', 'count' => 42],
        ['name' => 'Jordan', 'slug' => 'jordan', 'count' => 28],
        ['name' => 'Puma', 'slug' => 'puma', 'count' => 23],
        ['name' => 'New Balance', 'slug' => 'new-balance', 'count' => 31],
        ['name' => 'Vans', 'slug' => 'vans', 'count' => 27],
        ['name' => 'Converse', 'slug' => 'converse', 'count' => 18],
        ['name' => 'Reebok', 'slug' => 'reebok', 'count' => 15]
    ];
function authenticateUser($email, $password) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT id, first_name, last_name, email, password_hash, role, status 
                FROM users 
                WHERE email = ? AND status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $update_sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->execute([$user['id']]);
            
            return $user;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

function userExists($email) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("User exists check error: " . $e->getMessage());
        return false;
    }
}

function registerUser($user_data) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Hash password
        $hashed_password = password_hash($user_data['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (first_name, last_name, email, phone, password_hash, newsletter) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_data['first_name'],
            $user_data['last_name'],
            $user_data['email'],
            $user_data['phone'],
            $hashed_password,
            $user_data['newsletter']
        ]);
        
        return $conn->lastInsertId();
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

function getUserById($user_id) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT id, first_name, last_name, email, phone, role, newsletter, created_at, last_login 
                FROM users 
                WHERE id = ? AND status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Get user error: " . $e->getMessage());
        return false;
    }
}

function updateUserProfile($user_id, $user_data) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, newsletter = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_data['first_name'],
            $user_data['last_name'],
            $user_data['phone'],
            $user_data['newsletter'],
            $user_id
        ]);
        
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Update profile error: " . $e->getMessage());
        return false;
    }
}

function changePassword($user_id, $current_password, $new_password) {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Verify current password
        $sql = "SELECT password_hash FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            return false;
        }
        
        // Update password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([$new_hashed_password, $user_id]);
        
        return $update_stmt->rowCount() > 0;
    } catch (Exception $e) {
        error_log("Change password error: " . $e->getMessage());
        return false;
    }
}
}
?>