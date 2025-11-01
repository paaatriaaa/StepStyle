<?php
/**
 * Product Card Component
 * 
 * @param array $product Product data
 * @param string $class Additional CSS classes
 * @param bool $show_actions Whether to show action buttons
 * @param bool $show_description Whether to show product description
 */

function renderProductCard($product, $class = '', $show_actions = true, $show_description = true) {
    $discount = 0;
    if ($product['compare_price'] && $product['compare_price'] > $product['price']) {
        $discount = calculateDiscount($product['compare_price'], $product['price']);
    }
    
    $rating = getProductRating($GLOBALS['db'], $product['id']);
    $average_rating = $rating['average_rating'] ?? 0;
    $review_count = $rating['review_count'] ?? 0;
    ?>
    
    <div class="product-card <?php echo $class; ?>" data-product-id="<?php echo $product['id']; ?>">
        <div class="product-image">
            <a href="/products/detail.php?id=<?php echo $product['id']; ?>" class="product-image-link">
                <img src="<?php echo $product['image_url'] ?? '/assets/images/products/placeholder.jpg'; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     loading="lazy"
                     onerror="this.src='/assets/images/products/placeholder.jpg'">
            </a>
            
            <?php if ($discount > 0): ?>
                <div class="discount-badge">-<?php echo $discount; ?>%</div>
            <?php endif; ?>
            
            <?php if ($product['is_featured']): ?>
                <div class="featured-badge" title="Featured Product">
                    <i class="fas fa-star"></i>
                </div>
            <?php endif; ?>
            
            <?php if ($product['quantity'] <= 0): ?>
                <div class="out-of-stock-badge">Out of Stock</div>
            <?php endif; ?>
            
            <?php if ($show_actions): ?>
                <div class="product-actions">
                    <button class="action-btn wishlist-btn" 
                            data-product-id="<?php echo $product['id']; ?>"
                            title="Add to Wishlist">
                        <i class="far fa-heart"></i>
                    </button>
                    
                    <button class="action-btn quick-view-btn" 
                            data-product-id="<?php echo $product['id']; ?>"
                            title="Quick View">
                        <i class="fas fa-eye"></i>
                    </button>
                    
                    <button class="action-btn compare-btn" 
                            data-product-id="<?php echo $product['id']; ?>"
                            title="Add to Compare">
                        <i class="fas fa-exchange-alt"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if ($product['variants'] && count($product['variants']) > 0): ?>
                <div class="color-options">
                    <?php 
                    $colors = array_slice($product['variants'], 0, 4);
                    foreach ($colors as $variant): 
                        if (!empty($variant['color'])): ?>
                            <div class="color-dot" 
                                 style="background-color: <?php echo htmlspecialchars($variant['color']); ?>"
                                 data-variant-id="<?php echo $variant['id']; ?>"
                                 title="<?php echo htmlspecialchars($variant['color']); ?>"></div>
                        <?php endif;
                    endforeach; 
                    
                    if (count($product['variants']) > 4): ?>
                        <div class="color-more" title="More colors">
                            +<?php echo count($product['variants']) - 4; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="product-info">
            <div class="product-meta">
                <?php if (!empty($product['brand_name'])): ?>
                    <a href="/products/brand.php?slug=<?php echo $product['brand_slug']; ?>" class="product-brand">
                        <?php echo htmlspecialchars($product['brand_name']); ?>
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($product['category_name'])): ?>
                    <span class="product-category">in <?php echo htmlspecialchars($product['category_name']); ?></span>
                <?php endif; ?>
            </div>
            
            <h3 class="product-name">
                <a href="/products/detail.php?id=<?php echo $product['id']; ?>">
                    <?php echo htmlspecialchars($product['name']); ?>
                </a>
            </h3>
            
            <?php if ($show_description && !empty($product['short_description'])): ?>
                <p class="product-description">
                    <?php echo htmlspecialchars($product['short_description']); ?>
                </p>
            <?php endif; ?>
            
            <div class="product-rating">
                <div class="stars" data-rating="<?php echo $average_rating; ?>">
                    <?php echo generateStarRating($average_rating); ?>
                </div>
                <?php if ($review_count > 0): ?>
                    <span class="rating-count">(<?php echo $review_count; ?>)</span>
                <?php endif; ?>
            </div>
            
            <div class="product-price">
                <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                <?php if ($product['compare_price'] && $product['compare_price'] > $product['price']): ?>
                    <span class="compare-price"><?php echo formatPrice($product['compare_price']); ?></span>
                <?php endif; ?>
            </div>
            
            <?php if ($product['variants'] && count($product['variants']) > 0): ?>
                <div class="size-options">
                    <?php 
                    $sizes = array_slice(array_unique(array_column($product['variants'], 'size')), 0, 5);
                    foreach ($sizes as $size): 
                        if (!empty($size)): ?>
                            <span class="size-option"><?php echo htmlspecialchars($size); ?></span>
                        <?php endif;
                    endforeach; 
                    
                    if (count($sizes) > 5): ?>
                        <span class="size-more">+<?php echo count($sizes) - 5; ?> more</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div class="product-actions-bottom">
                <?php if ($product['quantity'] > 0): ?>
                    <button class="btn btn-primary add-to-cart-btn" 
                            data-product-id="<?php echo $product['id']; ?>"
                            data-variant-id="<?php echo $product['variants'][0]['id'] ?? 0; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>
                <?php else: ?>
                    <button class="btn btn-secondary notify-me-btn" 
                            data-product-id="<?php echo $product['id']; ?>"
                            disabled>
                        <i class="fas fa-bell"></i>
                        Notify When Available
                    </button>
                <?php endif; ?>
            </div>
            
            <div class="product-features">
                <?php if ($product['free_shipping']): ?>
                    <div class="feature-tag">
                        <i class="fas fa-shipping-fast"></i>
                        Free Shipping
                    </div>
                <?php endif; ?>
                
                <?php if ($product['fast_delivery']): ?>
                    <div class="feature-tag">
                        <i class="fas fa-bolt"></i>
                        Fast Delivery
                    </div>
                <?php endif; ?>
                
                <?php if ($product['easy_returns']): ?>
                    <div class="feature-tag">
                        <i class="fas fa-undo"></i>
                        Easy Returns
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

// Helper function to generate star rating HTML
function generateStarRating($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    $html = '';
    
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star"></i>';
    }
    
    // Half star
    if ($halfStar) {
        $html .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="far fa-star"></i>';
    }
    
    return $html;
}
?>