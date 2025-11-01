<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    redirect('../auth/login.php');
}

$user_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();

// Demo data
$cart_items = [
    [
        'name' => 'Nike Air Max 270',
        'brand' => 'Nike',
        'price' => 15990,
        'quantity' => 1,
        'size' => 'US 9',
        'color' => 'Black'
    ],
    [
        'name' => 'Adidas Ultraboost 21', 
        'brand' => 'Adidas',
        'price' => 22990,
        'quantity' => 2,
        'size' => 'US 10',
        'color' => 'Blue'
    ]
];

$subtotal = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $cart_items));

$shipping = 0; // Free shipping over $50
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - StepStyle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="checkout-container">
        <div class="container">
            <div class="checkout-header">
                <h1>Checkout</h1>
                <div class="checkout-steps">
                    <div class="step completed">
                        <span class="step-number">1</span>
                        <span class="step-text">Cart</span>
                    </div>
                    <div class="step active">
                        <span class="step-number">2</span>
                        <span class="step-text">Information</span>
                    </div>
                    <div class="step">
                        <span class="step-number">3</span>
                        <span class="step-text">Shipping</span>
                    </div>
                    <div class="step">
                        <span class="step-number">4</span>
                        <span class="step-text">Payment</span>
                    </div>
                </div>
            </div>

            <div class="checkout-content">
                <div class="checkout-form-section">
                    <form class="checkout-form" id="checkout-form">
                        <!-- Contact Information -->
                        <div class="form-section">
                            <h3>Contact Information</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" value="demo@stepstyle.com" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" value="+1 (555) 123-4567" required>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Address -->
                        <div class="form-section">
                            <h3>Shipping Address</h3>
                            <div class="address-options">
                                <label class="radio-option">
                                    <input type="radio" name="address-type" value="residential" checked>
                                    <span class="radio-checkmark"></span>
                                    Residential Address
                                </label>
                                <label class="radio-option">
                                    <input type="radio" name="address-type" value="business">
                                    <span class="radio-checkmark"></span>
                                    Business Address
                                </label>
                            </div>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="first-name">First Name *</label>
                                    <input type="text" id="first-name" name="first_name" value="John" required>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Last Name *</label>
                                    <input type="text" id="last-name" name="last_name" value="Doe" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Street Address *</label>
                                <input type="text" id="address" name="address" value="123 Main Street" required>
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" value="New York" required>
                                </div>
                                <div class="form-group">
                                    <label for="state">State *</label>
                                    <select id="state" name="state" required>
                                        <option value="NY" selected>New York</option>
                                        <option value="CA">California</option>
                                        <option value="TX">Texas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="zip">ZIP Code *</label>
                                    <input type="text" id="zip" name="zip_code" value="10001" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="country">Country *</label>
                                <select id="country" name="country" required>
                                    <option value="US" selected>United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="UK">United Kingdom</option>
                                </select>
                            </div>
                        </div>

                        <!-- Shipping Method -->
                        <div class="form-section">
                            <h3>Shipping Method</h3>
                            <div class="shipping-options">
                                <label class="shipping-option">
                                    <input type="radio" name="shipping" value="standard" checked>
                                    <span class="radio-checkmark"></span>
                                    <div class="shipping-info">
                                        <span class="shipping-name">Standard Shipping</span>
                                        <span class="shipping-time">5-7 business days</span>
                                    </div>
                                    <span class="shipping-price">FREE</span>
                                </label>
                                <label class="shipping-option">
                                    <input type="radio" name="shipping" value="express">
                                    <span class="radio-checkmark"></span>
                                    <div class="shipping-info">
                                        <span class="shipping-name">Express Shipping</span>
                                        <span class="shipping-time">2-3 business days</span>
                                    </div>
                                    <span class="shipping-price">$9.99</span>
                                </label>
                                <label class="shipping-option">
                                    <input type="radio" name="shipping" value="overnight">
                                    <span class="radio-checkmark"></span>
                                    <div class="shipping-info">
                                        <span class="shipping-name">Overnight Shipping</span>
                                        <span class="shipping-time">Next business day</span>
                                    </div>
                                    <span class="shipping-price">$19.99</span>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-section">
                            <h3>Payment Method</h3>
                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="card" checked>
                                    <span class="radio-checkmark"></span>
                                    <i class="fab fa-cc-visa"></i>
                                    <span>Credit/Debit Card</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="paypal">
                                    <span class="radio-checkmark"></span>
                                    <i class="fab fa-paypal"></i>
                                    <span>PayPal</span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment" value="applepay">
                                    <span class="radio-checkmark"></span>
                                    <i class="fab fa-apple-pay"></i>
                                    <span>Apple Pay</span>
                                </label>
                            </div>

                            <div class="card-form" id="card-form">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="card-number">Card Number *</label>
                                        <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456">
                                        <i class="fab fa-cc-visa card-icon"></i>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="card-name">Name on Card *</label>
                                        <input type="text" id="card-name" name="card_name" placeholder="John Doe">
                                    </div>
                                    <div class="form-group">
                                        <label for="card-expiry">Expiry Date *</label>
                                        <input type="text" id="card-expiry" name="card_expiry" placeholder="MM/YY">
                                    </div>
                                    <div class="form-group">
                                        <label for="card-cvv">CVV *</label>
                                        <input type="text" id="card-cvv" name="card_cvv" placeholder="123">
                                        <i class="fas fa-question-circle cvv-help" title="3-digit code on back of card"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="form-section">
                            <h3>Order Notes (Optional)</h3>
                            <div class="form-group">
                                <textarea id="order-notes" name="order_notes" placeholder="Add special instructions for your order..." rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="checkout-summary-section">
                    <div class="summary-card">
                        <h3>Order Summary</h3>
                        
                        <div class="order-items">
                            <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <i class="fas fa-shoe-prints"></i>
                                </div>
                                <div class="item-info">
                                    <span class="item-name"><?php echo $item['name']; ?></span>
                                    <span class="item-details">Size: <?php echo $item['size']; ?> | Color: <?php echo $item['color']; ?></span>
                                    <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                                </div>
                                <span class="item-price"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="summary-details">
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
                        </div>

                        <div class="promo-section">
                            <div class="promo-input">
                                <input type="text" placeholder="Promo code">
                                <button class="btn btn-outline">Apply</button>
                            </div>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary btn-place-order">
                            <i class="fas fa-lock"></i>
                            Place Order
                        </button>

                        <div class="security-assurance">
                            <i class="fas fa-shield-alt"></i>
                            <span>Your payment information is secure and encrypted</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../assets/js/checkout.js"></script>
</body>
</html>