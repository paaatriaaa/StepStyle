class CartManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateCartTotals();
    }

    setupEventListeners() {
        // Quantity buttons
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', this.handleQuantityChange.bind(this));
        });

        // Quantity input
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', this.handleQuantityInput.bind(this));
        });

        // Remove buttons
        document.querySelectorAll('.item-remove').forEach(btn => {
            btn.addEventListener('click', this.handleRemoveItem.bind(this));
        });
    }

    async handleQuantityChange(event) {
        const button = event.currentTarget;
        const action = button.dataset.action;
        const itemElement = button.closest('.cart-item');
        const quantityInput = itemElement.querySelector('.quantity-input');
        let quantity = parseInt(quantityInput.value);

        if (action === 'increase') {
            quantity = Math.min(quantity + 1, 10);
        } else if (action === 'decrease') {
            quantity = Math.max(quantity - 1, 1);
        }

        quantityInput.value = quantity;
        await this.updateItemQuantity(itemElement, quantity);
    }

    async handleQuantityInput(event) {
        const input = event.currentTarget;
        const itemElement = input.closest('.cart-item');
        let quantity = parseInt(input.value);

        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
            input.value = 1;
        } else if (quantity > 10) {
            quantity = 10;
            input.value = 10;
        }

        await this.updateItemQuantity(itemElement, quantity);
    }

    async handleRemoveItem(event) {
        const button = event.currentTarget;
        const itemIndex = button.dataset.itemIndex;
        
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            await this.removeItemFromCart(itemIndex);
        }
    }

    async updateItemQuantity(itemElement, quantity) {
        const itemIndex = itemElement.dataset.itemIndex;
        
        try {
            const response = await fetch('../API/cart/update.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_index: itemIndex,
                    quantity: quantity,
                    csrf_token: this.getCSRFToken()
                })
            });

            const result = await response.json();

            if (result.success) {
                this.updateItemDisplay(itemElement, quantity, result.item_total);
                this.updateCartTotals(result.cart_total, result.cart_count);
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            console.error('Error updating cart:', error);
            this.showNotification('Failed to update cart', 'error');
        }
    }

    async removeItemFromCart(itemIndex) {
        try {
            const response = await fetch('../API/cart/remove.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_index: itemIndex,
                    csrf_token: this.getCSRFToken()
                })
            });

            const result = await response.json();

            if (result.success) {
                // Remove item from DOM
                const itemElement = document.querySelector(`.cart-item[data-item-index="${itemIndex}"]`);
                itemElement.remove();

                this.updateCartTotals(result.cart_total, result.cart_count);
                this.showNotification('Item removed from cart', 'success');

                // Check if cart is empty
                if (result.cart_count === 0) {
                    this.showEmptyCart();
                }
            } else {
                this.showNotification(result.message, 'error');
            }
        } catch (error) {
            console.error('Error removing item from cart:', error);
            this.showNotification('Failed to remove item from cart', 'error');
        }
    }

    updateItemDisplay(itemElement, quantity, itemTotal) {
        const quantityInput = itemElement.querySelector('.quantity-input');
        const itemTotalElement = itemElement.querySelector('.item-total');

        quantityInput.value = quantity;
        itemTotalElement.textContent = `$${parseFloat(itemTotal).toFixed(2)}`;
    }

    updateCartTotals(cartTotal = null, cartCount = null) {
        if (cartTotal !== null) {
            // Update total displays
            document.querySelectorAll('.cart-total').forEach(element => {
                element.textContent = `$${parseFloat(cartTotal).toFixed(2)}`;
            });
        }

        if (cartCount !== null && window.stepstyle) {
            window.stepstyle.updateCartCount(cartCount);
        }
    }

    showEmptyCart() {
        const cartContent = document.querySelector('.cart-content');
        cartContent.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="../products/categories.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i>
                    Start Shopping
                </a>
            </div>
        `;
    }

    showNotification(message, type = 'info') {
        // Use the main app's notification system if available
        if (window.stepstyle) {
            window.stepstyle.showNotification(message, type);
        } else {
            // Fallback notification
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `;
            
            document.body.appendChild(notification);
            setTimeout(() => notification.classList.add('show'), 100);
            
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
            
            notification.querySelector('.notification-close').addEventListener('click', () => {
                notification.remove();
            });
        }
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
}

// Initialize cart manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CartManager();
});