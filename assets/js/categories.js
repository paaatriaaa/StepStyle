// categories.js - Updated with enhanced animations and effects

document.addEventListener('DOMContentLoaded', function() {
    // Initialize categories page
    initCategoriesPage();
    
    // Filter functionality
    initFilters();
    
    // View toggle functionality
    initViewToggle();
    
    // Sort functionality
    initSorting();
    
    // Load more functionality
    initLoadMore();
});

function initCategoriesPage() {
    console.log('Initializing categories page...');
    
    // Add loading animation to product cards
    const productItems = document.querySelectorAll('.product-item');
    productItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.classList.add('fade-in-up');
    });
    
    // Initialize filter toggles
    initFilterToggles();
}

function initFilterToggles() {
    const filterToggles = document.querySelectorAll('.filter-toggle');
    
    filterToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const filterSection = this.closest('.filter-section');
            const filterContent = filterSection.querySelector('.filter-content');
            const icon = this.querySelector('i');
            
            // Toggle content visibility
            filterContent.style.display = filterContent.style.display === 'none' ? 'block' : 'none';
            
            // Rotate icon
            icon.style.transform = filterContent.style.display === 'none' ? 'rotate(0deg)' : 'rotate(180deg)';
            
            // Add smooth transition
            filterContent.style.transition = 'all 0.3s ease';
        });
    });
}

function initFilters() {
    const filterInputs = document.querySelectorAll('input[name="category"], input[name="brand"], input[name="price"], input[name="color"]');
    const clearFiltersBtn = document.getElementById('clear-filters');
    const sizeOptions = document.querySelectorAll('.size-option');
    
    // Filter inputs change handler
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            applyFilters();
            updateActiveFilters();
        });
    });
    
    // Size options click handler
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            this.classList.toggle('active');
            applyFilters();
            updateActiveFilters();
        });
    });
    
    // Clear filters handler
    clearFiltersBtn.addEventListener('click', function() {
        clearAllFilters();
    });
    
    // Remove individual filter handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-filter')) {
            const filterType = e.target.closest('.remove-filter').dataset.filter;
            removeFilter(filterType);
        }
    });
}

function applyFilters() {
    const products = document.querySelectorAll('.product-item');
    let visibleCount = 0;
    
    products.forEach(product => {
        let shouldShow = true;
        
        // Add filtering logic here based on selected filters
        // This is a simplified version - you'll need to implement actual filtering logic
        
        if (shouldShow) {
            product.style.display = 'block';
            visibleCount++;
            
            // Add animation
            product.style.animation = 'fadeInUp 0.5s ease';
        } else {
            product.style.display = 'none';
        }
    });
    
    // Update product count
    updateProductCount(visibleCount);
    
    // Show/hide no results message
    toggleNoResults(visibleCount === 0);
}

function updateProductCount(count) {
    const countElement = document.querySelector('.page-subtitle');
    if (countElement) {
        countElement.textContent = `${count} product${count !== 1 ? 's' : ''} found`;
    }
}

function toggleNoResults(show) {
    let noResults = document.querySelector('.no-results');
    
    if (show && !noResults) {
        noResults = document.createElement('div');
        noResults.className = 'no-results';
        noResults.innerHTML = `
            <div class="no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>No products found</h3>
            <p>Try adjusting your filters or search terms</p>
            <button class="btn btn-primary" id="reset-search">
                <i class="fas fa-redo"></i>
                Reset Filters
            </button>
        `;
        
        const productsGrid = document.getElementById('products-grid');
        productsGrid.appendChild(noResults);
        
        // Add click handler for reset button
        document.getElementById('reset-search').addEventListener('click', clearAllFilters);
    } else if (!show && noResults) {
        noResults.remove();
    }
}

function updateActiveFilters() {
    const activeFiltersContainer = document.getElementById('active-filters');
    const selectedFilters = getSelectedFilters();
    
    if (Object.keys(selectedFilters).length === 0) {
        activeFiltersContainer.innerHTML = '';
        return;
    }
    
    let filtersHTML = '<div class="filters-list"><span class="filters-label">Active filters:</span>';
    
    Object.keys(selectedFilters).forEach(filterType => {
        selectedFilters[filterType].forEach(filterValue => {
            filtersHTML += `
                <span class="filter-tag">
                    ${filterType}: ${formatFilterValue(filterType, filterValue)}
                    <button class="remove-filter" data-filter="${filterType}">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
            `;
        });
    });
    
    filtersHTML += '</div>';
    activeFiltersContainer.innerHTML = filtersHTML;
}

