// Categories Page Functionality
class CategoriesManager {
    constructor() {
        this.filters = {
            category: [],
            brand: [],
            price: '',
            size: [],
            color: []
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFilterToggles();
        this.updateActiveFilters();
    }

    setupEventListeners() {
        // Filter changes
        document.querySelectorAll('input[name="category"]').forEach(input => {
            input.addEventListener('change', this.handleCategoryFilter.bind(this));
        });

        document.querySelectorAll('input[name="brand"]').forEach(input => {
            input.addEventListener('change', this.handleBrandFilter.bind(this));
        });

        document.querySelectorAll('input[name="price"]').forEach(input => {
            input.addEventListener('change', this.handlePriceFilter.bind(this));
        });

        // Size options
        document.querySelectorAll('.size-option').forEach(btn => {
            btn.addEventListener('click', this.handleSizeFilter.bind(this));
        });

        // Color options
        document.querySelectorAll('input[name="color"]').forEach(input => {
            input.addEventListener('change', this.handleColorFilter.bind(this));
        });

        // Clear filters
        const clearBtn = document.getElementById('clear-filters');
        if (clearBtn) {
            clearBtn.addEventListener('click', this.clearAllFilters.bind(this));
        }

        // Remove filter tags
        document.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', this.handleRemoveFilter.bind(this));
        });

        // View options
        document.querySelectorAll('.view-option').forEach(btn => {
            btn.addEventListener('click', this.handleViewChange.bind(this));
        });

        // Sort options
        const sortSelect = document.getElementById('sort-by');
        if (sortSelect) {
            sortSelect.addEventListener('change', this.handleSortChange.bind(this));
        }

