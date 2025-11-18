// ===== STEPSTYLE E-COMMERCE APPLICATION =====
class StepStyle {
    constructor() {
        this.cart = [];
        this.wishlist = [];
        this.products = [];
        this.currentUser = null;
        this.isInitialized = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        this.init();
    }

    // ===== INITIALIZATION =====
    init() {
        if (this.isInitialized) return;
        
        console.log('🚀 StepStyle E-commerce Initializing...');
        this.showLoading();
        
        // Check DOM ready state
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupApplication());
        } else {
            this.setupApplication();
        }
        
        this.isInitialized = true;
    }

    setupApplication() {
        try {
            console.log('🛠️ Setting up StepStyle application...');
            
            // Initialize core functionality
            this.initializeCore();
            this.setupEventListeners();
            this.loadInitialData();
            this.setupIntersectionObserver();
            this.setupImageErrorHandling(); // <- TAMBAHAN BARU
            
            // Safety timeout to ensure loading screen hides
            setTimeout(() => {
                this.hideLoading();
                console.log('✅ StepStyle application ready!');
            }, 1500);
            
        } catch (error) {
            console.error('❌ Application setup failed:', error);
            this.hideLoading();
            this.showNotification('Failed to initialize application. Please refresh the page.', 'error');
        }
    }

    initializeCore() {
        // Initialize cart from localStorage
        this.loadCartFromStorage();
        this.loadWishlistFromStorage();
        
        // Update UI counters
        this.updateCartBadge();
        this.updateWishlistBadge();
        
        // Set global configuration
        window.STEPSTYLE_CONFIG = {
            baseUrl: window.location.origin,
            csrfToken: this.csrfToken,
            userId: this.currentUser?.id || 0,
            currency: 'USD',
            debug: window.location.hostname === 'localhost'
        };
    }

    // ===== LOADING MANAGEMENT =====
    showLoading() {
        const existingLoading = document.getElementById('global-loading');
        if (existingLoading) return;
        
        const loading = document.createElement('div');
        loading.className = 'loading';
        loading.id = 'global-loading';
        loading.innerHTML = `
            <div class="loader-container">
                <div class="loader"></div>
                <p>Loading StepStyle...</p>
            </div>
        `;
        
        document.body.appendChild(loading);
        document.body.classList.add('loading');
    }

    hideLoading() {
        const loading = document.getElementById('global-loading');
        if (loading) {
            loading.classList.add('hidden');
            setTimeout(() => {
                loading.remove();
            }, 500);
        }
        document.body.classList.remove('loading');
    }

    // ===== EVENT LISTENERS =====
    setupEventListeners() {
        console.log('🔧 Setting up event listeners...');
        
        // Window events
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 100));
        window.addEventListener('resize', this.debounce(this.handleResize.bind(this), 250));
        
        // Search functionality
        this.setupSearch();
        
        // User interactions
        this.setupUserInteractions();
        
        // Navigation
        this.setupNavigation();
        
        // Forms
        this.setupForms();
        
        // Product interactions
        this.setupProductInteractions();
    }

    setupSearch() {
        const searchInput = document.querySelector('#search-input');
        const searchForm = document.querySelector('.search-form');
        const searchSuggestions = document.querySelector('#search-suggestions');
        
        if (searchInput) {
            searchInput.addEventListener('focus', this.handleSearchFocus.bind(this));
            searchInput.addEventListener('blur', this.handleSearchBlur.bind(this));
            searchInput.addEventListener('input', this.debounce(this.handleSearchInput.bind(this), 300));
        }
        
        if (searchForm) {
            searchForm.addEventListener('submit', this.handleSearchSubmit.bind(this));
        }
    }

    setupUserInteractions() {
        // Cart and wishlist buttons
        document.addEventListener('click', this.handleGlobalClicks.bind(this));
        
        // User action icons
        const userIcons = document.querySelectorAll('.action-icon');
        userIcons.forEach(icon => {
            icon.addEventListener('click', this.handleUserAction.bind(this));
        });
    }

    setupNavigation() {
        // Mobile menu toggle
        const mobileToggle = document.querySelector('.mobile-menu-toggle');
        const mobileClose = document.querySelector('.mobile-nav-close');
        const mobileOverlay = document.querySelector('.mobile-nav-overlay');
        
        if (mobileToggle) {
            mobileToggle.addEventListener('click', this.toggleMobileMenu.bind(this));
        }
        
        if (mobileClose) {
            mobileClose.addEventListener('click', this.closeMobileMenu.bind(this));
        }
        
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', this.closeMobileMenu.bind(this));
        }
        
        // Mobile accordions
        this.setupMobileAccordions();
    }

    setupForms() {
        // Newsletter forms
        const newsletterForms = document.querySelectorAll('.newsletter-form');
        newsletterForms.forEach(form => {
            form.addEventListener('submit', this.handleNewsletterSubmit.bind(this));
        });
    }

    setupProductInteractions() {
        // Product interactions are handled in handleGlobalClicks
    }

    // ===== IMAGE ERROR HANDLING =====
    setupImageErrorHandling() {
        const images = document.querySelectorAll('.product-image img');
        images.forEach(img => {
            img.addEventListener('error', function() {
                console.log('Image failed to load:', this.src);
                this.style.display = 'none';
                const placeholder = this.nextElementSibling;
                if (placeholder && placeholder.classList.contains('product-image-placeholder')) {
                    placeholder.style.display = 'flex';
                }
            });
            
            // Also check if src is empty
            if (!img.src || img.src === '' || img.src === window.location.href) {
                img.style.display = 'none';
                const placeholder = img.nextElementSibling;
                if (placeholder && placeholder.classList.contains('product-image-placeholder')) {
                    placeholder.style.display = 'flex';
                }
            }
        });
    }

    // ===== SEARCH FUNCTIONALITY =====
    handleSearchFocus(event) {
        const searchBar = event.target.closest('.search-bar');
        if (searchBar) {
            searchBar.classList.add('focused');
            this.loadSearchSuggestions();
        }
    }

    handleSearchBlur(event) {
        const searchBar = event.target.closest('.search-bar');
        if (searchBar) {
            // Delay to allow click on suggestions
            setTimeout(() => {
                searchBar.classList.remove('focused');
            }, 200);
        }
    }

    handleSearchInput(event) {
        const searchTerm = event.target.value.trim();
        if (searchTerm.length > 2) {
            this.loadSearchSuggestions(searchTerm);
        } else {
            this.loadPopularSearches();
        }
    }

    handleSearchSubmit(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const searchTerm = formData.get('search')?.trim();
        
        if (searchTerm) {
            window.location.href = `products/categories.php?search=${encodeURIComponent(searchTerm)}`;
        }
    }

    async loadSearchSuggestions(searchTerm = '') {
        const suggestionsList = document.querySelector('#suggestions-list');
        if (!suggestionsList) return;

        try {
            let url = 'API/search/suggestions.php';
            if (searchTerm) {
                url += `?q=${encodeURIComponent(searchTerm)}`;
            }

            const response = await fetch(url);
            const suggestions = await response.json();

            suggestionsList.innerHTML = suggestions.map(suggestion => `
                <div class="suggestion-item" data-product-id="${suggestion.id}">
                    <div class="suggestion-info">
                        <div class="suggestion-name">${suggestion.name}</div>
                        <div class="suggestion-brand">${suggestion.brand}</div>
                        <div class="suggestion-price">$${suggestion.price}</div>
                    </div>
                </div>
            `).join('');

            // Add click events to suggestions
            suggestionsList.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => {
                    const productId = item.dataset.productId;
                    window.location.href = `products/detail.php?id=${productId}`;
                });
            });

        } catch (error) {
            console.error('Error loading search suggestions:', error);
            this.loadPopularSearches();
        }
    }

    loadPopularSearches() {
        const suggestionsList = document.querySelector('#suggestions-list');
        if (!suggestionsList) return;

        const popularSearches = [
            { id: 1, name: 'Nike Air Max 270', brand: 'Running Shoes', price: '149.99' },
            { id: 2, name: 'Jordan 1 Retro High', brand: 'Basketball Shoes', price: '170.00' },
            { id: 3, name: 'Adidas Ultraboost', brand: 'Running Shoes', price: '180.00' },
            { id: 4, name: 'New Balance 550', brand: 'Lifestyle', price: '120.00' }
        ];

        suggestionsList.innerHTML = popularSearches.map(suggestion => `
            <div class="suggestion-item" data-product-id="${suggestion.id}">
                <div class="suggestion-info">
                    <div class="suggestion-name">${suggestion.name}</div>
                    <div class="suggestion-brand">${suggestion.brand}</div>
                    <div class="suggestion-price">$${suggestion.price}</div>
                </div>
            </div>
        `).join('');

        // Add click events to suggestions
        suggestionsList.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', () => {
                const productId = item.dataset.productId;
                window.location.href = `products/detail.php?id=${productId}`;
            });
        });
    }

    // ===== USER INTERACTIONS =====
    handleGlobalClicks(event) {
        const target = event.target;
        
        // Add to cart button
        if (target.closest('.btn-add-cart') || target.closest('.add-to-cart-btn')) {
            event.preventDefault();
            const button = target.closest('.btn-add-cart') || target.closest('.add-to-cart-btn');
            if (button.disabled) return;
            
            const productCard = button.closest('.product-card');
            this.addToCart(productCard);
            return;
        }
        
        // Wishlist button
        if (target.closest('.wishlist-btn')) {
            event.preventDefault();
            const productCard = target.closest('.product-card');
            this.toggleWishlist(productCard);
            return;
        }
        
        // Quick view button
        if (target.closest('.quick-view-btn')) {
            event.preventDefault();
            const productCard = target.closest('.product-card');
            this.quickView(productCard);
            return;
        }
        
        // Compare button
        if (target.closest('.compare-btn')) {
            event.preventDefault();
            const productCard = target.closest('.product-card');
            this.addToCompare(productCard);
            return;
        }
        
        // Notification close
        if (target.closest('.notification-close')) {
            event.preventDefault();
            this.closeNotification(target.closest('.notification'));
            return;
        }
        
        // Modal close
        if (target.closest('.modal-close') || target.closest('.modal-overlay')) {
            event.preventDefault();
            this.closeModal(target.closest('.modal-overlay'));
            return;
        }
    }

    handleUserAction(event) {
        const actionIcon = event.currentTarget;
        const actionType = Array.from(actionIcon.classList).find(cls => 
            cls.includes('cart-icon') || cls.includes('wishlist-icon') || cls.includes('user-profile')
        );
        
        if (!actionType) return;

        switch (true) {
            case actionType.includes('cart-icon'):
                if (actionIcon.querySelector('.cart-preview')) {
                    // Toggle cart preview instead of navigating
                    return;
                }
                window.location.href = 'user/cart.php';
                break;
            case actionType.includes('wishlist-icon'):
                window.location.href = 'user/wishlist.php';
                break;
            case actionType.includes('user-profile'):
                if (this.currentUser) {
                    window.location.href = 'user/profile.php';
                } else {
                    window.location.href = 'auth/login.php';
                }
                break;
        }
    }

    // ===== CART FUNCTIONALITY =====
    async addToCart(productCard) {
        if (!productCard) {
            console.error('Product card not found');
            this.showNotification('Product not found', 'error');
            return;
        }
        
        const productId = productCard.dataset.productId;
        
        try {
            // Send AJAX request to add to cart
            const response = await fetch('API/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1,
                    csrf_token: this.csrfToken
                })
            });

            const result = await response.json();

            if (result.success) {
                // Update cart count
                this.updateCartCount(result.cart_count);
                
                // Animate cart icon
                this.animateCartIcon();
                
                // Show success notification
                this.showNotification('🎉 Product added to cart!', 'success');
                
                // Update cart preview
                this.updateCartPreview();
            } else {
                this.showNotification(result.message || 'Failed to add product to cart', 'error');
            }
            
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification('Failed to add product to cart', 'error');
        }
    }

    updateCartCount(count) {
        const cartBadges = document.querySelectorAll('.cart-count');
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    updateCartPreview() {
        // This would typically fetch updated cart data from the server
        // For now, we'll just update the count in the preview header
        const cartCount = document.querySelector('.cart-count')?.textContent || '0';
        const previewHeader = document.querySelector('.cart-preview-header h4');
        if (previewHeader) {
            previewHeader.textContent = `Your Cart (${cartCount})`;
        }
    }

    // ===== WISHLIST FUNCTIONALITY =====
    async toggleWishlist(productCard) {
        if (!productCard) {
            console.error('Product card not found');
            return;
        }
        
        const productId = productCard.dataset.productId;
        const wishlistBtn = productCard.querySelector('.wishlist-btn');
        
        try {
            const response = await fetch('API/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    action: 'toggle',
                    csrf_token: this.csrfToken
                })
            });

            const result = await response.json();

            if (result.success) {
                if (result.in_wishlist) {
                    // Added to wishlist
                    if (wishlistBtn) {
                        wishlistBtn.classList.add('active');
                        wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
                    }
                    this.showNotification('❤️ Added to wishlist!', 'success');
                } else {
                    // Removed from wishlist
                    if (wishlistBtn) {
                        wishlistBtn.classList.remove('active');
                        wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
                    }
                    this.showNotification('💔 Removed from wishlist', 'info');
                }
                
                // Update wishlist count
                this.updateWishlistCount(result.wishlist_count);
            } else {
                this.showNotification(result.message || 'Failed to update wishlist', 'error');
            }
            
        } catch (error) {
            console.error('Error updating wishlist:', error);
            this.showNotification('Failed to update wishlist', 'error');
        }
    }

    updateWishlistCount(count) {
        const wishlistBadges = document.querySelectorAll('.wishlist-count');
        wishlistBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    // ===== PRODUCT INTERACTIONS =====
    quickView(productCard) {
        const productId = productCard?.dataset.productId;
        if (productId) {
            // Redirect to product detail page for now
            // In a real implementation, this would show a modal
            window.location.href = `products/detail.php?id=${productId}`;
        }
    }

    addToCompare(productCard) {
        const productId = productCard?.dataset.productId;
        this.showNotification('🔍 Added to comparison', 'info');
    }

    // ===== NAVIGATION =====
    toggleMobileMenu() {
        const mobileNav = document.querySelector('.mobile-nav');
        const mobileOverlay = document.querySelector('.mobile-nav-overlay');
        
        mobileNav.classList.toggle('active');
        mobileOverlay.classList.toggle('active');
        document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    }

    closeMobileMenu() {
        const mobileNav = document.querySelector('.mobile-nav');
        const mobileOverlay = document.querySelector('.mobile-nav-overlay');
        
        mobileNav.classList.remove('active');
        mobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    setupMobileAccordions() {
        const accordionHeaders = document.querySelectorAll('.mobile-nav-accordion-header');
        
        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                header.classList.toggle('active');
            });
        });
    }

    // ===== FORM HANDLING =====
    async handleNewsletterSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const emailInput = form.querySelector('input[type="email"]');
        const email = emailInput?.value.trim();
        
        if (email && this.validateEmail(email)) {
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subscribing...';
            submitBtn.disabled = true;
            
            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                this.showNotification('📧 Thank you for subscribing to our newsletter!', 'success');
                form.reset();
                
            } catch (error) {
                this.showNotification('❌ Failed to subscribe. Please try again.', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        } else {
            this.showNotification('❌ Please enter a valid email address', 'error');
        }
    }

    // ===== UI UPDATES =====
    updateCartBadge() {
        // This would typically fetch from server
        const count = this.cart.reduce((total, item) => total + item.quantity, 0);
        this.updateCartCount(count);
    }

    updateWishlistBadge() {
        // This would typically fetch from server
        const count = this.wishlist.length;
        this.updateWishlistCount(count);
    }

    animateCartIcon() {
        const cartIcons = document.querySelectorAll('.cart-icon');
        
        cartIcons.forEach(icon => {
            icon.style.transform = 'scale(1.2)';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 300);
        });
    }

    // ===== NOTIFICATION SYSTEM =====
    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Show with animation
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            this.closeNotification(notification);
        }, 5000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.closeNotification(notification);
        });
    }

    closeNotification(notification) {
        if (notification) {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }

    // ===== DATA MANAGEMENT =====
    loadInitialData() {
        console.log('📦 Loading initial data...');
        
        // Mock product data - in real app, this would come from API
        this.products = [
            {
                id: '1',
                name: 'Nike Air Max 270',
                brand: 'Nike',
                price: 150,
                original_price: 180,
                image_url: 'assets/images/products/nike-air-max-270.jpg',
                description: 'Comfortable running shoes with advanced cushioning technology.',
                rating: 4.5,
                review_count: 128,
                stock_quantity: 10
            },
            {
                id: '2',
                name: 'Adidas Ultraboost 21',
                brand: 'Adidas',
                price: 180,
                original_price: 220,
                image_url: 'assets/images/products/adidas-ultraboost-21.jpg',
                description: 'High-performance running shoes with responsive cushioning.',
                rating: 4.8,
                review_count: 256,
                stock_quantity: 15
            }
        ];
        
        console.log('✅ Initial data loaded');
    }

    // ===== STORAGE MANAGEMENT =====
    loadCartFromStorage() {
        try {
            const savedCart = localStorage.getItem('stepstyle_cart');
            if (savedCart) {
                this.cart = JSON.parse(savedCart);
            }
        } catch (error) {
            console.error('Error loading cart from storage:', error);
            this.cart = [];
        }
    }

    saveCartToStorage() {
        try {
            localStorage.setItem('stepstyle_cart', JSON.stringify(this.cart));
        } catch (error) {
            console.error('Error saving cart to storage:', error);
        }
    }

    loadWishlistFromStorage() {
        try {
            const savedWishlist = localStorage.getItem('stepstyle_wishlist');
            if (savedWishlist) {
                this.wishlist = JSON.parse(savedWishlist);
                // Update wishlist buttons
                this.updateWishlistButtons();
            }
        } catch (error) {
            console.error('Error loading wishlist from storage:', error);
            this.wishlist = [];
        }
    }

    saveWishlistToStorage() {
        try {
            localStorage.setItem('stepstyle_wishlist', JSON.stringify(this.wishlist));
        } catch (error) {
            console.error('Error saving wishlist to storage:', error);
        }
    }

    updateWishlistButtons() {
        this.wishlist.forEach(item => {
            const wishlistBtn = document.querySelector(`.wishlist-btn[data-product-id="${item.id}"]`);
            if (wishlistBtn) {
                wishlistBtn.classList.add('active');
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
            }
        });
    }

    // ===== WINDOW EVENTS =====
    handleScroll() {
        const header = document.querySelector('.header');
        const backToTop = document.querySelector('.back-to-top');
        const scrollY = window.scrollY;
        
        // Header scroll effect
        if (header) {
            header.classList.toggle('scrolled', scrollY > 100);
        }
        
        // Back to top button
        if (backToTop) {
            backToTop.classList.toggle('visible', scrollY > 300);
        }
        
        // Scroll to top when back to top button is clicked
        backToTop?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    handleResize() {
        // Close mobile menu on resize to desktop
        if (window.innerWidth > 768) {
            this.closeMobileMenu();
        }
    }

    // ===== UTILITY FUNCTIONS =====
    validateEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // ===== PERFORMANCE & OBSERVERS =====
    setupIntersectionObserver() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe elements for animation
        const elementsToObserve = document.querySelectorAll('.product-card, .brand-card, .category-card, .section-title');
        elementsToObserve.forEach(el => {
            if (el) observer.observe(el);
        });
    }

    // ===== PUBLIC METHODS =====
    getCart() {
        return [...this.cart];
    }

    getWishlist() {
        return [...this.wishlist];
    }

    getProduct(id) {
        return this.products.find(product => product.id === id);
    }

    // ===== DESTROY =====
    destroy() {
        this.isInitialized = false;
        console.log('🧹 StepStyle application destroyed');
    }
}

