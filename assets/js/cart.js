// Cart JavaScript for StepStyle

document.addEventListener('DOMContentLoaded', function() {
    initializeCartPage();
});

function initializeCartPage() {
    // Quantity controls
    initializeQuantityControls();
    
    // Remove item functionality
    initializeRemoveItem();
    
    // Save for later functionality
    initializeSaveForLater();
    
    // Promo code functionality
    initializePromoCode();
    
    // Cart summary updates
    initializeCartSummary();
    
    // Continue shopping
    initializeContinueShopping();
    
    // Proceed to checkout
    initializeCheckout();
}

function initializeQuantityControls() {
    const quantityButtons = document.querySelectorAll('.quantity-btn');
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    // Plus/Minus buttons
    quantityButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            const input = this.parentElement.querySelector('.quantity-input');
            updateQuantity(input, action);
        });
    });
    
    // Direct input changes
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            validateQuantity(this);
            updateCartItem(this);
        });
        
        input.addEventListener('blur', function() {
            validateQuantity(this);
        });
    });
}

function updateQuantity(input, action) {
    let quantity = parseInt(input.value);
    
    switch (action) {
        case 'increase':
            quantity++;
            break;
        case 'decrease':
            if (quantity > 1) quantity--;
            break;
    }
    
    input.value = quantity;
    validateQuantity(input);
    updateCartItem(input);
}

function validateQuantity(input) {
    let quantity = parseInt(input.value);
    const max = parseInt(input.getAttribute('max')) || 10;
    const min = parseInt(input.getAttribute('min')) || 1;
    
    if (isNaN(quantity) || quantity < min) {
        quantity = min;
    } else if (quantity > max) {
        quantity = max;
        window.StepStyle.showNotification(`Maximum quantity is ${max}`, 'warning');
    }
    
    input.value = quantity;
}

function updateCartItem(input) {
    const cartItem = input.closest('.cart-item');
    const cartId = cartItem.getAttribute('data-cart-id');
    const quantity = parseInt(input.value);
    const price = parseFloat(cartItem.querySelector('.item-price .price').textContent.replace('$', ''));
    
    // Update item total
    const itemTotal = cartItem.querySelector('.item-total .total');
    itemTotal.textContent = `$${(price * quantity).toFixed(2)}`;
    
    // Update cart summary
    updateCartSummary();
    
    // Show update confirmation
    showUpdateConfirmation(cartItem);
    
    // In a real application, you would send an AJAX request to update the cart
    console.log(`Updating cart item ${cartId} to quantity ${quantity}`);
}

function showUpdateConfirmation(cartItem) {
    const confirmation = document.createElement('div');
    confirmation.className = 'update-confirmation';
    confirmation.textContent = 'Updated';
    confirmation.style.cssText = `
        position: absolute;
        top: 10px;
        right: 10px;
        background: #27ae60;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 600;
        animation: fadeInOut 2s ease;
    `;
    
    cartItem.style.position = 'relative';
    cartItem.appendChild(confirmation);
    
    setTimeout(() => {
        confirmation.remove();
    }, 2000);
}

function initializeRemoveItem() {
    const removeButtons = document.querySelectorAll('.btn-remove');
    
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const cartId = cartItem.getAttribute('data-cart-id');
            const productName = cartItem.querySelector('.item-name').textContent;
            
            showRemoveConfirmation(cartItem, productName, cartId);
        });
    });
}

function showRemoveConfirmation(cartItem, productName, cartId) {
    // Create confirmation modal
    const modal = document.createElement('div');
    modal.className = 'confirmation-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>Remove Item</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to remove "<strong>${productName}</strong>" from your cart?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-cancel">Cancel</button>
                <button class="btn btn-danger btn-confirm-remove">Remove Item</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Show modal
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Event listeners
    modal.querySelector('.modal-close').addEventListener('click', closeModal);
    modal.querySelector('.btn-cancel').addEventListener('click', closeModal);
    modal.querySelector('.btn-confirm-remove').addEventListener('click', function() {
        removeCartItem(cartItem, cartId);
        closeModal();
    });
    
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => modal.remove(), 300);
    }
}

