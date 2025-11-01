// Checkout JavaScript for StepStyle

document.addEventListener('DOMContentLoaded', function() {
    initializeCheckoutPage();
});

function initializeCheckoutPage() {
    // Form validation
    initializeCheckoutForm();
    
    // Address type toggle
    initializeAddressType();
    
    // Shipping method selection
    initializeShippingMethods();
    
    // Payment method selection
    initializePaymentMethods();
    
    // Card form handling
    initializeCardForm();
    
    // Order summary
    initializeOrderSummary();
    
    // Place order functionality
    initializePlaceOrder();
    
    // Promo code application
    initializeCheckoutPromo();
}

function initializeCheckoutForm() {
    const form = document.getElementById('checkout-form');
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    
    // Real-time validation
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateCheckoutField(this);
        });
        
        input.addEventListener('input', function() {
            clearCheckoutFieldError(this);
        });
    });
    
    // Email validation
    const emailInput = form.querySelector('input[type="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            validateEmailField(this);
        });
    }
    
    // Phone validation
    const phoneInput = form.querySelector('input[type="tel"]');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            validatePhoneField(this);
        });
    }
    
    // Card number formatting
    const cardNumberInput = form.querySelector('#card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            formatCardNumber(this);
        });
    }
    
    // Card expiry formatting
    const cardExpiryInput = form.querySelector('#card-expiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', function() {
            formatCardExpiry(this);
        });
    }
    
    // CVV help
    const cvvHelp = form.querySelector('.cvv-help');
    if (cvvHelp) {
        cvvHelp.addEventListener('click', function() {
            showCVVHelp();
        });
    }
}

function validateCheckoutField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';
    
    if (!value) {
        isValid = false;
        message = 'This field is required';
    } else {
        switch (field.type) {
            case 'email':
                if (!isValidEmail(value)) {
                    isValid = false;
                    message = 'Please enter a valid email address';
                }
                break;
            case 'tel':
                if (!isValidPhone(value)) {
                    isValid = false;
                    message = 'Please enter a valid phone number';
                }
                break;
        }
        
        // Special validation for specific fields
        switch (field.id) {
            case 'card-number':
                if (!isValidCardNumber(value)) {
                    isValid = false;
                    message = 'Please enter a valid card number';
                }
                break;
            case 'card-expiry':
                if (!isValidCardExpiry(value)) {
                    isValid = false;
                    message = 'Please enter a valid expiry date (MM/YY)';
                }
                break;
            case 'card-cvv':
                if (!isValidCVV(value)) {
                    isValid = false;
                    message = 'Please enter a valid CVV';
                }
                break;
            case 'zip':
                if (!isValidZIP(value)) {
                    isValid = false;
                    message = 'Please enter a valid ZIP code';
                }
                break;
        }
    }
    
    if (isValid) {
        showCheckoutFieldSuccess(field);
    } else {
        showCheckoutFieldError(field, message);
    }
    
    return isValid;
}

function validateEmailField(field) {
    const value = field.value.trim();
    
    if (value && !isValidEmail(value)) {
        showCheckoutFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    return true;
}

function validatePhoneField(field) {
    const value = field.value.trim();
    
    if (value && !isValidPhone(value)) {
        showCheckoutFieldError(field, 'Please enter a valid phone number');
        return false;
    }
    
    return true;
}

function showCheckoutFieldError(field, message) {
    clearCheckoutFieldError(field);
    
    field.classList.add('error');
    field.classList.remove('success');
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    field.parentElement.appendChild(errorElement);
}

function showCheckoutFieldSuccess(field) {
    clearCheckoutFieldError(field);
    
    field.classList.remove('error');
    field.classList.add('success');
}

function clearCheckoutFieldError(field) {
    field.classList.remove('error', 'success');
    
    const existingError = field.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function initializeAddressType() {
    const addressRadios = document.querySelectorAll('input[name="address-type"]');
    
    addressRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateAddressType(this.value);
        });
    });
}

function updateAddressType(type) {
    // This would show/hide business-specific fields if needed
    console.log(`Address type changed to: ${type}`);
}

function initializeShippingMethods() {
    const shippingRadios = document.querySelectorAll('input[name="shipping"]');
    
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateShippingMethod(this.value);
        });
    });
}

