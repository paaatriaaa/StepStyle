// Auth Page Functionality
class AuthManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupPasswordToggle();
        this.setupPasswordStrength();
        this.setupFormValidation();
    }

    setupEventListeners() {
        // Real-time form validation
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', this.validateField.bind(this));
            input.addEventListener('input', this.validateField.bind(this));
        });

        // Confirm password validation
        const confirmPassword = document.getElementById('confirm_password');
        if (confirmPassword) {
            confirmPassword.addEventListener('input', this.validatePasswordMatch.bind(this));
        }

        // Terms agreement validation
        const termsCheckbox = document.getElementById('terms');
        if (termsCheckbox) {
            termsCheckbox.addEventListener('change', this.validateForm.bind(this));
        }
    }

    setupPasswordToggle() {
        const toggleButtons = document.querySelectorAll('.password-toggle');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.setAttribute('aria-label', 'Hide password');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.setAttribute('aria-label', 'Show password');
                }
            });
        });
    }

    setupPasswordStrength() {
        const passwordInput = document.getElementById('password');
        if (!passwordInput) return;

        const strengthFill = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = this.calculatePasswordStrength(password);
            
            strengthFill.setAttribute('data-strength', strength.level);
            strengthText.textContent = strength.text;
            strengthText.className = 'strength-text ' + strength.className;
        });

        // Add method to input element
        passwordInput.calculatePasswordStrength = function(password) {
            let strength = 0;
            let text = 'Password strength';
            let className = '';

            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
            if (password.match(/\d/)) strength++;
            if (password.match(/[^a-zA-Z\d]/)) strength++;

            switch (strength) {
                case 0:
                    text = 'Password strength';
                    className = '';
                    break;
                case 1:
                    text = 'Weak';
                    className = 'weak';
                    break;
                case 2:
                    text = 'Fair';
                    className = 'fair';
                    break;
                case 3:
                    text = 'Good';
                    className = 'good';
                    break;
                case 4:
                    text = 'Strong';
                    className = 'strong';
                    break;
            }

            return { level: strength, text: text, className: className };
        };
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('.auth-form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(event) {
                if (!this.validateForm()) {
                    event.preventDefault();
                    this.showFormErrors();
                }
            });
        });
    }

    validateField(event) {
        const field = event.target;
        const formGroup = field.closest('.form-group');
        
        // Remove previous validation states
        formGroup.classList.remove('valid', 'invalid');
        
        if (field.value.trim() === '') {
            if (field.hasAttribute('required') && field === document.activeElement) {
                // Don't show error while user is typing
                return;
            }
            formGroup.classList.add('invalid');
            return false;
        }
        
        // Email validation
        if (field.type === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                formGroup.classList.add('invalid');
                return false;
            }
        }
        
        // Phone validation (basic)
        if (field.type === 'tel' && field.value.trim() !== '') {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            const cleanPhone = field.value.replace(/[^\d+]/g, '');
            if (!phoneRegex.test(cleanPhone)) {
                formGroup.classList.add('invalid');
                return false;
            }
        }
        
        formGroup.classList.add('valid');
        return true;
    }

    validatePasswordMatch() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const confirmGroup = confirmPassword.closest('.form-group');
        
        if (!password || !confirmPassword) return;
        
        confirmGroup.classList.remove('valid', 'invalid');
        
        if (confirmPassword.value.trim() === '') {
            return;
        }
        
        if (password.value !== confirmPassword.value) {
            confirmGroup.classList.add('invalid');
            return false;
        } else {
            confirmGroup.classList.add('valid');
            return true;
        }
    }

    validateForm() {
        let isValid = true;
        const form = document.querySelector('.auth-form');
        
        // Validate all required fields
        const requiredFields = form.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (!this.validateField({ target: field })) {
                isValid = false;
            }
        });
        
        // Validate password match for registration
        if (form.id === 'register-form') {
            if (!this.validatePasswordMatch()) {
                isValid = false;
            }
            
            // Validate terms agreement
            const termsCheckbox = document.getElementById('terms');
            const termsGroup = termsCheckbox.closest('.form-options');
            
            termsGroup.classList.remove('invalid');
            if (!termsCheckbox.checked) {
                termsGroup.classList.add('invalid');
                isValid = false;
            }
        }
        
        return isValid;
    }

    showFormErrors() {
        const firstInvalidField = document.querySelector('.invalid input');
        if (firstInvalidField) {
            firstInvalidField.focus();
        }
        
        window.StepStyle.showNotification('Please check the form for errors.', 'error');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new AuthManager();
});