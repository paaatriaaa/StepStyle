<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Track Your Order - StepStyle';
$page_description = 'Track your StepStyle order in real-time. Get updates on shipping status, delivery estimates, and order progress.';
$body_class = 'track-order-page';

// Include configuration
require_once 'config/database.php';
require_once 'config/functions.php';
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
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <style>
        /* Track Order Page Styles */
        .track-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0 3rem;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .section-padding {
            padding: 4rem 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Track Form */
        .track-form-section {
            background: white;
            padding: 3rem 0;
        }
        
        .track-form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
            text-align: center;
        }
        
        .form-subtitle {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .track-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 600;
            color: #374151;
            font-size: 0.95rem;
        }
        
        .form-group input {
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-note {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
            margin-top: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
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
            transform: translateY(-1px);
        }
        
        .btn-block {
            width: 100%;
        }
        
        .btn-large {
            padding: 15px 24px;
            font-size: 1.1rem;
        }
        
        /* Order Status */
        .order-status-section {
            background: #f8fafc;
        }
        
        .status-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .order-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .order-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .order-date {
            color: #6b7280;
            font-size: 1rem;
        }
        
        .status-timeline {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .status-timeline::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 20px;
            bottom: 40px;
            width: 2px;
            background: #e5e7eb;
            z-index: 1;
        }
        
        .status-step {
            display: flex;
            gap: 2rem;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 2;
        }
        
        .status-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            position: relative;
        }
        
        .status-icon.completed {
            background: #10b981;
            color: white;
        }
        
        .status-icon.current {
            background: #3b82f6;
            color: white;
            animation: pulse 2s infinite;
        }
        
        .status-icon.pending {
            background: #d1d5db;
            color: #6b7280;
        }
        
        .status-content {
            flex: 1;
            padding-top: 0.5rem;
        }
        
        .status-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .status-description {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .status-time {
            font-size: 0.8rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }
        
        /* Order Details */
        .order-details {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 1.5rem;
        }
        
        .detail-card {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .detail-title {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-content {
            color: #1f2937;
            font-size: 1rem;
            line-height: 1.5;
        }
        
        /* Help Section */
        .help-section {
            background: white;
            text-align: center;
        }
        
        .help-content {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .help-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .help-description {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .help-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-outline {
            background: transparent;
            color: #3b82f6;
            border: 2px solid #3b82f6;
        }
        
        .btn-outline:hover {
            background: #3b82f6;
            color: white;
            transform: translateY(-1px);
        }
        
        /* Sample Order Data (Hidden by default) */
        .sample-result {
            display: none;
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            color: #0369a1;
        }
        
        .sample-result.show {
            display: block;
        }
        
        /* Animations */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(59, 130, 246, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .track-form-container,
            .status-container {
                padding: 2rem;
                margin: 0 1rem;
            }
            
            .status-step {
                gap: 1.5rem;
            }
            
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .help-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .section-padding {
                padding: 3rem 0;
            }
            
            .track-form-container,
            .status-container {
                padding: 1.5rem;
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
<?php include 'components/header.php'; ?>

<!-- Mobile Navigation -->
<?php include 'components/navigation.php'; ?>

<main class="main-content">
    <!-- Hero Section -->
    <section class="track-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Track Your Order</h1>
                <p class="hero-subtitle">Get real-time updates on your StepStyle order status and delivery progress</p>
            </div>
        </div>
    </section>

    <!-- Track Form Section -->
    <section class="track-form-section section-padding">
        <div class="container">
            <div class="track-form-container">
                <h2 class="form-title">Enter Your Order Details</h2>
                <p class="form-subtitle">Please enter your order number and email address to track your order status</p>
                
                <form class="track-form" id="track-order-form">
                    <div class="form-group">
                        <label for="order-number">Order Number *</label>
                        <input type="text" id="order-number" name="order_number" placeholder="e.g., STP-2024-00123" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block btn-large">
                        <i class="fas fa-search"></i>
                        Track Order
                    </button>
                </form>
                
                <p class="form-note">
                    Can't find your order number? Check your confirmation email or 
                    <a href="contact.php" style="color: #3b82f6; text-decoration: none;">contact our support team</a>
                </p>
                
                <!-- Sample result for demo purposes -->
                <div class="sample-result" id="sample-result">
                    <p><strong>Demo:</strong> Try order number "STP-2024-00123" with any email to see tracking example</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Status Section (Initially hidden) -->
    <section class="order-status-section section-padding" id="order-status-section" style="display: none;">
        <div class="container">
            <div class="status-container">
                <!-- Order Header -->
                <div class="order-header">
                    <div class="order-number" id="display-order-number">Order #STP-2024-00123</div>
                    <div class="order-date" id="display-order-date">Placed on January 15, 2024</div>
                </div>

                <!-- Status Timeline -->
                <div class="status-timeline">
                    <!-- Step 1: Order Confirmed -->
                    <div class="status-step">
                        <div class="status-icon completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-title">Order Confirmed</div>
                            <div class="status-description">Your order has been confirmed and is being processed</div>
                            <div class="status-time">Jan 15, 2024 • 10:30 AM</div>
                        </div>
                    </div>

                    <!-- Step 2: Processing -->
                    <div class="status-step">
                        <div class="status-icon completed">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-title">Processing</div>
                            <div class="status-description">Your items are being prepared for shipment</div>
                            <div class="status-time">Jan 15, 2024 • 2:15 PM</div>
                        </div>
                    </div>

                    <!-- Step 3: Shipped -->
                    <div class="status-step">
                        <div class="status-icon completed">
                            <i class="fas fa-shipping-fast"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-title">Shipped</div>
                            <div class="status-description">Your order has been shipped and is on its way</div>
                            <div class="status-time">Jan 16, 2024 • 9:45 AM</div>
                        </div>
                    </div>

                    <!-- Step 4: Out for Delivery -->
                    <div class="status-step">
                        <div class="status-icon current">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-title">Out for Delivery</div>
                            <div class="status-description">Your package is out for delivery today</div>
                            <div class="status-time">Expected delivery: Jan 18, 2024 • By 8:00 PM</div>
                        </div>
                    </div>

                    <!-- Step 5: Delivered -->
                    <div class="status-step">
                        <div class="status-icon pending">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="status-content">
                            <div class="status-title">Delivered</div>
                            <div class="status-description">Your order has been delivered</div>
                            <div class="status-time">Pending</div>
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="order-details">
                    <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 1.5rem; color: #1f2937;">Order Details</h3>
                    
                    <div class="details-grid">
                        <div class="detail-card">
                            <div class="detail-title">Shipping Address</div>
                            <div class="detail-content">
                                John Doe<br>
                                123 Main Street<br>
                                Los Angeles, CA 90001<br>
                                United States
                            </div>
                        </div>
                        
                        <div class="detail-card">
                            <div class="detail-title">Carrier & Tracking</div>
                            <div class="detail-content">
                                <strong>UPS</strong><br>
                                Tracking #: 1Z999AA1012345678<br>
                                <a href="#" style="color: #3b82f6; text-decoration: none;">View on UPS website</a>
                            </div>
                        </div>
                        
                        <div class="detail-card">
                            <div class="detail-title">Items Ordered</div>
                            <div class="detail-content">
                                Nike Air Jordan 1 Retro<br>
                                Size: US 10<br>
                                Qty: 1<br>
                                $180.00
                            </div>
                        </div>
                        
                        <div class="detail-card">
                            <div class="detail-title">Contact Support</div>
                            <div class="detail-content">
                                Need help?<br>
                                Email: support@stepstyle.com<br>
                                Phone: +1 (555) 123-4567
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Help Section -->
    <section class="help-section section-padding">
        <div class="container">
            <div class="help-content">
                <h2 class="help-title">Need Help With Your Order?</h2>
                <p class="help-description">
                    If you're experiencing issues with tracking your order or have any questions about your purchase, 
                    our support team is here to help you.
                </p>
                
                <div class="help-actions">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                    <a href="faq.php" class="btn btn-outline">
                        <i class="fas fa-question-circle"></i>
                        View FAQ
                    </a>
                    <a href="user/orders.php" class="btn btn-outline">
                        <i class="fas fa-history"></i>
                        Order History
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Footer -->
<?php include 'components/footer.php'; ?>

<!-- JavaScript -->
<script src="assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading screen
    const loadingScreen = document.getElementById('global-loading');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 1000);
    }

    const trackForm = document.getElementById('track-order-form');
    const orderStatusSection = document.getElementById('order-status-section');
    const sampleResult = document.getElementById('sample-result');
    const displayOrderNumber = document.getElementById('display-order-number');
    const displayOrderDate = document.getElementById('display-order-date');

    // Show sample result hint
    setTimeout(() => {
        sampleResult.classList.add('show');
    }, 2000);

    trackForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const orderNumber = document.getElementById('order-number').value;
        const email = document.getElementById('email').value;
        
        // Basic validation
        if (!orderNumber || !email) {
            alert('Please fill in all required fields.');
            return;
        }
        
        // For demo purposes - show tracking for sample order number
        if (orderNumber === 'STP-2024-00123') {
            // Update displayed order info
            displayOrderNumber.textContent = `Order #${orderNumber}`;
            displayOrderDate.textContent = 'Placed on January 15, 2024';
            
            // Show order status section
            orderStatusSection.style.display = 'block';
            
            // Scroll to order status
            orderStatusSection.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
            
            // Hide sample result
            sampleResult.classList.remove('show');
        } else {
            alert('Order not found. Please check your order number and email address. For demo, use: STP-2024-00123');
        }
    });

    // Add animation to status steps
    const statusSteps = document.querySelectorAll('.status-step');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateX(0)';
            }
        });
    }, { threshold: 0.1 });

    statusSteps.forEach((step, index) => {
        step.style.opacity = '0';
        step.style.transform = 'translateX(-20px)';
        step.style.transition = `opacity 0.6s ease ${index * 0.2}s, transform 0.6s ease ${index * 0.2}s`;
        observer.observe(step);
    });
});
</script>

</body>
</html>