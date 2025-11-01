// Authentication JavaScript for StepStyle

document.addEventListener('DOMContentLoaded', function() {
    initializeAuthPage();
});

function initializeAuthPage() {
    // Password visibility toggle
    initializePasswordToggle();
    
    // Form validation
    initializeFormValidation();
    
    // Social login buttons
    initializeSocialLogin();
    
    // Password strength meter
    initializePasswordStrength();
    
    // Auto-focus first input
    autoFocusFirstInput();
    
    // Form submission handling
    initializeFormSubmission();
}

function initializePasswordToggle() {
    const toggleButtons = document.querySelectorAll('.password-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
}

function initializeFormValidation() {
    const forms = document.querySelectorAll('.auth-form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            // Real-time validation
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
        
        // Email validation
        const emailInput = form.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                validateEmail(this);
            });
        }
        
        // Password confirmation
        const passwordInput = form.querySelector('input[name="password"]');
        const confirmInput = form.querySelector('input[name="confirm_password"]');
        
        if (passwordInput && confirmInput) {
            confirmInput.addEventListener('blur', function() {
                validatePasswordMatch(passwordInput, confirmInput);
            });
        }
    });
}

function validateField(field) {
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
    }
    
    if (isValid) {
        showFieldSuccess(field);
    } else {
        showFieldError(field, message);
    }
    
    return isValid;
}

function validateEmail(field) {
    const value = field.value.trim();
    
    if (value && !isValidEmail(value)) {
        showFieldError(field, 'Please enter a valid email address');
        return false;
    }
    
    return true;
}

function validatePasswordMatch(passwordField, confirmField) {
    if (passwordField.value && confirmField.value && passwordField.value !== confirmField.value) {
        showFieldError(confirmField, 'Passwords do not match');
        return false;
    }
    
    return true;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function isValidPhone(phone) {
    const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
    return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    field.classList.remove('success');
    
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    field.parentElement.appendChild(errorElement);
}

function showFieldSuccess(field) {
    clearFieldError(field);
    
    field.classList.remove('error');
    field.classList.add('success');
}

function clearFieldError(field) {
    field.classList.remove('error', 'success');
    
    const existingError = field.parentElement.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function initializePasswordStrength() {
    const passwordInput = document.querySelector('input[name="password"]');
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    if (passwordInput && strengthFill) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            
            strengthFill.setAttribute('data-strength', strength.score);
            strengthFill.style.width = `${strength.score * 25}%`;
            strengthFill.style.background = strength.color;
            
            if (strengthText) {
                strengthText.textContent = strength.text;
            }
        });
    }
}

function calculatePasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
    if (password.match(/\d/)) score++;
    if (password.match(/[^a-zA-Z\d]/)) score++;
    
    const strengthMap = {
        0: { text: 'Very Weak', color: '#e74c3c' },
        1: { text: 'Weak', color: '#e74c3c' },
        2: { text: 'Fair', color: '#f39c12' },
        3: { text: 'Good', color: '#3498db' },
        4: { text: 'Strong', color: '#27ae60' }
    };
    
    return {
        score: score,
        text: strengthMap[score].text,
        color: strengthMap[score].color
    };
}

function initializeSocialLogin() {
    const socialButtons = document.querySelectorAll('.btn-social');
    
    socialButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const provider = this.classList.contains('btn-google') ? 'Google' : 
                           this.classList.contains('btn-facebook') ? 'Facebook' : 'Social';
            
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connecting...';
            this.disabled = true;
            
            // Simulate social login
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                
                window.StepStyle.showNotification(`🔐 ${provider} login coming soon!`, 'info');
            }, 2000);
        });
    });
}

function autoFocusFirstInput() {
    const firstInput = document.querySelector('.auth-form input');
    if (firstInput) {
        setTimeout(() => firstInput.focus(), 500);
    }
}

function initializeFormSubmission() {
    const forms = document.querySelectorAll('.auth-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate all fields
            const inputs = this.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            // Special validations
            const emailInput = this.querySelector('input[type="email"]');
            if (emailInput && !validateEmail(emailInput)) {
                isValid = false;
            }
            
            const passwordInput = this.querySelector('input[name="password"]');
            const confirmInput = this.querySelector('input[name="confirm_password"]');
            if (passwordInput && confirmInput && !validatePasswordMatch(passwordInput, confirmInput)) {
                isValid = false;
            }
            
            if (!isValid) {
                window.StepStyle.showNotification('❌ Please fix the errors in the form', 'error');
                return;
            }
            
            // Show loading state
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            submitButton.disabled = true;
            
            // Simulate form submission
            setTimeout(() => {
                // In a real application, you would submit the form here
                // For demo purposes, we'll just show a success message
                
                if (form.closest('.auth-card').querySelector('h1').textContent.includes('Create')) {
                    window.StepStyle.showNotification('🎉 Account created successfully! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    // Login form - submit normally
                    form.submit();
                }
            }, 2000);
        });
    });
}

// Add CSS for form validation
const authStyles = document.createElement('style');
authStyles.textContent = `
    .input-with-icon input.error {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }
    
    .input-with-icon input.success {
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
    
    .btn-social:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .shake {
        animation: shake 0.5s ease-in-out;
    }
`;
document.head.appendChild(authStyles);