function updateShippingMethod(method) {
    const shippingPrices = {
        'standard': 0,
        'express': 9.99,
        'overnight': 19.99
    };
    
    const shippingPrice = shippingPrices[method];
    
    // Update order summary
    updateOrderSummaryShipping(shippingPrice);
    
    // Show shipping method confirmation
    window.StepStyle.showNotification(`🚚 ${method.charAt(0).toUpperCase() + method.slice(1)} shipping selected`, 'info');
}

function initializePaymentMethods() {
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updatePaymentMethod(this.value);
        });
    });
}

function updatePaymentMethod(method) {
    const cardForm = document.getElementById('card-form');
    
    if (method === 'card') {
        cardForm.style.display = 'block';
        // Animate in
        cardForm.style.opacity = '0';
        cardForm.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            cardForm.style.opacity = '1';
            cardForm.style.transform = 'translateY(0)';
        }, 100);
    } else {
        // Animate out then hide
        cardForm.style.opacity = '0';
        cardForm.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            cardForm.style.display = 'none';
        }, 300);
    }
    
    // Show payment method confirmation
    const methodNames = {
        'card': 'Credit/Debit Card',
        'paypal': 'PayPal',
        'applepay': 'Apple Pay'
    };
    
    window.StepStyle.showNotification(`💳 ${methodNames[method]} selected`, 'info');
}

function initializeCardForm() {
    // Card number formatting and validation
    const cardNumberInput = document.getElementById('card-number');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function() {
            detectCardType(this);
        });
    }
}

function formatCardNumber(input) {
    let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let matches = value.match(/\d{4,16}/g);
    let match = matches && matches[0] || '';
    let parts = [];
    
    for (let i = 0; i < match.length; i += 4) {
        parts.push(match.substring(i, i + 4));
    }
    
    if (parts.length) {
        input.value = parts.join(' ');
    } else {
        input.value = value;
    }
}

function formatCardExpiry(input) {
    let value = input.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    
    if (value.length >= 2) {
        input.value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
}

function detectCardType(input) {
    const value = input.value.replace(/\s+/g, '');
    let cardType = 'unknown';
    
    // Simple card type detection
    if (/^4/.test(value)) {
        cardType = 'visa';
    } else if (/^5[1-5]/.test(value)) {
        cardType = 'mastercard';
    } else if (/^3[47]/.test(value)) {
        cardType = 'amex';
    }
    
    // Update card icon
    const cardIcon = input.parentElement.querySelector('.card-icon');
    if (cardIcon) {
        cardIcon.className = `fab fa-cc-${cardType} card-icon`;
    }
}

function showCVVHelp() {
    window.StepStyle.showNotification('🔒 CVV is the 3-digit code on the back of your card', 'info');
}

function initializeOrderSummary() {
    // Initial order summary calculation
    updateOrderSummary();
}

function updateOrderSummary() {
    // This would calculate totals based on cart items, shipping, tax, etc.
    // For demo, we'll use fixed values
    const subtotal = 189.80;
    const shipping = 0; // Free shipping
    const tax = subtotal * 0.08;
    const total = subtotal + shipping + tax;
    
    updateOrderSummaryDisplay(subtotal, shipping, tax, total);
}

function updateOrderSummaryShipping(shippingCost) {
    const shippingElement = document.querySelector('.summary-row:nth-child(2) span:last-child');
    if (shippingElement) {
        shippingElement.textContent = shippingCost === 0 ? 'FREE' : `$${shippingCost.toFixed(2)}`;
    }
    
    // Recalculate total
    updateOrderSummary();
}

function updateOrderSummaryDisplay(subtotal, shipping, tax, total) {
    const subtotalElement = document.querySelector('.summary-row:nth-child(1) span:last-child');
    const taxElement = document.querySelector('.summary-row:nth-child(3) span:last-child');
    const totalElement = document.querySelector('.summary-row.total span:last-child');
    
    if (subtotalElement) subtotalElement.textContent = `$${subtotal.toFixed(2)}`;
    if (taxElement) taxElement.textContent = `$${tax.toFixed(2)}`;
    if (totalElement) totalElement.textContent = `$${total.toFixed(2)}`;
}

function initializePlaceOrder() {
    const placeOrderButton = document.querySelector('.btn-place-order');
    const checkoutForm = document.getElementById('checkout-form');
    
    if (placeOrderButton && checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            placeOrder(this);
        });
    }
}

