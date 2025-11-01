// Wishlist JavaScript for StepStyle

document.addEventListener('DOMContentLoaded', function() {
    initializeWishlistPage();
});

function initializeWishlistPage() {
    // Remove item functionality
    initializeRemoveWishlistItem();
    
    // Add to cart from wishlist
    initializeAddToCartFromWishlist();
    
    // Move to cart functionality
    initializeMoveToCart();
    
    // Size and color selection
    initializeProductOptions();
    
    // Share wishlist
    initializeShareWishlist();
    
    // Price drop alerts
    initializePriceAlerts();
    
    // Add all to cart
    initializeAddAllToCart();
    
    // Clear all items
    initializeClearAll();
}

function initializeRemoveWishlistItem() {
    const removeButtons = document.querySelectorAll('.btn-remove-wishlist');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            const wishlistItem = this.closest('.wishlist-item');
            const productName = wishlistItem.querySelector('.item-name').textContent;
            
            removeWishlistItem(wishlistItem, itemId, productName);
        });
    });
}

function removeWishlistItem(wishlistItem, itemId, productName) {
    // Add removal animation
    wishlistItem.style.transform = 'scale(0.9)';
    wishlistItem.style.opacity = '0';
    
    setTimeout(() => {
        wishlistItem.remove();
        
        // Update wishlist stats
        updateWishlistStats();
        
        // Update wishlist badge
        window.StepStyle.updateWishlistBadge(Math.max(0, parseInt(document.querySelector('#wishlist-btn .badge').textContent) - 1));
        
        // Show empty wishlist if needed
        checkEmptyWishlist();
        
        window.StepStyle.showNotification(`💔 "${productName}" removed from wishlist`, 'info');
    }, 300);
}

function initializeAddToCartFromWishlist() {
    const addToCartButtons = document.querySelectorAll('.btn-add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const wishlistItem = this.closest('.wishlist-item');
            const productName = wishlistItem.querySelector('.item-name').textContent;
            
            addToCartFromWishlist(productId, wishlistItem, productName);
        });
    });
}

function addToCartFromWishlist(productId, wishlistItem, productName) {
    // Validate size and color selection
    if (!validateProductOptions(wishlistItem)) {
        return;
    }
    
    // Show loading state
    const button = wishlistItem.querySelector('.btn-add-to-cart');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    button.disabled = true;
    
    // Simulate adding to cart
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        window.StepStyle.showNotification(`🛒 "${productName}" added to cart!`, 'success');
        window.StepStyle.updateCartBadge(Math.floor(Math.random() * 5) + 1);
        
        // Optional: Remove from wishlist after adding to cart
        // removeWishlistItem(wishlistItem, productId, productName);
    }, 1000);
}

function initializeMoveToCart() {
    const moveButtons = document.querySelectorAll('.btn-move-to-cart');
    
    moveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const wishlistItem = this.closest('.wishlist-item');
            const productName = wishlistItem.querySelector('.item-name').textContent;
            
            moveToCart(productId, wishlistItem, productName);
        });
    });
}

function moveToCart(productId, wishlistItem, productName) {
    // Validate size and color selection
    if (!validateProductOptions(wishlistItem)) {
        return;
    }
    
    // Show loading state
    const button = wishlistItem.querySelector('.btn-move-to-cart');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Moving...';
    button.disabled = true;
    
    // Simulate moving to cart
    setTimeout(() => {
        // Remove from wishlist
        wishlistItem.style.transform = 'translateX(100%)';
        wishlistItem.style.opacity = '0';
        
        setTimeout(() => {
            wishlistItem.remove();
            
            // Update wishlist stats and badge
            updateWishlistStats();
            window.StepStyle.updateWishlistBadge(Math.max(0, parseInt(document.querySelector('#wishlist-btn .badge').textContent) - 1));
            
            // Update cart badge
            window.StepStyle.updateCartBadge(Math.floor(Math.random() * 5) + 1);
            
            window.StepStyle.showNotification(`➡️ "${productName}" moved to cart!`, 'success');
            checkEmptyWishlist();
        }, 300);
    }, 800);
}

function initializeProductOptions() {
    // Size selection
    const sizeSelects = document.querySelectorAll('.size-select');
    sizeSelects.forEach(select => {
        select.addEventListener('change', function() {
            updateProductOption(this, 'size');
        });
    });
    
    // Color selection
    const colorSelectors = document.querySelectorAll('.color-selector input');
    colorSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            updateProductOption(this, 'color');
        });
    });
}

