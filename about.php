<?php
// Start session and set base path
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page metadata
$page_title = 'About StepStyle - Premium Footwear & Sneakers';
$page_description = 'Learn about StepStyle - your premier destination for authentic sneakers and premium footwear. Our story, mission, and commitment to quality.';
$body_class = 'about-page';

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
    <link rel="stylesheet" href="assets/css/about.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
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
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Our Story</h1>
                <p class="hero-subtitle">StepStyle was born from a passion for sneakers and a commitment to authenticity</p>
            </div>
        </div>
    </section>

    <!-- About Content -->
    <section class="about-content section-padding">
        <div class="container">
            <div class="about-grid">
                <!-- Main Content -->
                <div class="about-main">
                    <div class="content-block">
                        <h2>Who We Are</h2>
                        <p>StepStyle is more than just a sneaker store - we're a community of enthusiasts, collectors, and style-conscious individuals who believe that the right pair of shoes can transform your entire outfit and attitude.</p>
                        <p>Founded in 2020, we've grown from a small online boutique to one of the most trusted destinations for premium footwear. Our journey began with a simple mission: to provide authentic, high-quality sneakers with exceptional customer service.</p>
                    </div>

                    <div class="content-block">
                        <h2>Our Mission</h2>
                        <p>We're committed to bringing you the latest and most sought-after sneakers from top brands while maintaining the highest standards of authenticity and quality. Every pair we sell is carefully verified to ensure you're getting the real deal.</p>
                        <div class="mission-stats">
                            <div class="stat">
                                <span class="stat-number">50K+</span>
                                <span class="stat-label">Happy Customers</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">100+</span>
                                <span class="stat-label">Brands Available</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">24/7</span>
                                <span class="stat-label">Customer Support</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number">100%</span>
                                <span class="stat-label">Authentic Products</span>
                            </div>
                        </div>
                    </div>

                    <div class="content-block">
                        <h2>Why Choose StepStyle?</h2>
                        <div class="features-grid">
                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h3>100% Authentic</h3>
                                <p>Every product is verified by our authentication team to ensure you receive only genuine items.</p>
                            </div>
                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                                <h3>Fast Shipping</h3>
                                <p>Free shipping on orders over $50. Get your sneakers delivered quickly and securely.</p>
                            </div>
                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <h3>Easy Returns</h3>
                                <p>30-day return policy. If you're not satisfied, we'll make it right.</p>
                            </div>
                            <div class="feature">
                                <div class="feature-icon">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <h3>Expert Support</h3>
                                <p>Our sneaker experts are here to help you find the perfect pair.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="about-sidebar">
                    <div class="sidebar-card">
                        <h3>Quick Facts</h3>
                        <div class="facts-list">
                            <div class="fact">
                                <strong>Founded:</strong> 2020
                            </div>
                            <div class="fact">
                                <strong>Headquarters:</strong> Los Angeles, CA
                            </div>
                            <div class="fact">
                                <strong>Team Members:</strong> 50+
                            </div>
                            <div class="fact">
                                <strong>Countries Served:</strong> 15+
                            </div>
                            <div class="fact">
                                <strong>Products Sold:</strong> 100,000+
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h3>Our Values</h3>
                        <div class="values-list">
                            <div class="value">
                                <i class="fas fa-check-circle"></i>
                                <span>Authenticity Above All</span>
                            </div>
                            <div class="value">
                                <i class="fas fa-users"></i>
                                <span>Customer First</span>
                            </div>
                            <div class="value">
                                <i class="fas fa-rocket"></i>
                                <span>Innovation Driven</span>
                            </div>
                            <div class="value">
                                <i class="fas fa-heart"></i>
                                <span>Passion for Sneakers</span>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar-card">
                        <h3>Follow Our Journey</h3>
                        <div class="social-links">
                            <a href="#" class="social-link">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-tiktok"></i>
                                <span>TikTok</span>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>
                            <a href="#" class="social-link">
                                <i class="fab fa-youtube"></i>
                                <span>YouTube</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section section-padding bg-light">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Meet Our Team</h2>
                <p class="section-subtitle">The passionate individuals behind StepStyle</p>
            </div>

            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Alex Johnson</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p class="member-bio">Sneaker enthusiast with 10+ years in the industry. Started StepStyle to share his passion with the world.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Sarah Chen</h3>
                        <p class="member-role">Head of Authenticity</p>
                        <p class="member-bio">Expert in sneaker authentication with a keen eye for detail and quality assurance.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Mike Rodriguez</h3>
                        <p class="member-role">Creative Director</p>
                        <p class="member-bio">Brings style and innovation to our brand, ensuring we stay ahead of trends.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Emily Davis</h3>
                        <p class="member-role">Customer Experience</p>
                        <p class="member-bio">Dedicated to ensuring every customer has an exceptional shopping experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section section-padding">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Step Up Your Style?</h2>
                <p class="cta-description">Join thousands of satisfied customers and discover your next favorite pair of sneakers.</p>
                <div class="cta-actions">
                    <a href="products/categories.php" class="btn btn-primary btn-large">
                        <i class="fas fa-shopping-bag"></i>
                        Shop Now
                    </a>
                    <a href="auth/register.php" class="btn btn-outline btn-large">
                        <i class="fas fa-user-plus"></i>
                        Create Account
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
<script src="assets/js/about.js"></script>

</body>
</html>