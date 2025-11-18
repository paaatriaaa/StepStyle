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

// Check if user is logged in
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Please log in to view your cart.';
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// PERBAIKAN: Tambahkan parameter $user_id ke semua fungsi
$cart_items = getCartItems($user_id);
$cart_total = calculateCartTotal($user_id);
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
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    
    <style>
        /* Cart Styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 2rem 0;
            font-size: 0.9rem;
            color: #666;
        }
        
        .breadcrumb a {
            color: #666;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            color: #3b82f6;
        }
        
        .cart-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .cart-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }
        
        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .cart-items-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-count {
            font-weight: 600;
            color: #374151;
        }
        
        .continue-shopping {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        
        .continue-shopping:hover {
            text-decoration: underline;
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 1rem;
            background: white;
        }
        
        .item-image img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .item-image-placeholder {
            width: 100%;
            height: 120px;
            background: #f3f4f6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 2rem;
        }
        
        .item-details {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 1rem;
            align-items: start;
        }
        
        .item-info h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
        }
        
        .item-info a {
            color: #1f2937;
            text-decoration: none;
        }
        
        .item-info a:hover {
            color: #3b82f6;
        }
        
        .item-brand {
            color: #6b7280;
            margin: 0 0 0.5rem 0;
            font-size: 0.9rem;
        }
        
        .item-price {
            text-align: center;
        }
        
        .current-price {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
            display: block;
        }
        
        .original-price {
            font-size: 0.9rem;
            color: #9ca3af;
            text-decoration: line-through;
            display: block;
        }
        
        .item-quantity {
            text-align: center;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .quantity-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .quantity-btn:hover {
            background: #f9fafb;
        }
        
        .quantity-input {
            width: 60px;
            padding: 6px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            text-align: center;
        }
        
        .item-total {
            text-align: center;
        }
        
        .total-label {
            display: block;
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .total-price {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
        }
        
        .item-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: #6b7280;
        }
        
        .action-btn:hover {
            background: #f9fafb;
        }
        
        .cart-summary {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            position: sticky;
            top: 2rem;
        }
        
        .summary-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .summary-details {
            margin-bottom: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .summary-row.total {
            border-bottom: none;
            font-size: 1.1rem;
            padding-top: 1rem;
            border-top: 2px solid #e5e7eb;
        }
        
        .discount-amount {
            color: #10b981;
        }
        
        .shipping-notice {
            margin: 1.5rem 0;
            padding: 1rem;
            background: #f0f9ff;
            border-radius: 6px;
            border: 1px solid #bae6fd;
        }
        
        .shipping-progress .progress-bar {
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: #3b82f6;
            border-radius: 3px;
        }
        
        .free-shipping-achieved {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #10b981;
            font-weight: 500;
        }
        
        .checkout-actions {
            margin: 1.5rem 0;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-block {
            width: 100%;
        }
        
        .btn-large {
            padding: 15px 24px;
            font-size: 1.1rem;
        }
        
        .payment-methods {
            text-align: center;
            margin: 1rem 0;
            padding: 1rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .payment-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 1.5rem;
            color: #6b7280;
        }
        
        .security-notice {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
        
        .trust-badges {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .trust-badge i {
            color: #3b82f6;
            font-size: 1.25rem;
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }
        
        .empty-cart h2 {
            margin-bottom: 1rem;
            color: #374151;
        }
        
        .empty-cart p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        
        .recently-viewed-section {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .product-image img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .product-title {
            font-size: 0.9rem;
            margin: 0 0 0.25rem;
            font-weight: 600;
        }
        
        .product-brand {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 0 0 0.5rem;
        }
        
        .product-price {
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }
        
        @media (max-width: 768px) {
            .cart-content {
                grid-template-columns: 1fr;
            }
            
            .item-details {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .item-price, .item-quantity, .item-total {
                text-align: left;
            }
            
            .item-actions {
                flex-direction: row;
                justify-content: flex-start;
            }
        }
    </style>
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
                                    </div>
                                    
                                    <div class="item-price">
                                        <span class="current-price">$<?php echo number_format($item['price'], 2); ?></span>
                                        <?php if (isset($item['original_price']) && $item['original_price'] > $item['price']): ?>
                                            <span class="original-price">$<?php echo number_format($item['original_price'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="item-quantity">
                                        <label for="quantity-<?php echo $item['id']; ?>">Qty:</label>
                                        <div class="quantity-selector">
                                            <button class="quantity-btn minus" data-action="decrease" data-product-id="<?php echo $item['id']; ?>">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" 
                                                   id="quantity-<?php echo $item['id']; ?>" 
                                                   class="quantity-input" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="<?php echo $item['stock_quantity']; ?>"
                                                   data-product-id="<?php echo $item['id']; ?>">
                                            <button class="quantity-btn plus" data-action="increase" data-product-id="<?php echo $item['id']; ?>">
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
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                                <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                            </div>
                        </div>
                        <?php
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading screen
    const loadingScreen = document.getElementById('global-loading');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 1000);
    }

    // Quantity buttons functionality
    document.querySelectorAll('.quantity-btn').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.dataset.action;
            const productId = this.dataset.productId;
            const input = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            let quantity = parseInt(input.value);

            if (action === 'increase') {
                quantity++;
            } else if (action === 'decrease' && quantity > 1) {
                quantity--;
            }

            input.value = quantity;
            updateItemTotal(productId, quantity);
        });
    });

    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            let quantity = parseInt(this.value);
            const max = parseInt(this.max);

            if (quantity < 1) quantity = 1;
            if (quantity > max) quantity = max;

            this.value = quantity;
            updateItemTotal(productId, quantity);
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            if (confirm('Are you sure you want to remove this item from your cart?')) {
                // Implement remove functionality here
                console.log('Remove item:', productId);
            }
        });
    });

    // Save for later
    document.querySelectorAll('.save-later').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            // Implement save for later functionality here
            console.log('Save for later:', productId);
        });
    });

    function updateItemTotal(productId, quantity) {
        const item = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
        const price = parseFloat(item.querySelector('.current-price').textContent.replace('$', ''));
        const totalElement = item.querySelector('.total-price');
        const total = price * quantity;
        
        totalElement.textContent = '$' + total.toFixed(2);
        
        // Update cart total (you would typically make an AJAX call here)
        console.log('Update quantity for product', productId, 'to', quantity);
    }
});
</script>

</body>
</html>