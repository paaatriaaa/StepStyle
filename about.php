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
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <style>
        /* About Page Styles */
        .about-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6rem 0 4rem;
            text-align: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .section-padding {
            padding: 5rem 0;
        }
        
        .bg-light {
            background: #f8fafc;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* About Content */
        .about-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 4rem;
            align-items: start;
        }
        
        .content-block {
            margin-bottom: 3rem;
        }
        
        .content-block h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1f2937;
            position: relative;
        }
        
        .content-block h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .content-block p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4b5563;
            margin-bottom: 1.5rem;
        }
        
        /* Mission Stats */
        .mission-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .stat {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        
        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
        }
        
        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .feature {
            text-align: center;
            padding: 2rem 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
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
        
        .feature h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        
        .feature p {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.6;
            margin: 0;
        }
        
        /* Sidebar */
        .about-sidebar {
            position: sticky;
            top: 2rem;
        }
        
        .sidebar-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        
        .sidebar-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1f2937;
            text-align: center;
            position: relative;
        }
        
        .sidebar-card h3::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .facts-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .fact {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .fact strong {
            color: #1f2937;
        }
        
        .values-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .value {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            transition: transform 0.2s ease;
        }
        
        .value:hover {
            transform: translateX(5px);
        }
        
        .value i {
            color: #667eea;
            font-size: 1.2rem;
        }
        
        .value span {
            font-weight: 500;
            color: #374151;
        }
        
        .social-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .social-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 8px;
            text-decoration: none;
            color: #374151;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: #667eea;
            color: white;
            transform: translateX(5px);
        }
        
        .social-link i {
            font-size: 1.3rem;
            width: 24px;
        }
        
        /* Team Section */
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
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .team-member {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .team-member:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .member-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .image-placeholder {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            backdrop-filter: blur(10px);
        }
        
        .member-info {
            padding: 2rem;
            text-align: center;
        }
        
        .member-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        
        .member-role {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .member-bio {
            color: #6b7280;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        
        /* CTA Section */
        .cta-section {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            text-align: center;
        }
        
        .cta-content {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .cta-description {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .cta-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
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
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: #1f2937;
            transform: translateY(-2px);
        }
        
        .btn-large {
            padding: 15px 30px;
            font-size: 1.1rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .about-sidebar {
                position: static;
            }
            
            .mission-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .cta-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
        }
        
        @media (max-width: 480px) {
            .mission-stats {
                grid-template-columns: 1fr;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .section-padding {
                padding: 3rem 0;
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
                <!-- Team Member 1 -->
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Alex Johnson</h3>
                        <p class="member-role">Founder & CEO</p>
                        <p class="member-bio">Sneaker enthusiast with 10+ years in the industry. Started StepStyle to share his passion with the world and create the ultimate destination for authentic footwear.</p>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Sarah Chen</h3>
                        <p class="member-role">Head of Authenticity</p>
                        <p class="member-bio">Expert in sneaker authentication with a keen eye for detail. Sarah ensures every product meets our strict quality standards before reaching our customers.</p>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-palette"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Mike Rodriguez</h3>
                        <p class="member-role">Creative Director</p>
                        <p class="member-bio">Brings style and innovation to our brand. Mike's creative vision ensures we stay ahead of trends while maintaining our unique identity in the market.</p>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-headset"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">Emily Davis</h3>
                        <p class="member-role">Customer Experience Manager</p>
                        <p class="member-bio">Dedicated to ensuring every customer has an exceptional shopping experience. Emily leads our support team with passion and attention to detail.</p>
                    </div>
                </div>

                <!-- Team Member 5 -->
                <div class="team-member">
                    <div class="member-image">
                        <div class="image-placeholder">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3 class="member-name">David Kim</h3>
                        <p class="member-role">Operations Director</p>
                        <p class="member-bio">Oversees our logistics and supply chain. David ensures smooth operations from warehouse to delivery, making sure you get your sneakers fast and secure.</p>
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
<script>
// Simple JavaScript for about page
document.addEventListener('DOMContentLoaded', function() {
    // Hide loading screen
    const loadingScreen = document.getElementById('global-loading');
    if (loadingScreen) {
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 1000);
    }

    // Add animation to stats when they come into view
    const stats = document.querySelectorAll('.stat');
    const features = document.querySelectorAll('.feature');
    const teamMembers = document.querySelectorAll('.team-member');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    // Set initial state for animation
    stats.forEach(stat => {
        stat.style.opacity = '0';
        stat.style.transform = 'translateY(20px)';
        stat.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(stat);
    });

    features.forEach(feature => {
        feature.style.opacity = '0';
        feature.style.transform = 'translateY(20px)';
        feature.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(feature);
    });

    teamMembers.forEach(member => {
        member.style.opacity = '0';
        member.style.transform = 'translateY(20px)';
        member.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(member);
    });
});
</script>

</body>
</html>