function getSelectedFilters() {
    const filters = {};
    
    // Get category filters
    const categoryFilters = Array.from(document.querySelectorAll('input[name="category"]:checked'))
        .map(input => input.value);
    if (categoryFilters.length > 0) filters.category = categoryFilters;
    
    // Get brand filters
    const brandFilters = Array.from(document.querySelectorAll('input[name="brand"]:checked'))
        .map(input => input.value);
    if (brandFilters.length > 0) filters.brand = brandFilters;
    
    // Get price filter
    const priceFilter = document.querySelector('input[name="price"]:checked');
    if (priceFilter && priceFilter.value) filters.price = [priceFilter.value];
    
    return filters;
}

function formatFilterValue(filterType, value) {
    const formatMap = {
        'under50': 'Under $50',
        '50-100': '$50 - $100',
        '100-200': '$100 - $200',
        'over200': 'Over $200'
    };
    
    return formatMap[value] || value.charAt(0).toUpperCase() + value.slice(1);
}

function removeFilter(filterType) {
    const inputs = document.querySelectorAll(`input[name="${filterType}"]`);
    inputs.forEach(input => input.checked = false);
    
    applyFilters();
    updateActiveFilters();
}

function clearAllFilters() {
    // Uncheck all filter inputs
    const filterInputs = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
    filterInputs.forEach(input => input.checked = false);
    
    // Reset size options
    const sizeOptions = document.querySelectorAll('.size-option');
    sizeOptions.forEach(option => option.classList.remove('active'));
    
    // Reset color options
    const colorOptions = document.querySelectorAll('input[name="color"]');
    colorOptions.forEach(input => input.checked = false);
    
    // Apply changes
    applyFilters();
    updateActiveFilters();
    
    // Add visual feedback
    const clearBtn = document.getElementById('clear-filters');
    clearBtn.style.transform = 'scale(0.95)';
    setTimeout(() => {
        clearBtn.style.transform = 'scale(1)';
    }, 150);
}

function initViewToggle() {
    const viewOptions = document.querySelectorAll('.view-option');
    const productsGrid = document.getElementById('products-grid');
    
    viewOptions.forEach(option => {
        option.addEventListener('click', function() {
            const viewType = this.dataset.view;
            
            // Update active state
            viewOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Apply view type
            productsGrid.className = 'products-grid';
            if (viewType === 'list') {
                productsGrid.classList.add('list-view');
            }
            
            // Add transition
            productsGrid.style.opacity = '0.7';
            setTimeout(() => {
                productsGrid.style.opacity = '1';
                productsGrid.style.transition = 'opacity 0.3s ease';
            }, 150);
        });
    });
}

function initSorting() {
    const sortSelect = document.getElementById('sort-by');
    
    sortSelect.addEventListener('change', function() {
        // Add loading state
        const productsGrid = document.getElementById('products-grid');
        productsGrid.style.opacity = '0.7';
        
        // Simulate sorting delay
        setTimeout(() => {
            // Implement actual sorting logic here
            console.log('Sorting by:', this.value);
            
            // Remove loading state
            productsGrid.style.opacity = '1';
            productsGrid.style.transition = 'opacity 0.3s ease';
        }, 500);
    });
}

function initLoadMore() {
    const loadMoreBtn = document.getElementById('load-more');
    
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            // Add loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            this.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Implement actual load more logic here
                console.log('Loading more products...');
                
                // Reset button state
                this.innerHTML = originalText;
                this.disabled = false;
                
                // Show success feedback
                this.style.background = 'var(--success)';
                setTimeout(() => {
                    this.style.background = '';
                }, 1000);
            }, 1500);
        });
    }
}

// Utility function for smooth animations
function animateElement(element, animation, duration = 300) {
    element.style.animation = `${animation} ${duration}ms ease`;
    setTimeout(() => {
        element.style.animation = '';
    }, duration);
}