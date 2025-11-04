<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Payment Processing - StepStyle';
$page_description = 'Secure payment processing for your order.';
$body_class = 'payment-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if order data exists
if (!isset($_SESSION['current_order'])) {
    header('Location: checkout.php');
    exit();
}

$order_data = $_SESSION['current_order'];
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
    <link rel="stylesheet" href="../assets/css/payment.css">
    
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
            <a href="cart.php">Cart</a>
            <i class="fas fa-chevron-right"></i>
            <a href="checkout.php">Checkout</a>
            <i class="fas fa-chevron-right"></i>
            <span>Payment</span>
        </nav>

        <div class="payment-layout">
            <!-- Payment Header -->
            <div class="payment-header">
                <h1 class="page-title">Payment Processing</h1>
                <div class="payment-steps">
                    <div class="step completed" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label">Shipping</span>
                    </div>
                    <div class="step completed" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label">Payment</span>
                    </div>
                    <div class="step active" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label">Confirmation</span>
                    </div>
                </div>
            </div>

            <div class="payment-content">
                <!-- Payment Processing -->
                <div class="payment-processing">
                    <div class="processing-animation">
                        <div class="spinner"></div>
                        <i class="fas fa-shield-alt security-icon"></i>
                    </div>
                    
                    <h2>Processing Your Payment</h2>
                    <p>Please wait while we securely process your payment. Do not refresh or close this page.</p>
                    
                    <div class="processing-details">
                        <div class="detail-item">
                            <span class="detail-label">Order Total:</span>
                            <span class="detail-value">$<?php echo number_format($order_data['total'], 2); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Payment Method:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($order_data['payment_method']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Order ID:</span>
                            <span class="detail-value">#<?php echo $order_data['order_id']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary-payment">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <div class="order-items-payment">
                        <?php foreach ($order_data['items'] as $item): ?>
                        <div class="order-item-payment">
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
                                <h4 class="item-name"><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p class="item-brand"><?php echo htmlspecialchars($item['brand']); ?></p>
                                <p class="item-size">Size: <?php echo $item['size']; ?></p>
                                <p class="item-quantity">Qty: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="item-price">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="summary-details-payment">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($order_data['subtotal'], 2); ?></span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>
                                <?php if ($order_data['shipping_cost'] == 0): ?>
                                    FREE
                                <?php else: ?>
                                    $<?php echo number_format($order_data['shipping_cost'], 2); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="summary-row">
                            <span>Tax</span>
                            <span>$<?php echo number_format($order_data['tax'], 2); ?></span>
                        </div>
                        
                        <?php if ($order_data['discount'] > 0): ?>
                        <div class="summary-row discount">
                            <span>Discount</span>
                            <span class="discount-amount">-$<?php echo number_format($order_data['discount'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="summary-divider"></div>
                        
                        <div class="summary-row total">
                            <span><strong>Total</strong></span>
                            <span><strong>$<?php echo number_format($order_data['total'], 2); ?></strong></span>
                        </div>
                    </div>
                    
                    <div class="shipping-info-payment">
                        <h4>Shipping to:</h4>
                        <p>
                            <?php echo htmlspecialchars($order_data['shipping_address']['full_name']); ?><br>
                            <?php echo htmlspecialchars($order_data['shipping_address']['address']); ?><br>
                            <?php echo htmlspecialchars($order_data['shipping_address']['city']); ?>, 
                            <?php echo htmlspecialchars($order_data['shipping_address']['state']); ?> 
                            <?php echo htmlspecialchars($order_data['shipping_address']['zip_code']); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Security Features -->
            <div class="security-features">
                <div class="security-item">
                    <i class="fas fa-lock"></i>
                    <span>256-bit SSL Encryption</span>
                </div>
                <div class="security-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>PCI DSS Compliant</span>
                </div>
                <div class="security-item">
                    <i class="fas fa-user-shield"></i>
                    <span>Fraud Protection</span>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include '../components/footer.php'; ?>

<!-- JavaScript -->
<script src="../assets/js/main.js"></script>
<script src="../assets/js/payment.js"></script>

<script>
// Simulate payment processing
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        // Redirect to order confirmation after 3 seconds
        window.location.href = 'order-confirmation.php?order_id=<?php echo $order_data['order_id']; ?>';
    }, 3000);
});
</script>

</body>
</html>