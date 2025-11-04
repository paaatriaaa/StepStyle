// Wishlist Page Functionality
class WishlistManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupFilters();
    }

    setupEventListeners() {
        // Add to cart buttons
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', this.handleAddToCart.bind(this));
        });

        // Remove from wishlist buttons
        document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
            btn.addEventListener('click', this.handleRemoveFromWishlist.bind(this));
        });

        // Share buttons
        document.querySelectorAll('.share-item').forEach(btn => {
            btn.addEventListener('click', this.handleShareItem.bind(this));
        });

        // Notify me buttons
        document.querySelectorAll('.notify-me').forEach(btn => {
            btn.addEventListener('click', this.handleNotifyMe.bind(this));
        });

        // Clear wishlist button
        const clearBtn = document.getElementById('clear-wishlist');
        if (clearBtn) {
            clearBtn.addEventListener('click', this.handleClearWishlist.bind(this));
        }

        // Share wishlist button
        const shareWishlistBtn = document.getElementById('share-wishlist');
        if (shareWishlistBtn) {
            shareWishlistBtn.addEventListener('click', this.handleShareWishlist.bind(this));
        }
    }

    setupFilters() {
        const sortSelect = document.getElementById('sort-wishlist');
        const brandSelect = document.getElementById('filter-brand');

        if (sortSelect) {
            sortSelect.addEventListener('change', this.handleSort.bind(this));
        }

        if (brandSelect) {
            brandSelect.addEventListener('change', this.handleFilter.bind(this));
        }
    }

    handleAddToCart(event) {
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        
        // Simulate adding to cart
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
        
        setTimeout(() => {
            window.StepStyle.showNotification('Item added to cart!', 'success');
            button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
            button.disabled = false;
        }, 1000);
    }

    handleRemoveFromWishlist(event) {
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        const item = document.querySelector(`.wishlist-item[data-product-id="${productId}"]`);
        
        // Add removal animation
        item.style.opacity = '0';
        item.style.transform = 'translateX(-100%)';
        
        setTimeout(() => {
            item.remove();
            this.updateWishlistStats();
            this.checkEmptyWishlist();
        }, 300);
    }

    handleShareItem(event) {
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        
        // Simulate sharing
        window.StepStyle.showNotification('Product link copied to clipboard!', 'info');
    }

    handleNotifyMe(event) {
        const button = event.currentTarget;
        const productId = button.dataset.productId;
        
        button.innerHTML = '<i class="fas fa-bell"></i> Notifications On';
        button.classList.remove('btn-secondary');
        button.classList.add('btn-primary');
        
        window.StepStyle.showNotification('We\'ll notify you when this item is back in stock!', 'success');
    }

    handleClearWishlist() {
        if (confirm('Are you sure you want to clear your entire wishlist?')) {
            const wishlistItems = document.querySelectorAll('.wishlist-item');
            
            wishlistItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-100%)';
            });
            
            setTimeout(() => {
                document.querySelector('.wishlist-items').innerHTML = '';
                this.checkEmptyWishlist();
            }, 500);
            
            window.StepStyle.showNotification('Wishlist cleared!', 'info');
        }
    }

    handleShareWishlist() {
        // Simulate sharing wishlist
        window.StepStyle.showNotification('Wishlist link copied to clipboard!', 'info');
    }

    handleSort(event) {
        const sortBy = event.target.value;
        console.log('Sorting by:', sortBy);
        // Implement sorting logic here
    }

    handleFilter(event) {
        const filterBy = event.target.value;
        console.log('Filtering by:', filterBy);
        // Implement filtering logic here
    }

    updateWishlistStats() {
        const itemCount = document.querySelectorAll('.wishlist-item').length;
        const statNumber = document.querySelector('.stat-number');
        
        if (statNumber) {
            statNumber.textContent = itemCount;
        }
    }

    checkEmptyWishlist() {
        const wishlistItems = document.querySelectorAll('.wishlist-item');
        if (wishlistItems.length === 0) {
            // Show empty wishlist state
            const wishlistContent = document.querySelector('.wishlist-content');
            const emptyWishlistHTML = `
                <div class="empty-wishlist">
                    <div class="empty-wishlist-icon">
                        <i class="far fa-heart"></i>
                    </div>
                    <h2>Your wishlist is empty</h2>
                    <p>Start saving your favorite items to keep track of them.</p>
                    <div class="empty-actions">
                        <a href="../products/categories.php" class="btn btn-primary">
                            <i class="fas fa-shopping-bag"></i>
                            Explore Products
                        </a>
                        <a href="../products/categories.php?filter=featured" class="btn btn-outline">
                            <i class="fas fa-star"></i>
                            View Featured
                        </a>
                    </div>
                </div>
            `;
            wishlistContent.innerHTML = emptyWishlistHTML;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new WishlistManager();
});