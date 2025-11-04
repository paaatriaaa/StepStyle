<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Checkout - StepStyle';
$page_description = 'Complete your purchase securely. Enter shipping and payment information.';
$body_class = 'checkout-page';

// Include configuration
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if cart is empty
$cart_items = getCartItems();
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

// Get checkout data
$cart_total = calculateCartTotal($cart_items);
$user_data = getCurrentUser();
$shipping_methods = getShippingMethods();
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
    <link rel="stylesheet" href="../assets/css/checkout.css">
    
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
            <span>Checkout</span>
        </nav>

        <div class="checkout-layout">
            <!-- Checkout Header -->
            <div class="checkout-header">
                <h1 class="page-title">Checkout</h1>
                <div class="checkout-steps">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-label">Shipping</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-label">Payment</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-label">Confirmation</span>
                    </div>
                </div>
            </div>

            <div class="checkout-content">
                <!-- Checkout Form -->
                <div class="checkout-form-section">
                    <form id="checkout-form" class="checkout-form">
                        <!-- Shipping Information -->
                        <section class="form-section active" id="shipping-section">
                            <h2 class="section-title">Shipping Information</h2>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="shipping-email">Email Address *</label>
                                    <input type="email" id="shipping-email" name="shipping_email" required 
                                           value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="shipping-phone">Phone Number *</label>
                                    <input type="tel" id="shipping-phone" name="shipping_phone" required
                                           value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="shipping-fullname">Full Name *</label>
                                <input type="text" id="shipping-fullname" name="shipping_fullname" required
                                       value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="shipping-address">Street Address *</label>
                                <input type="text" id="shipping-address" name="shipping_address" required
                                       value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="shipping-city">City *</label>
                                    <input type="text" id="shipping-city" name="shipping_city" required
                                           value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="shipping-state">State *</label>
                                    <select id="shipping-state" name="shipping_state" required>
                                        <option value="">Select State</option>
                                        <option value="CA" <?php echo ($user_data['state'] ?? '') === 'CA' ? 'selected' : ''; ?>>California</option>
                                        <option value="NY" <?php echo ($user_data['state'] ?? '') === 'NY' ? 'selected' : ''; ?>>New York</option>
                                        <option value="TX" <?php echo ($user_data['state'] ?? '') === 'TX' ? 'selected' : ''; ?>>Texas</option>
                                        <option value="FL" <?php echo ($user_data['state'] ?? '') === 'FL' ? 'selected' : ''; ?>>Florida</option>
                                        <!-- Add more states as needed -->
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="shipping-zip">ZIP Code *</label>
                                    <input type="text" id="shipping-zip" name="shipping_zip" required
                                           value="<?php echo htmlspecialchars($user_data['zip_code'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="shipping-country">Country *</label>
                                <select id="shipping-country" name="shipping_country" required>
                                    <option value="US" selected>United States</option>
                                </select>
                            </div>

                            <!-- Shipping Method -->
                            <div class="shipping-methods">
                                <h3 class="methods-title">Shipping Method</h3>
                                <?php foreach ($shipping_methods as $method): ?>
                                <div class="shipping-method">
                                    <input type="radio" 
                                           id="shipping-<?php echo $method['id']; ?>" 
                                           name="shipping_method" 
                                           value="<?php echo $method['id']; ?>" 
                                           <?php echo $method['default'] ? 'checked' : ''; ?>
                                           data-cost="<?php echo $method['cost']; ?>">
                                    <label for="shipping-<?php echo $method['id']; ?>">
                                        <span class="method-name"><?php echo $method['name']; ?></span>
                                        <span class="method-details"><?php echo $method['description']; ?></span>
                                        <span class="method-cost">
                                            <?php if ($method['cost'] == 0): ?>
                                                FREE
                                            <?php else: ?>
                                                $<?php echo number_format($method['cost'], 2); ?>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="form-actions">
                                <a href="cart.php" class="btn btn-outline">
                                    <i class="fas fa-arrow-left"></i>
                                    Back to Cart
                                </a>
                                <button type="button" class="btn btn-primary next-step" data-next="payment">
                                    Continue to Payment
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </section>

                        <!-- Payment Information -->
                        <section class="form-section" id="payment-section">
                            <h2 class="section-title">Payment Information</h2>
                            
                            <!-- Payment Method Selection -->
                            <div class="payment-methods">
                                <div class="payment-method-tabs">
                                    <button type="button" class="payment-tab active" data-method="card">
                                        <i class="fas fa-credit-card"></i>
                                        Credit Card
                                    </button>
                                    <button type="button" class="payment-tab" data-method="paypal">
                                        <i class="fab fa-paypal"></i>
                                        PayPal
                                    </button>
                                    <button type="button" class="payment-tab" data-method="applepay">
                                        <i class="fab fa-apple-pay"></i>
                                        Apple Pay
                                    </button>
                                </div>

                                <!-- Credit Card Form -->
                                <div class="payment-form active" id="card-form">
                                    <div class="form-group">
                                        <label for="card-number">Card Number *</label>
                                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                        <div class="card-icons">
                                            <i class="fab fa-cc-visa"></i>
                                            <i class="fab fa-cc-mastercard"></i>
                                            <i class="fab fa-cc-amex"></i>
                                            <i class="fab fa-cc-discover"></i>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="card-expiry">Expiry Date *</label>
                                            <input type="text" id="card-expiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="card-cvc">CVC *</label>
                                            <input type="text" id="card-cvc" name="card_cvc" placeholder="123" maxlength="4">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="card-name">Name on Card *</label>
                                        <input type="text" id="card-name" name="card_name">
                                    </div>
                                </div>

                                <!-- PayPal Form -->
                                <div class="payment-form" id="paypal-form">
                                    <div class="paypal-info">
                                        <p>You will be redirected to PayPal to complete your payment securely.</p>
                                        <button type="button" class="btn btn-paypal">
                                            <i class="fab fa-paypal"></i>
                                            Continue with PayPal
                                        </button>
                                    </div>
                                </div>

                                <!-- Apple Pay Form -->
                                <div class="payment-form" id="applepay-form">
                                    <div class="applepay-info">
                                        <p>Complete your purchase quickly and securely with Apple Pay.</p>
                                        <button type="button" class="btn btn-applepay">
                                            <i class="fab fa-apple-pay"></i>
                                            Pay with Apple Pay
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Address -->
                            <div class="billing-address">
                                <div class="form-check">
                                    <input type="checkbox" id="same-as-shipping" name="same_as_shipping" checked>
                                    <label for="same-as-shipping">Billing address same as shipping address</label>
                                </div>

                                <div id="billing-fields" style="display: none;">
                                    <h3 class="section-subtitle">Billing Address</h3>
                                    <div class="form-group">
                                        <label for="billing-address">Street Address</label>
                                        <input type="text" id="billing-address" name="billing_address">
                                    </div>
                                    
                                    <div class="form-grid">
                                        <div class="form-group">
                                            <label for="billing-city">City</label>
                                            <input type="text" id="billing-city" name="billing_city">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="billing-state">State</label>
                                            <select id="billing-state" name="billing_state">
                                                <option value="">Select State</option>
                                                <option value="CA">California</option>
                                                <option value="NY">New York</option>
                                                <option value="TX">Texas</option>
                                                <option value="FL">Florida</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="billing-zip">ZIP Code</label>
                                            <input type="text" id="billing-zip" name="billing_zip">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="btn btn-out