// ===== APPLICATION BOOTSTRAP =====
document.addEventListener('DOMContentLoaded', () => {
    console.log('🌐 DOM fully loaded, initializing StepStyle...');
    
    try {
        window.StepStyle = new StepStyle();
        window.stepstyle = window.StepStyle;
        
        console.log('🎉 StepStyle application initialized successfully!');
        
    } catch (error) {
        console.error('💥 Failed to initialize StepStyle:', error);
        
        // Emergency fallback
        const loading = document.querySelector('.loading');
        if (loading) loading.remove();
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'notification notification-error';
        errorDiv.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load application. Please refresh the page.
                </span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        document.body.appendChild(errorDiv);
        
        setTimeout(() => errorDiv.classList.add('show'), 100);
        
        errorDiv.querySelector('.notification-close').addEventListener('click', () => {
            errorDiv.remove();
        });
    }
});

// Fallback for DOM already loaded
if (document.readyState !== 'loading') {
    window.StepStyle = new StepStyle();
    // main.js - General image handling functions

/**
 * Handle product image loading and errors
 */
function handleProductImages() {
    const productImages = document.querySelectorAll('.product-image-main');
    
    productImages.forEach(img => {
        // Check if image is already loaded
        if (img.complete) {
            if (img.naturalHeight === 0) {
                // Image failed to load
                showImageFallback(img);
            }
        } else {
            img.addEventListener('load', function() {
                console.log('Image loaded successfully:', this.src);
            });
            
            img.addEventListener('error', function() {
                console.log('Image failed to load:', this.src);
                showImageFallback(this);
            });
        }
    });
    
    // Force check all images after page load
    setTimeout(() => {
        productImages.forEach(img => {
            if (img.naturalHeight === 0 && img.style.display !== 'none') {
                showImageFallback(img);
            }
        });
    }, 1000);
}

/**
 * Show fallback placeholder when image fails to load
 */
function showImageFallback(imgElement) {
    imgElement.style.display = 'none';
    const placeholder = imgElement.nextElementSibling;
    if (placeholder && placeholder.classList.contains('product-image-placeholder')) {
        placeholder.style.display = 'flex';
    }
}

/**
 * Initialize all image handlers
 */
function initImageHandlers() {
    handleProductImages();
    handleBrandLogos();
}

/**
 * Handle brand logo images
 */
function handleBrandLogos() {
    const brandLogos = document.querySelectorAll('.brand-logo-img');
    
    brandLogos.forEach(logo => {
        if (logo.complete) {
            if (logo.naturalHeight === 0) {
                showBrandFallback(logo);
            }
        } else {
            logo.addEventListener('error', function() {
                showBrandFallback(this);
            });
        }
    });
}

/**
 * Show brand logo fallback
 */
function showBrandFallback(logoElement) {
    logoElement.style.display = 'none';
    const fallback = logoElement.nextElementSibling;
    if (fallback && fallback.classList.contains('logo-fallback')) {
        fallback.style.display = 'flex';
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initImageHandlers();
});

// Export functions for use in other files (if needed)
window.ImageHandlers = {
    handleProductImages,
    showImageFallback,
    handleBrandLogos,
    showBrandFallback
};

}