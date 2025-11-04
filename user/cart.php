<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Shopping Cart - StepStyle';
$page_description = 'Review your shopping cart items. Update quantities, remove items, or proceed to checkout.';
$body_class = 'cart-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Get cart items
$cart_items = getCartItems();
$cart_total = calculateCartTotal($cart_items);
$cart_count = count($cart_items);
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
    <link rel="stylesheet" href="../assets/css/cart.css">
    
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
            <span>Shopping Cart</span>
        </nav>

        <div class="cart-layout">
            <!-- Cart Header -->
            <div class="cart-header">
                <h1 class="page-title">Shopping Cart</h1>
                <p class="cart-subtitle">Review your items and proceed to checkout</p>
            </div>

            <div class="cart-content">
                <!-- Cart Items -->
                <div class="cart-items-section">
                    <?php if (!empty($cart_items)): ?>
                        <div class="cart-items-header">
                            <span class="items-count"><?php echo $cart_count; ?> item<?php echo $cart_count !== 1 ? 's' : ''; ?> in cart</span>
                            <a href="../products/categories.php" class="continue-shopping">
                                <i class="fas fa-arrow-left"></i>
                                Continue Shopping
                            </a>
                        </div>

                        <div class="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item" data-product-id="<?php echo $item['id']; ?>">
                                <div class="item-image">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <?php else: ?>
                                        <div class="item-image-placeholder">
                                            <i class="fas fa-shoe-prints"></i>
                                        </div>
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
                                        <p class="item-size">Size: <?php echo $item['size']; ?></p>
                                        <p class="item-color">Color: <?php echo $item['color']; ?></p>
                                    </div>
                                    
                                    <div class="item-price">
                                        <span class="current-price">$<?php echo number_format($item['price'], 2); ?></span>
                                        <?php if ($item['original_price'] > $item['price']): ?>
                                            <span class="original-price">$<?php echo number_format($item['original_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="item-quantity">
                                        <label for="quantity-<?php echo $item['id']; ?>">Qty:</label>
                                        <div class="quantity-selector">
                                            <button class="quantity-btn minus" data-action="decrease">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   id="quantity-<?php echo $item['id']; ?>" 
                                                   class="quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="<?php echo $item['stock_quantity']; ?>"
                                                   data-product-id="<?php echo $item['id']; ?>">
                                            <button class="quantity-btn plus" data-action="increase">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="item-total">
                                        <span class="total-label">Total:</span>
                                        <span class="total-price">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                    
                                    <div class="item-actions">
                                        <button class="action-btn save-later" data-product-id="<?php echo $item['id']; ?>">
                                            <i class="far fa-heart"></i>
                                            Save for Later
                                        </button>
                                        <button class="action-btn remove-item" data-product-id="<?php echo $item['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Empty Cart State -->
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h2>Your cart is empty</h2>
                            <p>Looks like you haven't added any items to your cart yet.</p>
                            <a href="../products/categories.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag"></i>
                                Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Cart Summary -->
                <?php if (!empty($cart_items)): ?>
                <div class="cart-summary-section">
                    <div class="cart-summary">
                        <h3 class="summary-title">Order Summary</h3>
                        
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Subtotal (<?php echo $cart_count; ?> items)</span>
                                <span>$<?php echo number_format($cart_total['subtotal'], 2); ?></span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span class="shipping-cost">
                                    <?php if ($cart_total['subtotal'] >= 50): ?>
                                        <span class="free-shipping">FREE</span>
                                    <?php else: ?>
                                        $<?php echo number_format($cart_total['shipping'], 2); ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                            
                            <div class="summary-row">
                                <span>Tax</span>
                                <span>$<?php echo number_format($cart_total['tax'], 2); ?></span>
                            </div>
                            
                            <?php if ($cart_total['discount'] > 0): ?>
                            <div class="summary-row discount">
                                <span>Discount</span>
                                <span class="discount-amount">-$<?php echo number_format($cart_total['discount'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="summary-divider"></div>
                            
                            <div class="summary-row total">
                                <span><strong>Total</strong></span>
                                <span><strong>$<?php echo number_format($cart_total['total'], 2); ?></strong></span>
                            </div>
                        </div>
                        
                        <div class="shipping-notice">
                            <?php if ($cart_total['subtotal'] < 50): ?>
                                <div class="shipping-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo ($cart_total['subtotal'] / 50) * 100; ?>%"></div>
                                    </div>
                                    <p>Add $<?php echo number_format(50 - $cart_total['subtotal'], 2); ?> more for <strong>FREE shipping</strong></p>
                                </div>
                            <?php else: ?>
                                <div class="free-shipping-achieved">
                                    <i class="fas fa-check-circle"></i>
                                    <span>You've qualified for FREE shipping!</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="checkout-actions">
                            <a href="checkout.php" class="btn btn-primary btn-block btn-large">
                                <i class="fas fa-lock"></i>
                                Proceed to Checkout
                            </a>
                            
                            <div class="payment-methods">
                                <p>We accept:</p>
                                <div class="payment-icons">
                                    <i class="fab fa-cc-visa" title="Visa"></i>
                                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                                    <i class="fab fa-cc-amex" title="American Express"></i>
                                    <i class="fab fa-cc-paypal" title="PayPal"></i>
                                    <i class="fab fa-apple-pay" title="Apple Pay"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="security-notice">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure checkout. Your information is safe with us.</span>
                        </div>
                    </div>
                    
                    <!-- Trust Badges -->
                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Free Shipping on $50+</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-undo"></i>
                            <span>30-Day Returns</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Authentic</span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Recently Viewed -->
            <?php if (!empty($cart_items)): ?>
            <div class="recently-viewed-section">
                <h2 class="section-title">You Might Also Like</h2>
                <div class="products-grid">
                    <?php
                    $recommended_products = getRecommendedProducts(4);
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
<script src="../assets/js/cart.js"></script>

</body>
</html>