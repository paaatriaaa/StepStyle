// Admin JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const adminContainer = document.querySelector('.admin-container');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            adminContainer.classList.toggle('sidebar-open');
        });
    }
    
    // User dropdown
    const adminUser = document.querySelector('.admin-user');
    if (adminUser) {
        adminUser.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        const adminUser = document.querySelector('.admin-user');
        if (adminUser) {
            adminUser.classList.remove('active');
        }
    });
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('a[onclick*="confirm"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
});

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Form validation
function validateForm(form) {
    const required = form.querySelectorAll('[required]');
    let valid = true;
    
    required.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#f72585';
            valid = false;
        } else {
            field.style.borderColor = '#dee2e6';
        }
    });
    
    return valid;
}