function removeCartItem(cartItem, cartId) {
    // Add removal animation
    cartItem.style.transform = 'translateX(100%)';
    cartItem.style.opacity = '0';
    
    setTimeout(() => {
        cartItem.remove();
        
        // Update cart summary
        updateCartSummary();
        
        // Update cart badge
        window.StepStyle.updateCartBadge(Math.max(0, parseInt(document.querySelector('#cart-btn .badge').textContent) - 1));
        
        // Show empty cart if needed
        checkEmptyCart();
        
        window.StepStyle.showNotification('🗑️ Item removed from cart', 'info');
    }, 300);
    
    // In a real application, you would send an AJAX request to remove the item
    console.log(`Removing cart item ${cartId}`);
}

function initializeSaveForLater() {
    const saveButtons = document.querySelectorAll('.btn-save-later');
    
    saveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartItem = this.closest('.cart-item');
            const productName = cartItem.querySelector('.item-name').textContent;
            
            // Move to save for later
            moveToSaveForLater(cartItem, productName);
        });
    });
}

function moveToSaveForLater(cartItem, productName) {
    // Add animation
    cartItem.style.transform = 'scale(0.9)';
    cartItem.style.opacity = '0.5';
    
    setTimeout(() => {
        cartItem.remove();
        updateCartSummary();
        window.StepStyle.updateCartBadge(Math.max(0, parseInt(document.querySelector('#cart-btn .badge').textContent) - 1));
        
        window.StepStyle.showNotification(`💾 "${productName}" saved for later`, 'success');
        checkEmptyCart();
    }, 500);
}

function initializePromoCode() {
    const promoInput = document.querySelector('.promo-input input');
    const applyButton = document.querySelector('.promo-input .btn');
    const promoButtons = document.querySelectorAll('.promo-tag');
    
    if (applyButton) {
        applyButton.addEventListener('click', applyPromoCode);
    }
    
    if (promoInput) {
        promoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyPromoCode();
            }
        });
    }
    
    // Promo tag buttons
    promoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const promoCode = this.textContent;
            document.querySelector('.promo-input input').value = promoCode;
            applyPromoCode();
        });
    });
}

function applyPromoCode() {
    const promoInput = document.querySelector('.promo-input input');
    const promoCode = promoInput.value.trim();
    
    if (!promoCode) {
        window.StepStyle.showNotification('Please enter a promo code', 'warning');
        return;
    }
    
    // Show loading state
    const applyButton = document.querySelector('.promo-input .btn');
    const originalText = applyButton.innerHTML;
    applyButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    applyButton.disabled = true;
    
    // Simulate promo code validation
    setTimeout(() => {
        applyButton.innerHTML = originalText;
        applyButton.disabled = false;
        
        const validPromoCodes = {
            'WELCOME15': 0.15,
            'FREESHIP': 'free-shipping',
            'SAVE20': 0.20
        };
        
        if (validPromoCodes[promoCode]) {
            const discount = validPromoCodes[promoCode];
            applyDiscount(discount, promoCode);
        } else {
            window.StepStyle.showNotification('Invalid promo code', 'error');
        }
    }, 1500);
}

function applyDiscount(discount, promoCode) {
    // This would apply the discount to the cart total
    // For demo purposes, we'll just show a success message
    
    let message = 'Promo code applied! ';
    
    if (discount === 'free-shipping') {
        message += 'Free shipping activated!';
    } else {
        message += `${(discount * 100)}% discount applied!`;
    }
    
    window.StepStyle.showNotification(`🎉 ${message}`, 'success');
    
    // Disable promo input
    const promoInput = document.querySelector('.promo-input input');
    const applyButton = document.querySelector('.promo-input .btn');
    
    promoInput.disabled = true;
    applyButton.disabled = true;
    applyButton.textContent = 'Applied';
    
    // Update cart summary with discount
    updateCartSummaryWithDiscount(discount, promoCode);
}

