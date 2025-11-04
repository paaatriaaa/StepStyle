// Checkout Page Functionality
class CheckoutManager {
    constructor() {
        this.currentStep = 1;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupPaymentTabs();
        this.setupBillingAddress();
        this.calculateShipping();
    }

    setupEventListeners() {
        // Next step buttons
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', this.handleNextStep.bind(this));
        });

        // Previous step buttons
        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', this.handlePrevStep.bind(this));
        });

        // Shipping method changes
        document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
            radio.addEventListener('change', this.handleShippingChange.bind(this));
        });

        // Form submission
        const form = document.getElementById('checkout-form');
        if (form) {
            form.addEventListener('submit', this.handleFormSubmit.bind(this));
        }
    }

    setupPaymentTabs() {
        const paymentTabs = document.querySelectorAll('.payment-tab');
        
        paymentTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const method = tab.dataset.method;
                
                // Update active tab
                paymentTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                // Show corresponding form
                document.querySelectorAll('.payment-form').forEach(form => {
                    form.classList.remove('active');
                });
                document.getElementById(`${method}-form`).classList.add('active');
            });
        });
    }

    setupBillingAddress() {
        const sameAsShipping = document.getElementById('same-as-shipping');
        const billingFields = document.getElementById('billing-fields');
        
        sameAsShipping.addEventListener('change', function() {
            if (this.checked) {
                billingFields.style.display = 'none';
            } else {
                billingFields.style.display = 'block';
            }
        });
    }

    handleNextStep(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const nextStep = button.dataset.next;
        
        if (this.validateCurrentStep()) {
            this.goToStep(nextStep);
        }
    }

    handlePrevStep(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const prevStep = button.dataset.prev;
        
        this.goToStep(prevStep);
    }

    goToStep(stepName) {
        // Hide all steps
        document.querySelectorAll('.form-section').forEach(section => {
            section.classList.remove('active');
        });
        
        // Show target step
        document.getElementById(`${stepName}-section`).classList.add('active');
        
        // Update steps UI
        document.querySelectorAll('.step').forEach(step => {
            step.classList.remove('active');
        });
        
        const stepNumber = stepName === 'shipping' ? 1 : 2;
        document.querySelector(`.step[data-step="${stepNumber}"]`).classList.add('active');
        
        this.currentStep = stepNumber;
    }

    validateCurrentStep() {
        if (this.currentStep === 1) {
            return this.validateShippingStep();
        }
        return true;
    }

    validateShippingStep() {
        const requiredFields = document.querySelectorAll('#shipping-section [required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = 'var(--danger)';
                isValid = false;
            } else {
                field.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            window.StepStyle.showNotification('Please fill in all required fields', 'error');
        }
        
        return isValid;
    }

    handleShippingChange(event) {
        this.calculateShipping();
    }

    calculateShipping() {
        const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
        if (selectedShipping) {
            const shippingCost = parseFloat(selectedShipping.dataset.cost);
            const shippingElement = document.getElementById('shipping-cost');
            const orderTotalElement = document.getElementById('order-total');
            
            // Update shipping cost display
            if (shippingCost === 0) {
                shippingElement.textContent = 'FREE';
            } else {
                shippingElement.textContent = `$${shippingCost.toFixed(2)}`;
            }
            
            // This would typically recalculate the total from server
            console.log('Shipping cost updated:', shippingCost);
        }
    }

    handleFormSubmit(event) {
        event.preventDefault();
        
        if (this.validateCurrentStep()) {
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitBtn.disabled = true;
            
            // Simulate payment processing
            setTimeout(() => {
                // Store order data in session (in real app, this would be server-side)
                const orderData = {
                    order_id: 'STP' + Date.now(),
                    total: 328.97,
                    subtotal: 299.99,
                    shipping_cost: 0,
                    tax: 28.98,
                    discount: 0,
                    payment_method: 'Credit Card',
                    items: [
                        {
                            name: 'Nike Air Max 270',
                            brand: 'Nike',
                            price: 149.99,
                            quantity: 1,
                            size: 'US 10',
                            image_url: '../assets/images/products/nike-air-max-270.jpg'
                        },
                        {
                            name: 'Adidas Ultraboost 21',
                            brand: 'Adidas',
                            price: 180.00,
                            quantity: 2,
                            size: 'US 9.5',
                            image_url: '../assets/images/products/adidas-ultraboost-21.jpg'
                        }
                    ],
                    shipping_address: {
                        full_name: document.getElementById('shipping-fullname').value,
                        address: document.getElementById('shipping-address').value,
                        city: document.getElementById('shipping-city').value,
                        state: document.getElementById('shipping-state').value,
                        zip_code: document.getElementById('shipping-zip').value
                    }
                };
                
                // Store in session storage for payment page
                sessionStorage.setItem('current_order', JSON.stringify(orderData));
                
                // Redirect to payment page
                window.location.href = 'payment.php';
                
            }, 2000);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new CheckoutManager();
});