function updateProductOption(element, type) {
    const wishlistItem = element.closest('.wishlist-item');
    
    if (type === 'color') {
        // Update active color
        const colorSelectors = wishlistItem.querySelectorAll('.color-selector');
        colorSelectors.forEach(selector => {
            selector.classList.remove('active');
        });
        element.closest('.color-selector').classList.add('active');
    }
    
    // Enable add to cart buttons if both size and color are selected
    validateProductOptions(wishlistItem);
}

function validateProductOptions(wishlistItem) {
    const sizeSelect = wishlistItem.querySelector('.size-select');
    const colorSelected = wishlistItem.querySelector('.color-selector input:checked');
    const addToCartBtn = wishlistItem.querySelector('.btn-add-to-cart');
    const moveToCartBtn = wishlistItem.querySelector('.btn-move-to-cart');
    
    const isValid = sizeSelect.value && colorSelected;
    
    // Enable/disable buttons based on selection
    [addToCartBtn, moveToCartBtn].forEach(btn => {
        if (btn) {
            btn.disabled = !isValid;
            btn.style.opacity = isValid ? '1' : '0.6';
        }
    });
    
    return isValid;
}

function initializeShareWishlist() {
    const shareButton = document.querySelector('.btn-share-wishlist');
    const shareButtons = document.querySelectorAll('.btn-share');
    
    if (shareButton) {
        shareButton.addEventListener('click', shareWishlist);
    }
    
    shareButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.querySelector('i').className.includes('facebook') ? 'facebook' :
                           this.querySelector('i').className.includes('twitter') ? 'twitter' :
                           this.querySelector('i').className.includes('whatsapp') ? 'whatsapp' : 'link';
            
            shareWishlistOnPlatform(platform);
        });
    });
}

function shareWishlist() {
    // Create share modal or copy link to clipboard
    const wishlistUrl = window.location.href;
    
    // Try to use Clipboard API
    if (navigator.clipboard) {
        navigator.clipboard.writeText(wishlistUrl).then(() => {
            window.StepStyle.showNotification('🔗 Wishlist link copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(wishlistUrl);
        });
    } else {
        fallbackCopyToClipboard(wishlistUrl);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand('copy');
    document.body.removeChild(textArea);
    
    window.StepStyle.showNotification('🔗 Wishlist link copied to clipboard!', 'success');
}

function shareWishlistOnPlatform(platform) {
    const wishlistUrl = encodeURIComponent(window.location.href);
    const wishlistTitle = encodeURIComponent('My StepStyle Wishlist');
    
    let shareUrl = '';
    
    switch (platform) {
        case 'facebook':
            shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${wishlistUrl}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?text=${wishlistTitle}&url=${wishlistUrl}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${wishlistTitle} ${wishlistUrl}`;
            break;
        default:
            shareWishlist();
            return;
    }
    
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function initializePriceAlerts() {
    const alertButton = document.querySelector('.btn-enable-alerts');
    
    if (alertButton) {
        alertButton.addEventListener('click', function() {
            enablePriceAlerts(this);
        });
    }
}

function enablePriceAlerts(button) {
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enabling...';
    button.disabled = true;
    
    // Simulate enabling alerts
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-bell-slash"></i> Disable Alerts';
        button.disabled = false;
        button.classList.remove('btn-outline');
        button.classList.add('btn-primary');
        
        window.StepStyle.showNotification('🔔 Price drop alerts enabled!', 'success');
        
        // Update click handler to disable
        button.onclick = function() { disablePriceAlerts(this); };
    }, 1500);
}

function disablePriceAlerts(button) {
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Disabling...';
    button.disabled = true;
    
    // Simulate disabling alerts
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-bell"></i> Enable Alerts';
        button.disabled = false;
        button.classList.remove('btn-primary');
        button.classList.add('btn-outline');
        
        window.StepStyle.showNotification('🔕 Price drop alerts disabled', 'info');
        
        // Update click handler to enable
        button.onclick = function() { enablePriceAlerts(this); };
    }, 1500);
}

function initializeAddAllToCart() {
    const addAllButton = document.querySelector('.btn-add-all-to-cart');
    
    if (addAllButton) {
        addAllButton.addEventListener('click', function() {
            addAllToCart(this);
        });
    }
}

function addAllToCart(button) {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const validItems = Array.from(wishlistItems).filter(item => validateProductOptions(item));
    
    if (validItems.length === 0) {
        window.StepStyle.showNotification('Please select size and color for all items', 'warning');
        return;
    }
    
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Adding ${validItems.length} items...`;
    button.disabled = true;
    
    // Simulate adding all items to cart
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        window.StepStyle.showNotification(`🛒 Added ${validItems.length} items to cart!`, 'success');
        window.StepStyle.updateCartBadge(validItems.length);
    }, 2000);
}

function initializeClearAll() {
    const clearButton = document.querySelector('.btn-clear-all');
    
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            clearAllWishlistItems(this);
        });
    }
}

