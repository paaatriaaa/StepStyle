// Home page functionality
document.addEventListener('DOMContentLoaded', function() {
    // Countdown timer
    function updateCountdown() {
        const countdownDate = new Date();
        countdownDate.setDate(countdownDate.getDate() + 5); // 5 days from now
        
        const now = new Date().getTime();
        const distance = countdownDate - now;
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Update display
        document.getElementById('days')?.textContent = days.toString().padStart(2, '0');
        document.getElementById('hours')?.textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes')?.textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds')?.textContent = seconds.toString().padStart(2, '0');
        
        // If countdown is over
        if (distance < 0) {
            clearInterval(countdownTimer);
            document.querySelector('.countdown-timer')?.innerHTML = '<div class="timer-expired">Sale Ended!</div>';
        }
    }
    
    // Initialize countdown
    let countdownTimer;
    if (document.querySelector('.countdown-timer')) {
        updateCountdown();
        countdownTimer = setInterval(updateCountdown, 1000);
    }
    
    // Newsletter form submission
    const newsletterForm = document.getElementById('home-newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            // Simulate form submission
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                this.reset();
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            }, 1500);
        });
    }
    
    // Brand card hover effects
    const brandCards = document.querySelectorAll('.brand-card');
    brandCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Sneaker image animations
    const sneakerImages = document.querySelectorAll('.hero-sneaker-image, .sale-sneaker-image');
    sneakerImages.forEach(img => {
        img.addEventListener('mouseenter', function() {
            this.style.transform = 'rotate(0deg) scale(1.05)';
        });
        
        img.addEventListener('mouseleave', function() {
            const isSaleSneaker = this.closest('.sale-sneaker');
            this.style.transform = isSaleSneaker ? 'rotate(5deg) scale(1)' : 'rotate(-5deg) scale(1)';
        });
    });
    
    // Scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.product-item, .category-card, .testimonial-card, .brand-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Loading screen
    const loadingScreen = document.getElementById('global-loading');
    if (loadingScreen) {
        window.addEventListener('load', function() {
            setTimeout(() => {
                loadingScreen.style.opacity = '0';
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            }, 1000);
        });
    }
    // Home page specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Image lazy loading enhancement
    const lazyImages = document.querySelectorAll('.product-image-main.lazy');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    lazyImages.forEach(img => imageObserver.observe(img));

    // Product card interactions
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        // Add to cart functionality
        const addToCartBtn = card.querySelector('.btn-add-cart:not(.disabled)');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                addToCart(productId);
            });
        }

        // Wishlist functionality
        const wishlistBtn = card.querySelector('.wishlist-btn');
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                toggleWishlist(productId, this);
            });
        }

        // Quick view functionality
        const quickViewBtn = card.querySelector('.quick-view-btn');
        if (quickViewBtn) {
            quickViewBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const productId = this.dataset.productId;
                openQuickView(productId);
            });
        }
    });

    // Add to cart function
    function addToCart(productId) {
        // Simulate add to cart - replace with actual API call
        const btn = document.querySelector(`.btn-add-cart[data-product-id="${productId}"]`);
        const originalText = btn.innerHTML;
        
        btn.innerHTML = '<i class="fas fa-check"></i> Added!';
        btn.style.background = '#10b981';
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = '';
        }, 2000);
        
        // Update cart count
        updateCartCount();
    }

    // Toggle wishlist function
    function toggleWishlist(productId, button) {
        const icon = button.querySelector('i');
        const isActive = icon.classList.contains('fas');
        
        if (isActive) {
            icon.className = 'far fa-heart';
            button.style.background = '';
        } else {
            icon.className = 'fas fa-heart';
            button.style.background = '#ef4444';
            button.style.color = 'white';
        }
    }

    // Quick view function
    function openQuickView(productId) {
        // Simulate quick view - replace with modal implementation
        console.log('Quick view for product:', productId);
    }

    // Update cart count function
    function updateCartCount() {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            const currentCount = parseInt(cartCount.textContent) || 0;
            cartCount.textContent = currentCount + 1;
        }
    }
});
});