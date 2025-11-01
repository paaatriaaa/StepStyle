// Products JavaScript for StepStyle

document.addEventListener('DOMContentLoaded', function() {
    initializeProductsPage();
});

function initializeProductsPage() {
    // View toggle functionality
    initializeViewToggle();
    
    // Filter functionality
    initializeFilters();
    
    // Sort functionality
    initializeSorting();
    
    // Product interactions
    initializeProductInteractions();
    
    // Load more functionality
    initializeLoadMore();
    
    // Quick view modal
    initializeQuickView();
}

function initializeViewToggle() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const productsGrid = document.getElementById('products-view');
    
    if (!viewButtons.length || !productsGrid) return;
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            
            // Update active button
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Update grid view
            productsGrid.className = 'products-grid';
            if (viewType === 'list') {
                productsGrid.classList.add('list-view');
            }
            
            // Save preference to localStorage
            localStorage.setItem('productsView', viewType);
        });
    });
    
    // Load saved view preference
    const savedView = localStorage.getItem('productsView');
    if (savedView) {
        const savedButton = document.querySelector(`.view-btn[data-view="${savedView}"]`);
        if (savedButton) {
            savedButton.click();
        }
    }
}

function initializeFilters() {
    const filterCheckboxes = document.querySelectorAll('.filter-checkbox input, .size-option input');
    const priceRange = document.querySelector('.range-slider');
    const applyButton = document.querySelector('.btn-filter');
    const clearButton = document.querySelector('.btn-clear');
    
    // Price range filter
    if (priceRange) {
        priceRange.addEventListener('input', function() {
            updatePriceRangeDisplay(this.value);
        });
        
        // Initialize price display
        updatePriceRangeDisplay(priceRange.value);
    }
    
    // Apply filters
    if (applyButton) {
        applyButton.addEventListener('click', applyFilters);
    }
    
    // Clear filters
    if (clearButton) {
        clearButton.addEventListener('click', clearFilters);
    }
    
    // Real-time filtering on checkbox change
    filterCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.type === 'checkbox') {
                applyFilters();
            }
        });
    });
}

function updatePriceRangeDisplay(value) {
    const priceValues = document.querySelector('.price-values');
    if (priceValues) {
        const maxPrice = priceValues.querySelector('span:last-child');
        if (maxPrice) {
            maxPrice.textContent = `$${value}`;
        }
    }
}

function applyFilters() {
    const products = document.querySelectorAll('.product-card');
    const selectedCategories = getSelectedCheckboxes('category');
    const selectedSizes = getSelectedCheckboxes('size');
    const priceRange = document.querySelector('.range-slider');
    const maxPrice = priceRange ? parseInt(priceRange.value) : 500;
    
    let visibleCount = 0;
    
    products.forEach(product => {
        const productCategory = product.getAttribute('data-category');
        const productPrice = parseInt(product.getAttribute('data-price')) / 100; // Convert to dollars
        let shouldShow = true;
        
        // Category filter
        if (selectedCategories.length > 0 && !selectedCategories.includes(productCategory)) {
            shouldShow = false;
        }
        
        // Price filter
        if (productPrice > maxPrice) {
            shouldShow = false;
        }
        
        // Size filter (this would need size data attributes on products)
        if (selectedSizes.length > 0) {
            // Implement size filtering logic here
        }
        
        if (shouldShow) {
            product.style.display = 'block';
            visibleCount++;
            
            // Add animation
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, 100);
        } else {
            product.style.display = 'none';
        }
    });
    
    // Show no results message if needed
    showNoResultsMessage(visibleCount === 0);
    
    // Update results count
    updateResultsCount(visibleCount);
}

function clearFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('.filter-checkbox input, .size-option input').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset price range
    const priceRange = document.querySelector('.range-slider');
    if (priceRange) {
        priceRange.value = 500;
        updatePriceRangeDisplay(500);
    }
    
    // Show all products
    document.querySelectorAll('.product-card').forEach(product => {
        product.style.display = 'block';
    });
    
    // Hide no results message
    showNoResultsMessage(false);
    
    // Reset results count
    updateResultsCount(document.querySelectorAll('.product-card').length);
}

function getSelectedCheckboxes(name) {
    const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
    return Array.from(checkboxes).map(checkbox => checkbox.value);
}