        // Load more
        const loadMoreBtn = document.getElementById('load-more');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', this.handleLoadMore.bind(this));
        }

        // Reset search
        const resetSearchBtn = document.getElementById('reset-search');
        if (resetSearchBtn) {
            resetSearchBtn.addEventListener('click', this.resetSearch.bind(this));
        }
    }

    setupFilterToggles() {
        document.querySelectorAll('.filter-toggle').forEach(btn => {
            btn.addEventListener('click', function() {
                const content = this.closest('.filter-section').querySelector('.filter-content');
                this.classList.toggle('active');
                
                if (this.classList.contains('active')) {
                    content.style.display = 'block';
                } else {
                    content.style.display = 'none';
                }
            });
        });
    }

    handleCategoryFilter(event) {
        const checkbox = event.target;
        const value = checkbox.value;
        
        if (checkbox.checked) {
            this.filters.category.push(value);
        } else {
            this.filters.category = this.filters.category.filter(cat => cat !== value);
        }
        
        this.applyFilters();
    }

    handleBrandFilter(event) {
        const checkbox = event.target;
        const value = checkbox.value;
        
        if (checkbox.checked) {
            this.filters.brand.push(value);
        } else {
            this.filters.brand = this.filters.brand.filter(brand => brand !== value);
        }
        
        this.applyFilters();
    }

    handlePriceFilter(event) {
        this.filters.price = event.target.value;
        this.applyFilters();
    }

    handleSizeFilter(event) {
        const button = event.currentTarget;
        const size = button.dataset.size;
        
        button.classList.toggle('active');
        
        if (button.classList.contains('active')) {
            this.filters.size.push(size);
        } else {
            this.filters.size = this.filters.size.filter(s => s !== size);
        }
        
        this.applyFilters();
    }

    handleColorFilter(event) {
        const checkbox = event.target;
        const value = checkbox.value;
        
        if (checkbox.checked) {
            this.filters.color.push(value);
        } else {
            this.filters.color = this.filters.color.filter(color => color !== value);
        }
        
        this.applyFilters();
    }

    applyFilters() {
        // In a real application, this would make an API call
        // For now, we'll just update the URL and show a loading state
        this.updateURL();
        this.updateActiveFilters();
        this.showLoadingState();
        
        // Simulate API call delay
        setTimeout(() => {
            this.hideLoadingState();
            window.StepStyle.showNotification('Filters applied', 'info');
        }, 500);
    }

    updateURL() {
        const params = new URLSearchParams();
        
        if (this.filters.category.length > 0) {
            params.set('cat', this.filters.category[0]); // Single category for demo
        }
        
        if (this.filters.brand.length > 0) {
            params.set('brand', this.filters.brand[0]); // Single brand for demo
        }
        
        if (this.filters.price) {
            params.set('price', this.filters.price);
        }
        
        // Update URL without page reload (for demo)
        const newURL = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
        window.history.replaceState({}, '', newURL);
    }

    updateActiveFilters() {
        const activeFiltersContainer = document.getElementById('active-filters');
        let activeFiltersHTML = '';
        
        if (this.filters.category.length > 0 || this.filters.brand.length > 0 || this.filters.price) {
            activeFiltersHTML = '<div class="filters-list"><span class="filters-label">Active filters:</span>';
            
            this.filters.category.forEach(cat => {
                activeFiltersHTML += `
                    <span class="filter-tag">
                        Category: ${this.capitalizeFirst(cat)}
                        <button class="remove-filter" data-filter="category" data-value="${cat}">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `;
            });
            
            this.filters.brand.forEach(brand => {
                activeFiltersHTML += `
                    <span class="filter-tag">
                        Brand: ${this.capitalizeFirst(brand)}
                        <button class="remove-filter" data-filter="brand" data-value="${brand}">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `;
            });
            
            if (this.filters.price) {
                let priceText = this.filters.price;
                if (priceText === 'under50') priceText = 'Under $50';
                else if (priceText === '50-100') priceText = '$50 - $100';
                else if (priceText === '100-200') priceText = '$100 - $200';
                else if (priceText === 'over200') priceText = 'Over $200';
                
                activeFiltersHTML += `
                    <span class="filter-tag">
                        Price: ${priceText}
                        <button class="remove-filter" data-filter="price" data-value="${this.filters.price}">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                `;
            }
            
            activeFiltersHTML += '</div>';
        }
        
        activeFiltersContainer.innerHTML = activeFiltersHTML;
        
        // Re-attach event listeners to new remove buttons
        document.querySelectorAll('.remove-filter').forEach(btn => {
            btn.addEventListener('click', this.handleRemoveFilter.bind(this));
        });
    }

    handleRemoveFilter(event) {
        const button = event.currentTarget;
        const filterType = button.dataset.filter;
        const filterValue = button.dataset.value;
        
        switch (filterType) {
            case 'category':
                this.filters.category = this.filters.category.filter(cat => cat !== filterValue);
                document.querySelector(`input[name="category"][value="${filterValue}"]`).checked = false;
                break;
            case 'brand':
                this.filters.brand = this.filters.brand.filter(brand => brand !== filterValue);
                document.querySelector(`input[name="brand"][value="${filterValue}"]`).checked = false;
                break;
            case 'price':
                this.filters.price = '';
                document.querySelector('input[name="price"]:checked').checked = false;
                document.querySelector('#price-all').checked = true;
                break;
        }
        
        this.applyFilters();
    }

    clearAllFilters() {
        // Reset all checkboxes and radio buttons
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            if (radio.id === 'price-all') {
                radio.checked = true;
            } else {
                radio.checked = false;
            }
        });
        
        document.querySelectorAll('.size-option').forEach(btn => {
            btn.classList.remove('active');
        });
        
        // Reset filters object
        this.filters = {
            category: [],
            brand: [],
            price: '',
            size: [],
            color: []
        };
        
        this.applyFilters();
    }

    handleViewChange(event) {
        const button = event.currentTarget;
        const view = button.dataset.view;
        const productsGrid = document.getElementById('products-grid');
        
        // Update active button
        document.querySelectorAll('.view-option').forEach(btn => {
            btn.classList.remove('active');
        });
        button.classList.add('active');
        
        // Update grid view
        productsGrid.classList.remove('grid-view', 'list-view');
        productsGrid.classList.add(view + '-view');
    }

    handleSortChange(event) {
        const sortBy = event.target.value;
        
        // In real app, this would reload products with new sorting
        window.StepStyle.showNotification(`Sorted by: ${event.target.options[event.target.selectedIndex].text}`, 'info');
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('sort', sortBy);
        window.history.replaceState({}, '', url);
    }

    handleLoadMore() {
        const button = document.getElementById('load-more');
        const originalText = button.innerHTML;
        
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        button.disabled = true;
        
        // Simulate loading more products
        setTimeout(() => {
            window.StepStyle.showNotification('More products loaded!', 'success');
            button.innerHTML = originalText;
            button.disabled = false;
            
            // Hide load more button after "loading" all products
            button.style.display = 'none';
        }, 1500);
    }

    resetSearch() {
        window.location.href = 'categories.php';
    }

    showLoadingState() {
        const productsGrid = document.getElementById('products-grid');
        productsGrid.style.opacity = '0.5';
        productsGrid.style.pointerEvents = 'none';
    }

    hideLoadingState() {
        const productsGrid = document.getElementById('products-grid');
        productsGrid.style.opacity = '1';
        productsGrid.style.pointerEvents = 'auto';
    }

    capitalizeFirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new CategoriesManager();
});