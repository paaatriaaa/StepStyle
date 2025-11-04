// Modern Home Page Interactions
class StepStyleHome {
    constructor() {
        this.init();
    }

    init() {
        this.initProductInteractions();
        this.initCountdownTimer();
        this.initNewsletter();
        this.initAnimations();
        this.initSmoothScrolling();
    }

    initProductInteractions() {
        // Add to cart functionality
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.addToCart(btn);
            });
        });

        // Wishlist functionality
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleWishlist(btn);
            });
        });

        // Quick view functionality
        document.querySelectorAll('.quick-view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.quickView(btn);
            });
        });

        // Product card clicks
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', (e) => {
                if (!e.target.closest('.product-actions') && !e.target.closest('.btn-add-cart')) {
                    const productId = card.dataset.productId;
                    this.viewProduct(productId);
                }
            });
        });
    }

    addToCart(button) {
        const productCard = button.closest('.product-card');
        const productName = productCard.querySelector('.product-title').textContent;
        
        // Add loading state
        button.classList.add('loading');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        
        // Simulate API call
        setTimeout(() => {
            // Show success
            this.showNotification(`🎉 ${productName} added to cart!`, 'success');
            
            // Update button
            button.innerHTML = '<i class="fas fa-check"></i> Added!';
            button.style.background = 'linear-gradient(45deg, #4CAF50, #45a049)';
            
            // Reset after delay
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.style.background = '';
                button.classList.remove('loading');
            }, 2000);
            
            // Update cart counter
            this.updateCartCounter();
        }, 1000);
    }

    toggleWishlist(button) {
        const icon = button.querySelector('i');
        const isActive = icon.classList.contains('fas');
        
        if (isActive) {
            icon.classList.replace('fas', 'far');
            this.showNotification('❤️ Removed from wishlist', 'info');
        } else {
            icon.classList.replace('far', 'fas');
            icon.style.color = '#f44336';
            this.showNotification('❤️ Added to wishlist!', 'success');
            
            // Add bounce animation
            button.style.animation = 'bounce 0.5s ease';
            setTimeout(() => button.style.animation = '', 500);
        }
    }

    quickView(button) {
        const productCard = button.closest('.product-card');
        const productName = productCard.querySelector('.product-title').textContent;
        this.showNotification(`👀 Quick view: ${productName}`, 'info');
    }

    viewProduct(productId) {
        this.showNotification(`🔍 Viewing product ${productId}`, 'info');
        // In real implementation, redirect to product page
        // window.location.href = `/products/product.php?id=${productId}`;
    }

    initCountdownTimer() {
        const countdownElement = document.querySelector('.countdown-timer');
        if (!countdownElement) return;

        // Set countdown to 5 days from now
        const countdownDate = new Date();
        countdownDate.setDate(countdownDate.getDate() + 5);

        const updateTimer = () => {
            const now = new Date().getTime();
            const distance = countdownDate - now;

            if (distance < 0) {
                countdownElement.innerHTML = '<div class="timer-expired">🎉 Sale Ended!</div>';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Update DOM
            ['days', 'hours', 'minutes', 'seconds'].forEach((unit, index) => {
                const element = document.getElementById(unit);
                if (element) {
                    const value = [days, hours, minutes, seconds][index];
                    element.textContent = value.toString().padStart(2, '0');
                    
                    // Add flip animation
                    element.style.animation = 'flip 0.5s ease';
                    setTimeout(() => element.style.animation = '', 500);
                }
            });
        };

        updateTimer();
        setInterval(updateTimer, 1000);
    }

    initNewsletter() {
        const form = document.getElementById('home-newsletter-form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const emailInput = form.querySelector('input[type="email"]');
            const email = emailInput.value.trim();

            if (!this.isValidEmail(email)) {
                this.showNotification('❌ Please enter a valid email address', 'error');
                emailInput.focus();
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;

            // Show loading
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            submitBtn.disabled = true;

            // Simulate subscription
            setTimeout(() => {
                this.showNotification('🎉 Successfully subscribed to newsletter!', 'success');
                emailInput.value = '';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }, 2000);
            }, 1500);
        });
    }

    initAnimations() {
        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe elements
        document.querySelectorAll('.product-card, .category-card, .brand-card, .feature-item').forEach(el => {
            el.classList.add('animate-on-scroll');
            observer.observe(el);
        });
    }

    initSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.custom-notification').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = `custom-notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span>${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 10000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;

        notification.querySelector('.notification-close').onclick = () => this.removeNotification(notification);
        
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => notification.style.transform = 'translateX(0)', 100);

        // Auto remove
        setTimeout(() => this.removeNotification(notification), 5000);
    }

    removeNotification(notification) {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => notification.remove(), 300);
    }

    getNotificationColor(type) {
        const colors = {
            success: 'linear-gradient(45deg, #4CAF50, #45a049)',
            error: 'linear-gradient(45deg, #f44336, #d32f2f)',
            info: 'linear-gradient(45deg, #2196F3, #1976D2)',
            warning: 'linear-gradient(45deg, #FF9800, #F57C00)'
        };
        return colors[type] || colors.info;
    }

    updateCartCounter() {
        const counter = document.querySelector('.cart-count');
        if (counter) {
            const current = parseInt(counter.textContent) || 0;
            counter.textContent = current + 1;
            counter.style.animation = 'bounce 0.5s ease';
            setTimeout(() => counter.style.animation = '', 500);
        }
    }

    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new StepStyleHome();
});

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    @keyframes bounce {
        0%, 20%, 53%, 80%, 100% {
            transform: scale(1);
        }
        40%, 43% {
            transform: scale(1.3);
        }
        70% {
            transform: scale(1.1);
        }
    }
    
    @keyframes flip {
        0% {
            transform: rotateX(0);
        }
        50% {
            transform: rotateX(90deg);
        }
        100% {
            transform: rotateX(0);
        }
    }
    
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }
    
    .animate-on-scroll.animate-in {
        opacity: 1;
        transform: translateY(0);
    }
    
    .loading {
        pointer-events: none;
        opacity: 0.7;
    }
    
    .notification-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }
    
    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
`;
document.head.appendChild(style);