function showNoResultsMessage(show) {
    let noResults = document.querySelector('.no-results-message');
    
    if (show && !noResults) {
        noResults = document.createElement('div');
        noResults.className = 'no-results-message';
        noResults.innerHTML = `
            <i class="fas fa-search"></i>
            <h3>No products found</h3>
            <p>Try adjusting your filters to see more results</p>
            <button class="btn btn-primary btn-clear-filters">Clear All Filters</button>
        `;
        
        const productsGrid = document.querySelector('.products-grid');
        productsGrid.parentNode.insertBefore(noResults, productsGrid);
        
        // Add click handler for clear filters button
        noResults.querySelector('.btn-clear-filters').addEventListener('click', clearFilters);
    } else if (!show && noResults) {
        noResults.remove();
    }
}

function updateResultsCount(count) {
    let resultsCount = document.querySelector('.results-count');
    
    if (!resultsCount) {
        resultsCount = document.createElement('div');
        resultsCount.className = 'results-count';
        document.querySelector('.products-header').appendChild(resultsCount);
    }
    
    resultsCount.textContent = `${count} products found`;
}

function initializeSorting() {
    const sortSelect = document.querySelector('.sort-select');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortProducts(this.value);
        });
    }
}

function sortProducts(sortBy) {
    const productsGrid = document.querySelector('.products-grid');
    const products = Array.from(productsGrid.querySelectorAll('.product-card'));
    
    products.sort((a, b) => {
        switch (sortBy) {
            case 'price-low':
                return (parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price')));
            case 'price-high':
                return (parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price')));
            case 'name':
                return a.querySelector('.product-name').textContent.localeCompare(b.querySelector('.product-name').textContent);
            case 'rating':
                // This would require rating data attributes
                return 0;
            case 'newest':
            default:
                return 0; // Already in order by default
        }
    });
    
    // Reappend sorted products
    products.forEach(product => {
        productsGrid.appendChild(product);
    });
    
    // Save sort preference
    localStorage.setItem('productsSort', sortBy);
}

function initializeProductInteractions() {
    // Color selection
    initializeColorSelection();
    
    // Size selection
    initializeSizeSelection();
    
    // Quick view
    initializeQuickViewButtons();
}

function initializeColorSelection() {
    // This would handle color selection on product cards
    // Implementation depends on specific product page structure
}

function initializeSizeSelection() {
    // This would handle size selection on product cards
    // Implementation depends on specific product page structure
}

function initializeQuickViewButtons() {
    const quickViewButtons = document.querySelectorAll('.quick-view');
    
    quickViewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product');
            showQuickViewModal(productId);
        });
    });
}

function initializeQuickView() {
    // Quick view modal would be implemented here
    // This is a placeholder for the modal functionality
}

function showQuickViewModal(productId) {
    window.StepStyle.showNotification('👀 Quick view feature coming soon!', 'info');
    
    // In a real implementation, you would:
    // 1. Fetch product details via AJAX
    // 2. Show a modal with product information
    // 3. Allow adding to cart directly from the modal
}

function initializeLoadMore() {
    const loadMoreButton = document.querySelector('.btn-load-more');
    
    if (loadMoreButton) {
        loadMoreButton.addEventListener('click', function() {
            loadMoreProducts(this);
        });
    }
}

function loadMoreProducts(button) {
    // Show loading state
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    button.disabled = true;
    
    // Simulate loading more products
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        window.StepStyle.showNotification('📦 More products loaded!', 'success');
        
        // In a real implementation, you would:
        // 1. Fetch next page of products via AJAX
        // 2. Append them to the products grid
        // 3. Update the load more button state
    }, 1500);
}

// Add CSS for products page
const productsStyles = document.createElement('style');
productsStyles.textContent = `
    .results-count {
        font-size: 0.9rem;
        color: #7f8c8d;
        font-weight: 500;
    }
    
    .no-results-message {
        text-align: center;
        padding: 60px 20px;
        grid-column: 1 / -1;
    }
    
    .no-results-message i {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 20px;
    }
    
    .no-results-message h3 {
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .no-results-message p {
        color: #7f8c8d;
        margin-bottom: 25px;
    }
    
    .product-card {
        transition: all 0.3s ease;
    }
    
    .filter-group {
        transition: all 0.3s ease;
    }
    
    .size-option input:checked + .size-box {
        transform: scale(1.1);
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .product-card[style*="display: block"] {
        animation: fadeInUp 0.5s ease;
    }
`;
document.head.appendChild(productsStyles);