function updateCartSummaryWithDiscount(discount, promoCode) {
    // This would update the cart summary to show the applied discount
    const summaryCard = document.querySelector('.summary-card');
    
    // Remove existing discount row if any
    const existingDiscount = summaryCard.querySelector('.summary-row.discount');
    if (existingDiscount) {
        existingDiscount.remove();
    }
    
    // Add discount row
    const discountRow = document.createElement('div');
    discountRow.className = 'summary-row discount';
    
    if (discount === 'free-shipping') {
        discountRow.innerHTML = `
            <span>Shipping Discount</span>
            <span style="color: #27ae60;">FREE</span>
        `;
    } else {
        discountRow.innerHTML = `
            <span>Discount (${promoCode})</span>
            <span style="color: #27ae60;">-${(discount * 100)}%</span>
        `;
    }
    
    const shippingRow = summaryCard.querySelector('.summary-row:nth-child(2)');
    shippingRow.parentNode.insertBefore(discountRow, shippingRow.nextSibling);
}

function initializeCartSummary() {
    // Initial cart summary calculation
    updateCartSummary();
}

function updateCartSummary() {
    const cartItems = document.querySelectorAll('.cart-item');
    let subtotal = 0;
    
    cartItems.forEach(item => {
        const price = parseFloat(item.querySelector('.item-price .price').textContent.replace('$', ''));
        const quantity = parseInt(item.querySelector('.quantity-input').value);
        subtotal += price * quantity;
    });
    
    // Update subtotal in summary
    const subtotalElement = document.querySelector('.summary-row:nth-child(1) span:last-child');
    if (subtotalElement) {
        subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    }
    
    // Update total (simplified - in real app you'd calculate shipping and tax)
    const totalElement = document.querySelector('.summary-row.total span:last-child');
    if (totalElement) {
        const shipping = subtotal > 50 ? 0 : 10;
        const tax = subtotal * 0.08;
        const total = subtotal + shipping + tax;
        totalElement.textContent = `$${total.toFixed(2)}`;
    }
}

function initializeContinueShopping() {
    const continueButtons = document.querySelectorAll('a[href*="products"]');
    
    continueButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add smooth transition
            document.body.style.opacity = '0.7';
            
            setTimeout(() => {
                window.location.href = this.href;
            }, 300);
        });
    });
}

function initializeCheckout() {
    const checkoutButton = document.querySelector('.btn-checkout');
    
    if (checkoutButton) {
        checkoutButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const cartItems = document.querySelectorAll('.cart-item');
            
            if (cartItems.length === 0) {
                window.StepStyle.showNotification('Your cart is empty', 'warning');
                return;
            }
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing...';
            this.disabled = true;
            
            // Simulate checkout preparation
            setTimeout(() => {
                window.location.href = 'checkout.php';
            }, 1000);
        });
    }
}

function checkEmptyCart() {
    const cartItems = document.querySelectorAll('.cart-item');
    const emptyCart = document.querySelector('.empty-cart');
    const cartContent = document.querySelector('.cart-items-section');
    
    if (cartItems.length === 0 && !emptyCart) {
        // Show empty cart message
        const emptyHTML = `
            <div class="empty-cart">
                <div class="empty-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p>Discover our amazing collection and add some items to your cart</p>
                <a href="../products/categories/sneakers.php" class="btn btn-primary">
                    <i class="fas fa-shoe-prints"></i>
                    Start Shopping
                </a>
            </div>
        `;
        
        cartContent.innerHTML = emptyHTML;
    }
}

// Add CSS for cart page
const cartStyles = document.createElement('style');
cartStyles.textContent = `
    .confirmation-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .confirmation-modal.show {
        opacity: 1;
    }
    
    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 30px;
        max-width: 400px;
        width: 90%;
        transform: scale(0.7);
        transition: transform 0.3s ease;
    }
    
    .confirmation-modal.show .modal-content {
        transform: scale(1);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .modal-header h3 {
        margin: 0;
        color: #2c3e50;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #7f8c8d;
    }
    
    .modal-body {
        margin-bottom: 25px;
    }
    
    .modal-footer {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }
    
    .btn-danger {
        background: #e74c3c;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c0392b;
    }
    
    @keyframes fadeInOut {
        0% { opacity: 0; transform: translateY(-10px); }
        50% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-10px); }
    }
    
    .cart-item {
        transition: all 0.3s ease;
    }
    
    .quantity-controls {
        transition: all 0.3s ease;
    }
    
    .btn-save-later:hover {
        transform: translateX(5px);
    }
    
    .promo-tag {
        transition: all 0.3s ease;
    }
    
    .promo-tag:hover {
        transform: translateY(-2px);
    }
`;
document.head.appendChild(cartStyles);