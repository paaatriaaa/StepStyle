<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    redirect('../auth/login.php');
}

$database = new Database();
$db = $database->getConnection();

// Demo cart data
$cart_items = [
    [
        'id' => 1,
        'product_id' => 1,
        'name' => 'Nike Air Max 270',
        'brand' => 'Nike',
        'price' => 15990,
        'image' => '../../assets/images/products/nike-air-max-270.jpg',
        'quantity' => 1,
        'size' => 'US 9',
        'color' => 'Black'
    ],
    [
        'id' => 2,
        'product_id' => 2,
        'name' => 'Adidas Ultraboost 21',
        'brand' => 'Adidas',
        'price' => 22990,
        'image' => '../../assets/images/products/ultraboost-21.jpg',
        'quantity' => 2,
        'size' => 'US 10',
        'color' => 'Blue'
    ]
];

$subtotal = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart_items));

$shipping = $subtotal > 5000 ? 0 : 1000;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - StepStyle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="cart-container">
        <div class="container">
            <div class="cart-header">
                <h1>Shopping Cart</h1>
                <div class="cart-steps">
                    <div class="step active">
                        <span class="step-number">1</span>
                        <span class="step-text">Cart</span>
                    </div>
                    <div class="step">
                        <span class="step-number">2</span>
                        <span class="step-text">Checkout</span>
                    </div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <span class="step-text">Payment</span>
                    </div>
                    <div class="step">
                        <span class="step-number">4</span>
                        <span class="step-text">Confirmation</span>
                    </div>
                </div>
            </div>

            <div class="cart-content">
                <div class="cart-items-section">
                    <?php if (empty($cart_items)): ?>
                        <div class="empty-cart">
                            <div class="empty-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h2>Your cart is empty</h2>
                            <p>Discover our amazing collection and add some items to your cart</p>
                            <a href="../products/categories/sneakers.php" class="btn btn-primary">
                                <i class="fas fa-shoe-prints"></i>
                                Start Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item" data-cart-id="<?php echo $item['id']; ?>">
                                <div class="item-image">
                                    <div class="image-placeholder">
                                        <i class="fas fa-shoe-prints"></i>
                                    </div>
                                </div>
                                
                                <div class="item-details">
                                    <h3 class="item-name"><?php echo $item['name']; ?></h3>
                                    <p class="item-brand"><?php echo $item['brand']; ?></p>
                                    <div class="item-options">
                                        <?php if ($item['size']): ?>
                                            <span class="item-option">Size: <?php echo $item['size']; ?></span>
                                        <?php endif; ?>
                                        <?php if ($item['color']): ?>
                                            <span class="item-option">Color: <?php echo $item['color']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-availability">
                                        <i class="fas fa-check-circle"></i>
                                        <span>In Stock - Ready to Ship</span>
                                    </div>
                                </div>

                                <div class="item-price">
                                    <span class="price"><?php echo formatPrice($item['price']); ?></span>
                                </div>

                                <div class="item-quantity">
                                    <div class="quantity-controls">
                                        <button class="quantity-btn minus" data-action="decrease">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="10">
                                        <button class="quantity-btn plus" data-action="increase">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <button class="btn-save-later">
                                        <i class="far fa-heart"></i>
                                        Save for later
                                    </button>
                                </div>

                                <div class="item-total">
                                    <span class="total"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                                </div>

                                <div class="item-actions">
                                    <button class="btn-remove" data-cart-id="<?php echo $item['id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-promo">
                            <div class="promo-input">
                                <input type="text" placeholder="Enter promo code">
                                <button class="btn btn-secondary">Apply</button>
                            </div>
                            <div class="promo-suggestions">
                                <span>Suggested: </span>
                                <button class="promo-tag">WELCOME15</button>
                                <button class="promo-tag">FREESHIP</button>
                                <button class="promo-tag">SAVE20</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cart-summary-section">
                    <div class="summary-card">
                        <h3>Order Summary</h3>
                        
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span><?php echo $shipping == 0 ? 'FREE' : formatPrice($shipping); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Tax</span>
                            <span><?php echo formatPrice($tax); ?></span>
                        </div>

                        <div class="summary-divider"></div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span><?php echo formatPrice($total); ?></span>
                        </div>

                        <div class="shipping-notice">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Free shipping on orders over $50</span>
                        </div>

                        <button class="btn btn-primary btn-checkout" onclick="window.location.href='checkout.php'">
                            <i class="fas fa-lock"></i>
                            Proceed to Checkout
                        </button>

                        <div class="payment-methods">
                            <span>We accept:</span>
                            <div class="payment-icons">
                                <i class="fab fa-cc-visa"></i>
                                <i class="fab fa-cc-mastercard"></i>
                                <i class="fab fa-cc-amex"></i>
                                <i class="fab fa-cc-paypal"></i>
                                <i class="fab fa-apple-pay"></i>
                            </div>
                        </div>
                    </div>

                    <div class="security-badge">
                        <i class="fas fa-shield-alt"></i>
                        <div>
                            <strong>Secure Checkout</strong>
                            <span>Your information is protected</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($cart_items)): ?>
            <div class="recently-viewed">
                <h3>Recently Viewed</h3>
                <div class="recent-products">
                    <!-- Recently viewed products would go here -->
                    <div class="recent-product">
                        <div class="recent-image">
                            <i class="fas fa-shoe-prints"></i>
                        </div>
                        <div class="recent-info">
                            <span class="recent-brand">Puma</span>
                            <span class="recent-name">RS-X Toys</span>
                            <span class="recent-price">$119.99</span>
                        </div>
                    </div>
                    <div class="recent-product">
                        <div class="recent-image">
                            <i class="fas fa-shoe-prints"></i>
                        </div>
                        <div class="recent-info">
                            <span class="recent-brand">New Balance</span>
                            <span class="recent-name">574 Core</span>
                            <span class="recent-price">$129.99</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/cart.js"></script>
</body>
</html>