// Payment Page Functionality
class PaymentManager {
    constructor() {
        this.init();
    }

    init() {
        this.displayOrderSummary();
        this.setupSecurityFeatures();
    }

    displayOrderSummary() {
        // Get order data from session storage
        const orderData = JSON.parse(sessionStorage.getItem('current_order'));
        
        if (!orderData) {
            window.location.href = 'checkout.php';
            return;
        }
        
        // Update order details
        this.updateOrderDetails(orderData);
    }

    updateOrderDetails(orderData) {
        // Update processing details
        const detailItems = document.querySelectorAll('.detail-item');
        
        detailItems.forEach(item => {
            const label = item.querySelector('.detail-label').textContent;
            const valueElement = item.querySelector('.detail-value');
            
            switch(label) {
                case 'Order Total:':
                    valueElement.textContent = `$${orderData.total.toFixed(2)}`;
                    break;
                case 'Payment Method:':
                    valueElement.textContent = orderData.payment_method;
                    break;
                case 'Order ID:':
                    valueElement.textContent = `#${orderData.order_id}`;
                    break;
            }
        });
        
        // Update shipping info
        const shippingInfo = document.querySelector('.shipping-info-payment p');
        if (shippingInfo && orderData.shipping_address) {
            const address = orderData.shipping_address;
            shippingInfo.innerHTML = `
                ${address.full_name}<br>
                ${address.address}<br>
                ${address.city}, ${address.state} ${address.zip_code}
            `;
        }
    }

    setupSecurityFeatures() {
        // Add security feature animations
        const securityItems = document.querySelectorAll('.security-item');
        
        securityItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.2}s`;
            item.classList.add('animate-in');
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new PaymentManager();
});