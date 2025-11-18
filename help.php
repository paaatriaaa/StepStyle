<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'Help Center - StepStyle';
$page_description = 'Get help with your StepStyle orders, shipping, returns, and more. Find answers to frequently asked questions and contact our support team.';
$body_class = 'help-page';

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
        /* Help Center Page Styles */
        .help-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5rem 0 3rem;
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
            margin: 0 auto 2rem;
            line-height: 1.6;
        }
        
        .search-container {
            max-width: 600px;
            margin: 0 auto;
            position: relative;
        }
        
        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        
        .search-button {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #3b82f6;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .section-padding {
            padding: 4rem 0;
        }
        
        .bg-light {
            background: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Quick Help Section */
        .quick-help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .help-card {
            background: white;
            padding: 2.5rem 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .help-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .help-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }
        
        .help-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .help-card p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .help-link {
            color: #3b82f6;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .help-link:hover {
            text-decoration: underline;
        }
        
        /* FAQ Section */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .section-subtitle {
            font-size: 1.2rem;
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .faq-categories {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .category-btn {
            padding: 12px 24px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s ease;
        }
        
        .category-btn.active,
        .category-btn:hover {
            border-color: #3b82f6;
            background: #3b82f6;
            color: white;
        }
        
        .faq-grid {
            display: grid;
            gap: 1rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        
        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: between;
            align-items: center;
            font-weight: 600;
            color: #1f2937;
            transition: background-color 0.3s ease;
        }
        
        .faq-question:hover {
            background: #f8fafc;
        }
        
        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            color: #6b7280;
            transition: transform 0.3s ease;
        }
        
        .faq-item.active .faq-question::after {
            transform: rotate(45deg);
            color: #3b82f6;
        }
        
        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            color: #6b7280;
            line-height: 1.6;
        }
        
        .faq-item.active .faq-answer {
            padding: 0 1.5rem 1.5rem;
            max-height: 500px;
        }
        
        /* Contact Section */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }
        
        .contact-methods {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .contact-method {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .contact-icon {
            width: 50px;
            height: 50px;
            background: #f0f9ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        
        .contact-info h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .contact-info p {
            color: #6b7280;
            margin-bottom: 0.25rem;
        }
        
        .contact-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
        }
        
        .contact-link:hover {
            text-decoration: underline;
        }
        
        .contact-form-container {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1f2937;
        }
        
        .contact-form {
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
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
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
        
        /* Resources Section */
        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .resource-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .resource-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .resource-icon {
            font-size: 2.5rem;
            color: #3b82f6;
            margin-bottom: 1rem;
        }
        
        .resource-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .resource-card p {
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-form-container {
                padding: 2rem;
            }
            
            .faq-categories {
                gap: 0.5rem;
            }
            
            .category-btn {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 480px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .section-padding {
                padding: 3rem 0;
            }
            
            .help-card {
                padding: 2rem 1.5rem;
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
    <section class="help-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">How can we help you?</h1>
                <p class="hero-subtitle">Find answers to your questions about orders, shipping, returns, and more</p>
                
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Search for help articles...">
                    <button class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Help Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Quick Help</h2>
                <p class="section-subtitle">Get immediate assistance with common issues</p>
            </div>
            
            <div class="quick-help-grid">
                <a href="track-order.php" class="help-card">
                    <div class="help-icon">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h3>Track Your Order</h3>
                    <p>Get real-time updates on your order status and delivery progress</p>
                    <span class="help-link">Track Order <i class="fas fa-arrow-right"></i></span>
                </a>
                
                <a href="user/orders.php" class="help-card">
                    <div class="help-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <h3>Returns & Exchanges</h3>
                    <p>Learn about our 30-day return policy and how to process returns</p>
                    <span class="help-link">Return Policy <i class="fas fa-arrow-right"></i></span>
                </a>
                
                <a href="contact.php" class="help-card">
                    <div class="help-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3>Contact Support</h3>
                    <p>Reach out to our customer support team for personalized help</p>
                    <span class="help-link">Contact Us <i class="fas fa-arrow-right"></i></span>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Frequently Asked Questions</h2>
                <p class="section-subtitle">Quick answers to common questions</p>
            </div>
            
            <div class="faq-categories">
                <button class="category-btn active" data-category="all">All Questions</button>
                <button class="category-btn" data-category="orders">Orders</button>
                <button class="category-btn" data-category="shipping">Shipping</button>
                <button class="category-btn" data-category="returns">Returns</button>
                <button class="category-btn" data-category="products">Products</button>
            </div>
            
            <div class="faq-grid">
                <!-- Order FAQs -->
                <div class="faq-item" data-category="orders">
                    <div class="faq-question">How do I place an order?</div>
                    <div class="faq-answer">
                        To place an order, simply browse our products, select your desired items, add them to your cart, and proceed to checkout. You'll need to create an account or checkout as a guest, enter your shipping information, and complete the payment process.
                    </div>
                </div>
                
                <div class="faq-item" data-category="orders">
                    <div class="faq-question">Can I modify or cancel my order?</div>
                    <div class="faq-answer">
                        Orders can be modified or cancelled within 1 hour of placement. After that, orders enter our processing system and cannot be changed. Please contact our support team immediately if you need to make changes to a recent order.
                    </div>
                </div>
                
                <!-- Shipping FAQs -->
                <div class="faq-item" data-category="shipping">
                    <div class="faq-question">What are your shipping options?</div>
                    <div class="faq-answer">
                        We offer several shipping options:
                        <br><br>
                        • Standard Shipping: 5-7 business days ($4.99)<br>
                        • Express Shipping: 2-3 business days ($9.99)<br>
                        • Overnight Shipping: Next business day ($19.99)<br>
                        • Free Shipping: On orders over $50 (Standard shipping only)
                    </div>
                </div>
                
                <div class="faq-item" data-category="shipping">
                    <div class="faq-question">Do you ship internationally?</div>
                    <div class="faq-answer">
                        Currently, we ship to the United States, Canada, United Kingdom, Australia, and select European countries. International shipping times vary from 7-14 business days. Additional customs fees and import duties may apply depending on your country's regulations.
                    </div>
                </div>
                
                <!-- Returns FAQs -->
                <div class="faq-item" data-category="returns">
                    <div class="faq-question">What is your return policy?</div>
                    <div class="faq-answer">
                        We offer a 30-day return policy from the date of delivery. Items must be unworn, in original condition with all tags attached, and in the original packaging. Sale items and customized products are final sale and cannot be returned.
                    </div>
                </div>
                
                <div class="faq-item" data-category="returns">
                    <div class="faq-question">How do I return an item?</div>
                    <div class="faq-answer">
                        To return an item:
                        <br><br>
                        1. Log into your account and go to Order History<br>
                        2. Select the order and items you want to return<br>
                        3. Print the return label and packing slip<br>
                        4. Package the items securely and attach the label<br>
                        5. Drop off at any authorized shipping location<br>
                        <br>
                        Refunds are processed within 3-5 business days after we receive your return.
                    </div>
                </div>
                
                <!-- Product FAQs -->
                <div class="faq-item" data-category="products">
                    <div class="faq-question">Are your products authentic?</div>
                    <div class="faq-answer">
                        Yes, all products sold on StepStyle are 100% authentic. We source directly from authorized distributors and brands. Every product undergoes our authentication process to ensure you receive genuine items. We guarantee the authenticity of all our products.
                    </div>
                </div>
                
                <div class="faq-item" data-category="products">
                    <div class="faq-question">How do I find the right size?</div>
                    <div class="faq-answer">
                        We provide detailed size charts for each brand on product pages. We recommend measuring your feet and comparing with our size guides. If you're between sizes, we suggest going up half a size for comfort. Remember, you can always exchange for a different size within 30 days.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section-padding">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Still Need Help?</h2>
                <p class="section-subtitle">Our support team is here to assist you</p>
            </div>
            
            <div class="contact-grid">
                <div class="contact-methods">
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Call Us</h3>
                            <p>+1 (555) 123-4567</p>
                            <p>Monday - Friday: 9AM - 6PM PST</p>
                            <p>Saturday: 10AM - 4PM PST</p>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Email Us</h3>
                            <p>support@stepstyle.com</p>
                            <p>We typically respond within 24 hours</p>
                            <a href="mailto:support@stepstyle.com" class="contact-link">Send Email</a>
                        </div>
                    </div>
                    
                    <div class="contact-method">
                        <div class="contact-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="contact-info">
                            <h3>Live Chat</h3>
                            <p>Available during business hours</p>
                            <p>Get instant help from our support team</p>
                            <a href="#" class="contact-link" id="start-chat">Start Chat</a>
                        </div>
                    </div>
                </div>
                
                <div class="contact-form-container">
                    <h3 class="form-title">Send us a Message</h3>
                    <form class="contact-form" id="help-contact-form">
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="order-number">Order Number (Optional)</label>
                            <input type="text" id="order-number" name="order_number">
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Issue Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select a category</option>
                                <option value="order">Order Issue</option>
                                <option value="shipping">Shipping Problem</option>
                                <option value="return">Return & Refund</option>
                                <option value="product">Product Question</option>
                                <option value="account">Account Issue</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" placeholder="Please describe your issue in detail..." required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i>
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Helpful Resources</h2>
                <p class="section-subtitle">Additional information to enhance your shopping experience</p>
            </div>
            
            <div class="resources-grid">
                <a href="size-guide.php" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-ruler"></i>
                    </div>
                    <h3>Size Guide</h3>
                    <p>Find the perfect fit with our comprehensive size charts and fitting tips</p>
                </a>
                
                <a href="care-guide.php" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-gem"></i>
                    </div>
                    <h3>Care Guide</h3>
                    <p>Learn how to properly care for and maintain your sneakers</p>
                </a>
                
                <a href="authenticity-guarantee.php" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Authenticity Guarantee</h3>
                    <p>Learn about our authentication process and quality standards</p>
                </a>
                
                <a href="shipping-info.php" class="resource-card">
                    <div class="resource-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Shipping Information</h3>
                    <p>Detailed information about shipping methods, times, and costs</p>
                </a>
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

    // FAQ Accordion Functionality
    const faqItems = document.querySelectorAll('.faq-item');
    const categoryButtons = document.querySelectorAll('.category-btn');
    
    // FAQ toggle
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        question.addEventListener('click', () => {
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });
    
    // Category filtering
    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const category = button.dataset.category;
            
            // Update active button
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            // Filter FAQ items
            faqItems.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.querySelector('.search-input');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        
        if (searchTerm.length > 2) {
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                
                if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.classList.add('active'); // Auto-expand matching items
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show all categories when searching
            categoryButtons.forEach(btn => {
                if (btn.dataset.category === 'all') {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        } else {
            // Reset to all when search is cleared
            faqItems.forEach(item => {
                item.style.display = 'block';
                item.classList.remove('active');
            });
            
            categoryButtons.forEach(btn => {
                if (btn.dataset.category === 'all') {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }
    });
    
    // Contact form handling
    const contactForm = document.getElementById('help-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const category = document.getElementById('category').value;
            const message = document.getElementById('message').value;
            
            if (!name || !email || !category || !message) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Simulate form submission
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                alert('Thank you for your message! Our support team will get back to you within 24 hours.');
                contactForm.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    }
    
    // Live chat simulation
    const startChatBtn = document.getElementById('start-chat');
    if (startChatBtn) {
        startChatBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Live chat is available Monday-Friday, 9AM-6PM PST. Please call or email us outside of these hours.');
        });
    }
    
    // Add animation to help cards
    const helpCards = document.querySelectorAll('.help-card');
    const resourceCards = document.querySelectorAll('.resource-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    helpCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
    
    resourceCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});
</script>

</body>
</html>