function clearAllWishlistItems(button) {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    
    if (wishlistItems.length === 0) {
        window.StepStyle.showNotification('Wishlist is already empty', 'info');
        return;
    }
    
    // Create confirmation modal
    const modal = document.createElement('div');
    modal.className = 'confirmation-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Clear Wishlist</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove all ${wishlistItems.length} items from your wishlist?</p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-cancel">Cancel</button>
                <button class="btn btn-danger btn-confirm-clear">Clear All</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Event listeners
    modal.querySelector('.modal-close').addEventListener('click', closeModal);
    modal.querySelector('.btn-cancel').addEventListener('click', closeModal);
    modal.querySelector('.btn-confirm-clear').addEventListener('click', function() {
        clearAllItems();
        closeModal();
    });
    
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }
}

function clearAllItems() {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    
    // Add removal animation to all items
    wishlistItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.transform = 'scale(0.8)';
            item.style.opacity = '0';
            
            setTimeout(() => {
                item.remove();
            }, 300);
        }, index * 100);
    });
    
    // Update stats after all animations complete
    setTimeout(() => {
        updateWishlistStats();
        window.StepStyle.updateWishlistBadge(0);
        checkEmptyWishlist();
        
        window.StepStyle.showNotification('🗑️ All items removed from wishlist', 'info');
    }, wishlistItems.length * 100 + 300);
}

function updateWishlistStats() {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const itemsCount = document.querySelector('.items-count');
    const statValue = document.querySelector('.stat-value');
    
    if (itemsCount) {
        itemsCount.textContent = `${wishlistItems.length} items`;
    }
    
    if (statValue) {
        // Calculate total value (simplified)
        let totalValue = 0;
        wishlistItems.forEach(item => {
            const priceText = item.querySelector('.current-price').textContent;
            const price = parseFloat(priceText.replace('$', ''));
            totalValue += price;
        });
        
        statValue.textContent = `$${totalValue.toFixed(2)}`;
    }
}

function checkEmptyWishlist() {
    const wishlistItems = document.querySelectorAll('.wishlist-item');
    const emptyWishlist = document.querySelector('.empty-wishlist');
    const wishlistContent = document.querySelector('.wishlist-content');
    
    if (wishlistItems.length === 0 && !emptyWishlist) {
        // Show empty wishlist message
        const emptyHTML = `
            <div class="empty-wishlist">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h2>Your wishlist is empty</h2>
                <p>Save your favorite items here for easy access later</p>
                <a href="../products/categories/sneakers.php" class="btn btn-primary">
                    <i class="fas fa-shoe-prints"></i>
                    Explore Products
                </a>
            </div>
        `;
        
        document.querySelector('.wishlist-items').innerHTML = emptyHTML;
    }
}

// Add CSS for wishlist page
const wishlistStyles = document.createElement('style');
wishlistStyles.textContent = `
    .color-selector.active .color-dot {
        transform: scale(1.2);
        box-shadow: 0 0 0 2px #667eea;
    }
    
    .color-dot {
        transition: all 0.3s ease;
    }
    
    .size-select {
        transition: all 0.3s ease;
    }
    
    .wishlist-item {
        transition: all 0.3s ease;
    }
    
    .btn-share {
        transition: all 0.3s ease;
    }
    
    .btn-share:hover {
        transform: translateY(-2px);
    }
    
    .warning-text {
        color: #e74c3c;
        font-size: 0.9rem;
        margin-top: 10px;
    }
    
    .stat {
        text-align: center;
        padding: 15px;
    }
    
    .stat-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: #7f8c8d;
    }
    
    @keyframes wishlistPulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .wishlist-item.adding {
        animation: wishlistPulse 0.5s ease;
    }
`;
document.head.appendChild(wishlistStyles);