function placeOrder(form) {
    // Validate all fields
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!validateCheckoutField(input)) {
            isValid = false;
        }
    });
    
    // Special validation for card payment
    const paymentMethod = form.querySelector('input[name="payment"]:checked').value;
    if (paymentMethod === 'card') {
        const cardNumber = form.querySelector('#card-number').value;
        const cardExpiry = form.querySelector('#card-expiry').value;
        const cardCVV = form.querySelector('#card-cvv').value;
        
        if (!isValidCardNumber(cardNumber) || !isValidCardExpiry(cardExpiry) || !isValidCVV(cardCVV)) {
            isValid = false;
            window.StepStyle.showNotification('Please check your card details', 'error');
        }
    }
    
    if (!isValid) {
        window.StepStyle.showNotification('❌ Please fix the errors in the form', 'error');
        
        // Scroll to first error
        const firstError = form.querySelector('.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        return;
    }
    
    // Show loading state
    const placeOrderButton = form.querySelector('.btn-place-order');
    const originalText = placeOrderButton.innerHTML;
    placeOrderButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    placeOrderButton.disabled = true;
    
    // Simulate order processing
    setTimeout(() => {
        // In a real application, you would submit the form data to your server
        // For demo purposes, we'll simulate successful order placement
        
        window.StepStyle.showNotification('🎉 Order placed successfully!', 'success');
        
        // Redirect to order confirmation page
        setTimeout(() => {
            window.location.href = 'order-confirm.php';
        }, 2000);
    }, 3000);
}

function initializeCheckoutPromo() {
    const promoInput = document.querySelector('.promo-section input');
    const applyButton = document.querySelector('.promo-section .btn');
    
    if (applyButton) {
        applyButton.addEventListener('click', applyCheckoutPromo);
    }
    
    if (promoInput) {
        promoInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyCheckoutPromo();
            }
        });
    }
}

function applyCheckoutPromo() {
    const promoInput = document.querySelector('.promo-section input');
    const promoCode = promoInput.value.trim();
    
    if (!promoCode) {
        window.StepStyle.showNotification('Please enter a promo code', 'warning');
        return;
    }
    
    // Show loading state
    const applyButton = document.querySelector('.promo-section .btn');
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
            applyCheckoutDiscount(discount, promoCode);
        } else {
            window.StepStyle.showNotification('Invalid promo code', 'error');
        }
    }, 1500);
}

function applyCheckoutDiscount(discount, promoCode) {
    let message = 'Promo code applied! ';
    
    if (discount === 'free-shipping') {
        message += 'Free shipping activated!';
        updateOrderSummaryShipping(0);
    } else {
        message += `${(discount * 100)}% discount applied!`;
        // This would apply the discount to the order total
    }
    
    window.StepStyle.showNotification(`🎉 ${message}`, 'success');
    
    // Disable promo input
    const promoInput = document.querySelector('.promo-section input');
    const applyButton = document.querySelector('.promo-section .btn');
    
    promoInput.disabled = true;
    applyButton.disabled = true;
    applyButton.textContent = 'Applied';
}

// Validation helper functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function isValidCardNumber(number) {
    const cleaned = number.replace(/\s+/g, '');
    return /^\d{13,19}$/.test(cleaned);
}

function isValidCardExpiry(expiry) {
    return /^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry);
}

function isValidCVV(cvv) {
    return /^\d{3,4}$/.test(cvv);
}

function isValidZIP(zip) {
    return /^\d{5}(-\d{4})?$/.test(zip);
}

// Add CSS for checkout page
const checkoutStyles = document.createElement('style');
checkoutStyles.textContent = `
    .form-group input.error,
    .form-group select.error {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }
    
    .form-group input.success,
    .form-group select.success {
        border-color: #27ae60;
        box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
    }
    
    .field-error {
        color: #e74c3c;
        font-size: 0.85rem;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .field-error i {
        font-size: 0.8rem;
    }
    
    .card-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
        font-size: 1.2rem;
    }
    
    .cvv-help {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
        cursor: help;
        font-size: 0.9rem;
    }
    
    .shipping-option,
    .payment-option {
        transition: all 0.3s ease;
    }
    
    .shipping-option:hover,
    .payment-option:hover {
        transform: translateY(-2px);
    }
    
    .security-assurance {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #27ae60;
    }
    
    .security-assurance i {
        font-size: 1.2rem;
    }
    
    .checkout-steps .step {
        transition: all 0.3s ease;
    }
    
    .checkout-steps .step.active {
        transform: scale(1.1);
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .btn-place-order:disabled {
        animation: pulse 1s infinite;
    }
`;
document.head.appendChild(checkoutStyles);