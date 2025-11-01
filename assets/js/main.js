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
            this.initializeServiceWorker();
            this.setupErrorHandling();
            
            // Safety timeout to ensure loading screen hides
            setTimeout(() => {
                this.hideLoading();
                console.log('✅ StepStyle application ready!');
            }, 2000);
            
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
        // Remove existing loading screens
        this.hideLoading();
        
        const loading = document.createElement('div');
        loading.className = 'loading';
        loading.id = 'global-loading';
        loading.innerHTML = `
            <div class="loader-container">
                <div class="loader"></div>
                <p>Loading StepStyle...</p>
            </div>
        `;
        
        // Add to body
        document.body.appendChild(loading);
        document.body.classList.add('loading');
        
        console.log('🔄 Loading screen shown');
    }

    hideLoading() {
        const loading = document.getElementById('global-loading');
        if (loading) {
            loading.classList.add('hidden');
            setTimeout(() => {
                if (loading.parentNode) {
                    loading.remove();
                }
            }, 500);
        }
        document.body.classList.remove('loading');
        
        console.log('✅ Loading screen hidden');
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
        
        // Keyboard shortcuts
        this.setupKeyboardShortcuts();
    }

    setupSearch() {
        const searchInput = document.querySelector('.search-bar input');
        const searchForm = document.querySelector('.search-form');
        
        if (searchInput) {
            searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), 300));
            searchInput.addEventListener('focus', this.handleSearchFocus.bind(this));
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
        
        // Dropdown menus
        this.setupDropdowns();
    }

    setupForms() {
        // Newsletter forms
        const newsletterForms = document.querySelectorAll('.newsletter-form');
        newsletterForms.forEach(form => {
            form.addEventListener('submit', this.handleNewsletterSubmit.bind(this));
        });
        
        // Contact forms
        const contactForms = document.querySelectorAll('form[data-contact]');
        contactForms.forEach(form => {
            form.addEventListener('submit', this.handleContactSubmit.bind(this));
        });
    }

    setupProductInteractions() {
        // Product card interactions are handled in handleGlobalClicks
        // Additional product-specific setup can go here
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Escape key
            if (e.key === 'Escape') {
                this.handleEscapeKey();
            }
            
            // Search focus (Ctrl+K or Cmd+K)
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.focusSearch();
            }
            
            // Cart toggle (Ctrl+C or Cmd+C)
            if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                e.preventDefault();
                this.toggleCartPreview();
            }
        });
    }

    // ===== SEARCH FUNCTIONALITY =====
    handleSearch(event) {
        const searchTerm = event.target.value.trim().toLowerCase();
        
        if (searchTerm.length > 2) {
            this.showSearchSuggestions(searchTerm);
        } else {
            this.hideSearchSuggestions();
        }
    }

    handleSearchFocus(event) {
        const searchBar = event.target.closest('.search-bar');
        if (searchBar) {
            searchBar.classList.add('focused');
        }
    }

    handleSearchSubmit(event) {
        event.preventDefault();
        const formData = new FormData(event.target);
        const searchTerm = formData.get('q')?.trim();
        
        if (searchTerm) {
            window.location.href = `/products/search.php?q=${encodeURIComponent(searchTerm)}`;
        }
    }

    showSearchSuggestions(searchTerm) {
        this.hideSearchSuggestions();
        
        const searchBar = document.querySelector('.search-bar');
        if (!searchBar) return;
        
        const suggestions = document.createElement('div');
        suggestions.className = 'search-suggestions';
        
        // Filter products based on search term
        const filteredProducts = this.products.filter(product => 
            product.name.toLowerCase().includes(searchTerm) ||
            product.brand.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm)
        ).slice(0, 5);
        
        if (filteredProducts.length > 0) {
            suggestions.innerHTML = `
                <div class="suggestions-header">
                    <h4>Products</h4>
                </div>
                ${filteredProducts.map(product => `
                    <div class="suggestion-item" data-product-id="${product.id}">
                        <img src="${product.image}" alt="${product.name}" 
                             onerror="this.src='/assets/images/products/placeholder.jpg'">
                        <div class="suggestion-info">
                            <div class="suggestion-name">${product.name}</div>
                            <div class="suggestion-brand">${product.brand}</div>
                            <div class="suggestion-price">${this.formatPrice(product.price)}</div>
                        </div>
                    </div>
                `).join('')}
                <div class="suggestions-footer">
                    <a href="/products/search.php?q=${encodeURIComponent(searchTerm)}" class="view-all-results">
                        View all results for "${searchTerm}"
                    </a>
                </div>
            `;
            
            searchBar.appendChild(suggestions);
            
            // Add click events
            suggestions.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => {
                    const productId = item.dataset.productId;
                    this.viewProduct(productId);
                    this.hideSearchSuggestions();
                });
            });
            
            // Close suggestions when clicking outside
            setTimeout(() => {
                document.addEventListener('click', this.handleClickOutsideSuggestions.bind(this));
            }, 100);
        }
    }

    handleClickOutsideSuggestions(event) {
        const searchBar = document.querySelector('.search-bar');
        const suggestions = document.querySelector('.search-suggestions');
        
        if (suggestions && searchBar && !searchBar.contains(event.target)) {
            this.hideSearchSuggestions();
        }
    }

    hideSearchSuggestions() {
        const suggestions = document.querySelector('.search-suggestions');
        if (suggestions) {
            suggestions.remove();
        }
        document.removeEventListener('click', this.handleClickOutsideSuggestions);
        
        const searchBar = document.querySelector('.search-bar');
        if (searchBar) {
            searchBar.classList.remove('focused');
        }
    }

    focusSearch() {
        const searchInput = document.querySelector('.search-bar input');
        if (searchInput) {
            searchInput.focus();
        }
    }

    // ===== USER INTERACTIONS =====
    handleGlobalClicks(event) {
        const target = event.target;
        
        // Add to cart button
        if (target.closest('.add-to-cart-btn')) {
            event.preventDefault();
            const productCard = target.closest('.product-card');
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
        
        // Color selection
        if (target.closest('.color-dot')) {
            event.preventDefault();
            this.selectColor(target.closest('.color-dot'));
            return;
        }
        
        // Size selection
        if (target.closest('.size-option')) {
            event.preventDefault();
            this.selectSize(target.closest('.size-option'));
            return;
        }
        
        // Notification close
        if (target.closest('.notification-close')) {
            event.preventDefault();
            this.closeNotification(target.closest('.notification'));
            return;
        }
    }

    handleUserAction(event) {
        const actionIcon = event.currentTarget;
        const actionType = Array.from(actionIcon.classList).find(cls => 
            cls.includes('cart-icon') || cls.includes('wishlist-icon') || cls.includes('user-profile')
        );
        
        switch (true) {
            case actionType.includes('cart-icon'):
                window.location.href = '/user/cart.php';
                break;
            case actionType.includes('wishlist-icon'):
                window.location.href = '/user/wishlist.php';
                break;
            case actionType.includes('user-profile'):
                if (this.currentUser) {
                    window.location.href = '/user/profile.php';
                } else {
                    window.location.href = '/auth/login.php';
                }
                break;
        }
    }

    // ===== CART FUNCTIONALITY =====
    addToCart(productCard) {
        if (!productCard) {
            console.error('Product card not found');
            this.showNotification('Product not found', 'error');
            return;
        }
        
        const productId = productCard.dataset.productId;
        const product = this.products.find(p => p.id === productId);
        
        if (!product) {
            console.error('Product not found in catalog');
            this.showNotification('Product not available', 'error');
            return;
        }
        
        // Check if product is in stock
        if (product.quantity <= 0) {
            this.showNotification('Product is out of stock', 'warning');
            return;
        }
        
        // Get selected variant
        const selectedVariant = this.getSelectedVariant(productCard);
        const variantId = selectedVariant?.id || null;
        
        // Check variant stock
        if (selectedVariant && selectedVariant.quantity <= 0) {
            this.showNotification('Selected variant is out of stock', 'warning');
            return;
        }
        
        // Add to cart
        const cartItem = {
            id: this.generateCartItemId(productId, variantId),
            productId: productId,
            variantId: variantId,
            name: product.name,
            brand: product.brand,
            price: selectedVariant ? product.price + selectedVariant.additional_price : product.price,
            image: product.image,
            quantity: 1,
            variantInfo: selectedVariant ? {
                size: selectedVariant.size,
                color: selectedVariant.color
            } : null,
            maxQuantity: selectedVariant ? selectedVariant.quantity : product.quantity
        };
        
        // Check if item already exists in cart
        const existingItemIndex = this.cart.findIndex(item => item.id === cartItem.id);
        
        if (existingItemIndex > -1) {
            // Update quantity
            const existingItem = this.cart[existingItemIndex];
            if (existingItem.quantity < existingItem.maxQuantity) {
                existingItem.quantity += 1;
                this.cart[existingItemIndex] = existingItem;
            } else {
                this.showNotification('Maximum quantity reached', 'warning');
                return;
            }
        } else {
            // Add new item
            this.cart.push(cartItem);
        }
        
        // Update storage and UI
        this.saveCartToStorage();
        this.updateCartBadge();
        this.animateCartIcon();
        
        // Show success notification
        this.showNotification('🎉 Product added to cart!', 'success');
        
        console.log('Cart updated:', this.cart);
    }

    removeFromCart(cartItemId) {
        this.cart = this.cart.filter(item => item.id !== cartItemId);
        this.saveCartToStorage();
        this.updateCartBadge();
        this.showNotification('Item removed from cart', 'info');
    }

    updateCartQuantity(cartItemId, newQuantity) {
        const item = this.cart.find(item => item.id === cartItemId);
        if (item && newQuantity > 0 && newQuantity <= item.maxQuantity) {
            item.quantity = newQuantity;
            this.saveCartToStorage();
            this.updateCartBadge();
        }
    }

    getSelectedVariant(productCard) {
        const selectedColor = productCard.querySelector('.color-dot.active')?.dataset.variantId;
        // In a real app, you'd fetch variant data based on selected options
        return null; // Simplified for demo
    }

    generateCartItemId(productId, variantId) {
        return variantId ? `${productId}-${variantId}` : productId;
    }

    // ===== WISHLIST FUNCTIONALITY =====
    toggleWishlist(productCard) {
        if (!productCard) {
            console.error('Product card not found');
            return;
        }
        
        const productId = productCard.dataset.productId;
        const product = this.products.find(p => p.id === productId);
        const wishlistBtn = productCard.querySelector('.wishlist-btn');
        
        if (!product) {
            console.error('Product not found in catalog');
            return;
        }
        
        const existingIndex = this.wishlist.findIndex(item => item.id === productId);
        
        if (existingIndex > -1) {
            // Remove from wishlist
            this.wishlist.splice(existingIndex, 1);
            if (wishlistBtn) {
                wishlistBtn.classList.remove('active');
                wishlistBtn.innerHTML = '<i class="far fa-heart"></i>';
            }
            this.showNotification('💔 Removed from wishlist', 'info');
        } else {
            // Add to wishlist
            this.wishlist.push({
                id: productId,
                name: product.name,
                brand: product.brand,
                price: product.price,
                image: product.image,
                addedAt: new Date().toISOString()
            });
            
            if (wishlistBtn) {
                wishlistBtn.classList.add('active');
                wishlistBtn.innerHTML = '<i class="fas fa-heart"></i>';
            }
            this.showNotification('❤️ Added to wishlist!', 'success');
            this.animateWishlistIcon(wishlistBtn);
        }
        
        this.saveWishlistToStorage();
        this.updateWishlistBadge();
        
        console.log('Wishlist updated:', this.wishlist);
    }

    // ===== PRODUCT INTERACTIONS =====
    quickView(productCard) {
        const productId = productCard?.dataset.productId;
        const product = this.products.find(p => p.id === productId);
        
        if (product) {
            this.showQuickViewModal(product);
        }
    }

    showQuickViewModal(product) {
        // Create modal HTML
        const modalHTML = `
            <div class="modal-overlay quick-view-overlay">
                <div class="modal-container quick-view-modal">
                    <button class="modal-close" aria-label="Close quick view">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="modal-content">
                        <div class="product-quick-view">
                            <div class="product-images">
                                <div class="main-image">
                                    <img src="${product.image}" alt="${product.name}">
                                </div>
                            </div>
                            <div class="product-details">
                                <div class="product-brand">${product.brand}</div>
                                <h2 class="product-title">${product.name}</h2>
                                <div class="product-price">
                                    <span class="current-price">${this.formatPrice(product.price)}</span>
                                    ${product.comparePrice ? `<span class="compare-price">${this.formatPrice(product.comparePrice)}</span>` : ''}
                                </div>
                                <div class="product-rating">
                                    <div class="stars">${this.generateStarRating(product.rating)}</div>
                                    <span class="rating-count">(${product.reviewCount} reviews)</span>
                                </div>
                                <div class="product-description">
                                    ${product.description}
                                </div>
                                <div class="product-actions">
                                    <button class="btn btn-primary add-to-cart-btn" data-product-id="${product.id}">
                                        <i class="fas fa-shopping-cart"></i>
                                        Add to Cart
                                    </button>
                                    <button class="btn btn-outline wishlist-btn" data-product-id="${product.id}">
                                        <i class="far fa-heart"></i>
                                        Add to Wishlist
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Add to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Add event listeners
        const modal = document.querySelector('.quick-view-overlay');
        const closeBtn = modal.querySelector('.modal-close');
        
        closeBtn.addEventListener('click', () => this.closeModal(modal));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) this.closeModal(modal);
        });
        
        // Add product action listeners within modal
        const addToCartBtn = modal.querySelector('.add-to-cart-btn');
        const wishlistBtn = modal.querySelector('.wishlist-btn');
        
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', () => {
                this.addToCart({ dataset: { productId: product.id } });
                this.closeModal(modal);
            });
        }
        
        if (wishlistBtn) {
            wishlistBtn.addEventListener('click', () => {
                this.toggleWishlist({ dataset: { productId: product.id } });
                wishlistBtn.querySelector('i').className = 'fas fa-heart';
                wishlistBtn.classList.add('active');
            });
        }
    }

    closeModal(modal) {
        modal.style.opacity = '0';
        setTimeout(() => {
            if (modal.parentNode) {
                modal.remove();
            }
        }, 300);
    }

    addToCompare(productCard) {
        const productId = productCard?.dataset.productId;
        this.showNotification('🔍 Added to comparison', 'info');
        // Comparison functionality would be implemented here
    }

    selectColor(colorDot) {
        const productCard = colorDot.closest('.product-card');
        const colorDots = productCard.querySelectorAll('.color-dot');
        
        colorDots.forEach(dot => dot.classList.remove('active'));
        colorDot.classList.add('active');
        
        // Update product image based on color
        const color = colorDot.style.backgroundColor;
        this.updateProductImage(productCard, color);
    }

    selectSize(sizeOption) {
        const productCard = sizeOption.closest('.product-card');
        const sizeOptions = productCard.querySelectorAll('.size-option');
        
        sizeOptions.forEach(option => option.classList.remove('active'));
        sizeOption.classList.add('active');
    }

    updateProductImage(productCard, color) {
        // This would update the product image based on selected color
        // Implementation depends on your product data structure
        console.log('Updating product image for color:', color);
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

    setupDropdowns() {
        const dropdowns = document.querySelectorAll('.dropdown');
        
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('mouseenter', () => {
                dropdown.classList.add('open');
            });
            
            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.remove('open');
            });
        });
    }

    // ===== FORM HANDLING =====
    handleNewsletterSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const emailInput = form.querySelector('input[type="email"]');
        const email = emailInput?.value.trim();
        
        if (email && this.validateEmail(email)) {
            // Simulate API call
            this.showLoading();
            
            setTimeout(() => {
                this.hideLoading();
                this.showNotification('📧 Thank you for subscribing to our newsletter!', 'success');
                form.reset();
            }, 1000);
        } else {
            this.showNotification('❌ Please enter a valid email address', 'error');
        }
    }

    handleContactSubmit(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);
        
        // Basic validation
        const name = formData.get('name');
        const email = formData.get('email');
        const message = formData.get('message');
        
        if (!name || !email || !message) {
            this.showNotification('Please fill in all required fields', 'error');
            return;
        }
        
        if (!this.validateEmail(email)) {
            this.showNotification('Please enter a valid email address', 'error');
            return;
        }
        
        // Simulate form submission
        this.showLoading();
        
        setTimeout(() => {
            this.hideLoading();
            this.showNotification('✅ Thank you for your message! We\'ll get back to you soon.', 'success');
            form.reset();
        }, 1500);
    }

    // ===== UI UPDATES =====
    updateCartBadge() {
        const count = this.cart.reduce((total, item) => total + item.quantity, 0);
        const cartBadges = document.querySelectorAll('.cart-count');
        
        cartBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    updateWishlistBadge() {
        const count = this.wishlist.length;
        const wishlistBadges = document.querySelectorAll('.wishlist-count');
        
        wishlistBadges.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
    }

    animateCartIcon() {
        const cartIcons = document.querySelectorAll('.cart-icon');
        
        cartIcons.forEach(icon => {
            icon.style.transform = 'scale(1.3)';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 300);
        });
    }

    animateWishlistIcon(wishlistBtn) {
        if (wishlistBtn) {
            wishlistBtn.style.transform = 'scale(1.3)';
            setTimeout(() => {
                wishlistBtn.style.transform = 'scale(1)';
            }, 300);
        }
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
        const autoRemove = setTimeout(() => {
            this.closeNotification(notification);
        }, 5000);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            clearTimeout(autoRemove);
            this.closeNotification(notification);
        });
    }

    closeNotification(notification) {
        if (notification) {
            notification.classList.remove('show');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }

    // ===== DATA MANAGEMENT =====
    loadInitialData() {
        console.log('📦 Loading initial data...');
        
        // Mock product data - in real app, this would come from an API
        this.products = [
            {
                id: '1',
                name: 'Nike Air Max 270',
                brand: 'Nike',
                price: 150,
                comparePrice: 180,
                image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&h=400&fit=crop',
                description: 'Comfortable running shoes with advanced cushioning technology. Perfect for everyday wear and athletic activities.',
                rating: 4.5,
                reviewCount: 128,
                quantity: 10,
                variants: [
                    { id: '1-1', size: 'US 8', color: '#000000', quantity: 5 },
                    { id: '1-2', size: 'US 9', color: '#ffffff', quantity: 3 },
                    { id: '1-3', size: 'US 10', color: '#ff0000', quantity: 2 }
                ]
            },
            {
                id: '2',
                name: 'Adidas Ultraboost 21',
                brand: 'Adidas',
                price: 180,
                comparePrice: 220,
                image: 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=400&h=400&fit=crop',
                description: 'High-performance running shoes with responsive cushioning and superior comfort.',
                rating: 4.8,
                reviewCount: 256,
                quantity: 15,
                variants: [
                    { id: '2-1', size: 'US 8', color: '#0000ff', quantity: 8 },
                    { id: '2-2', size: 'US 9', color: '#000000', quantity: 7 }
                ]
            },
            {
                id: '3',
                name: 'Jordan 1 Retro High',
                brand: 'Jordan',
                price: 170,
                comparePrice: 200,
                image: 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&h=400&fit=crop',
                description: 'Classic basketball shoes with modern comfort and timeless style.',
                rating: 4.7,
                reviewCount: 89,
                quantity: 8,
                variants: [
                    { id: '3-1', size: 'US 9', color: '#ce1141', quantity: 4 },
                    { id: '3-2', size: 'US 10', color: '#000000', quantity: 4 }
                ]
            },
            {
                id: '4',
                name: 'Puma RS-X',
                brand: 'Puma',
                price: 120,
                comparePrice: 150,
                image: 'https://images.unsplash.com/photo-1560769629-975ec94e6a86?w=400&h=400&fit=crop',
                description: 'Bold design with maximum comfort for all-day wear. Perfect for urban lifestyle.',
                rating: 4.3,
                reviewCount: 67,
                quantity: 12,
                variants: [
                    { id: '4-1', size: 'US 8', color: '#ff6b35', quantity: 6 },
                    { id: '4-2', size: 'US 9', color: '#000000', quantity: 6 }
                ]
            }
        ];
        
        this.renderProducts();
        console.log('✅ Initial data loaded');
    }

    renderProducts() {
        const productsGrid = document.querySelector('.products-grid');
        if (!productsGrid) {
            console.warn('Products grid not found');
            return;
        }
        
        console.log('🎨 Rendering products...');
        
        productsGrid.innerHTML = this.products.map(product => `
            <div class="product-card" data-product-id="${product.id}">
                <div class="product-image">
                    <a href="/products/detail.php?id=${product.id}" class="product-image-link">
                        <img src="${product.image}" alt="${product.name}" 
                             onerror="this.src='/assets/images/products/placeholder.jpg'">
                    </a>
                    
                    ${product.comparePrice ? `<div class="discount-badge">-${Math.round((1 - product.price / product.comparePrice) * 100)}%</div>` : ''}
                    
                    <div class="product-actions">
                        <button class="action-btn wishlist-btn" 
                                data-product-id="${product.id}"
                                title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        
                        <button class="action-btn quick-view-btn" 
                                data-product-id="${product.id}"
                                title="Quick View">
                            <i class="fas fa-eye"></i>
                        </button>
                        
                        <button class="action-btn compare-btn" 
                                data-product-id="${product.id}"
                                title="Add to Compare">
                            <i class="fas fa-exchange-alt"></i>
                        </button>
                    </div>
                    
                    ${product.variants && product.variants.length > 0 ? `
                        <div class="color-options">
                            ${product.variants.slice(0, 4).map(variant => `
                                <div class="color-dot" 
                                     style="background-color: ${variant.color}"
                                     data-variant-id="${variant.id}"
                                     title="Color variant"></div>
                            `).join('')}
                            ${product.variants.length > 4 ? `
                                <div class="color-more" title="More colors">
                                    +${product.variants.length - 4}
                                </div>
                            ` : ''}
                        </div>
                    ` : ''}
                </div>
                
                <div class="product-info">
                    <div class="product-meta">
                        <a href="/products/brand.php?brand=${product.brand.toLowerCase()}" class="product-brand">
                            ${product.brand}
                        </a>
                    </div>
                    
                    <h3 class="product-name">
                        <a href="/products/detail.php?id=${product.id}">
                            ${product.name}
                        </a>
                    </h3>
                    
                    <p class="product-description">
                        ${product.description}
                    </p>
                    
                    <div class="product-rating">
                        <div class="stars">
                            ${this.generateStarRating(product.rating)}
                        </div>
                        <span class="rating-count">(${product.reviewCount})</span>
                    </div>
                    
                    <div class="product-price">
                        <span class="current-price">${this.formatPrice(product.price)}</span>
                        ${product.comparePrice ? `<span class="compare-price">${this.formatPrice(product.comparePrice)}</span>` : ''}
                    </div>
                    
                    ${product.variants && product.variants.length > 0 ? `
                        <div class="size-options">
                            ${Array.from(new Set(product.variants.map(v => v.size))).slice(0, 5).map(size => `
                                <span class="size-option">${size}</span>
                            `).join('')}
                        </div>
                    ` : ''}
                    
                    <div class="product-actions-bottom">
                        ${product.quantity > 0 ? `
                            <button class="btn btn-primary add-to-cart-btn" 
                                    data-product-id="${product.id}">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        ` : `
                            <button class="btn btn-secondary notify-me-btn" 
                                    data-product-id="${product.id}"
                                    disabled>
                                <i class="fas fa-bell"></i>
                                Notify When Available
                            </button>
                        `}
                    </div>
                    
                    <div class="product-features">
                        <div class="feature-tag">
                            <i class="fas fa-shipping-fast"></i>
                            Free Shipping
                        </div>
                        <div class="feature-tag">
                            <i class="fas fa-undo"></i>
                            Easy Returns
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
        
        console.log('✅ Products rendered successfully');
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
        
        // Parallax effects
        this.handleParallax();
    }

    handleResize() {
        // Close mobile menu on resize to desktop
        if (window.innerWidth > 768) {
            this.closeMobileMenu();
        }
    }

    handleParallax() {
        const hero = document.querySelector('.hero');
        if (!hero) return;
        
        const scrolled = window.pageYOffset;
        const parallaxSpeed = 0.5;
        hero.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
    }

    handleEscapeKey() {
        // Close modals
        const modals = document.querySelectorAll('.modal-overlay');
        modals.forEach(modal => this.closeModal(modal));
        
        // Close mobile menu
        this.closeMobileMenu();
        
        // Close search suggestions
        this.hideSearchSuggestions();
    }

    toggleCartPreview() {
        // This would toggle a cart preview sidebar
        this.showNotification('Cart preview feature coming soon!', 'info');
    }

    // ===== UTILITY FUNCTIONS =====
    formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    }

    generateStarRating(rating) {
        const fullStars = Math.floor(rating);
        const halfStar = (rating - fullStars) >= 0.5;
        const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
        
        let stars = '';
        
        // Full stars
        for (let i = 0; i < fullStars; i++) {
            stars += '<i class="fas fa-star active"></i>';
        }
        
        // Half star
        if (halfStar) {
            stars += '<i class="fas fa-star-half-alt active"></i>';
        }
        
        // Empty stars
        for (let i = 0; i < emptyStars; i++) {
            stars += '<i class="far fa-star"></i>';
        }
        
        return stars;
    }

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
                    
                    // Stagger animation for product cards
                    if (entry.target.classList.contains('product-card')) {
                        const delay = Array.from(entry.target.parentNode.children).indexOf(entry.target) * 100;
                        entry.target.style.animationDelay = `${delay}ms`;
                    }
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        // Observe elements for animation
        const elementsToObserve = document.querySelectorAll('.product-card, .brand-card, .section-title');
        elementsToObserve.forEach(el => {
            if (el) observer.observe(el);
        });
    }

    setupErrorHandling() {
        // Global error handler
        window.addEventListener('error', (event) => {
            console.error('Global error:', event.error);
            this.showNotification('An unexpected error occurred', 'error');
        });
        
        // Unhandled promise rejection
        window.addEventListener('unhandledrejection', (event) => {
            console.error('Unhandled promise rejection:', event.reason);
            this.showNotification('An unexpected error occurred', 'error');
        });
    }

    // ===== SERVICE WORKER =====
    initializeServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('✅ Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('❌ Service Worker registration failed:', error);
                });
        }
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

    viewProduct(productId) {
        window.location.href = `/products/detail.php?id=${productId}`;
    }

    // ===== DESTROY =====
    destroy() {
        // Clean up event listeners and resources
        this.isInitialized = false;
        console.log('🧹 StepStyle application destroyed');
    }
}

// ===== APPLICATION BOOTSTRAP =====
// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    console.log('🌐 DOM fully loaded, initializing StepStyle...');
    
    try {
        window.StepStyle = new StepStyle();
        
        // Make it globally available
        window.stepstyle = window.StepStyle;
        
        console.log('🎉 StepStyle application initialized successfully!');
        
    } catch (error) {
        console.error('💥 Failed to initialize StepStyle:', error);
        
        // Emergency fallback: Remove loading screen
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
    console.log('⚡ DOM already ready, initializing immediately...');
    window.StepStyle = new StepStyle();
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = StepStyle;
}