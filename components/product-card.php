<?php
// components/product-card.php

// Ensure $product variable is set and has required structure
if (!isset($product) || !is_array($product)) {
    return;
}

// Set default values to avoid undefined array key warnings
$product['id'] = $product['id'] ?? 0;
$product['name'] = $product['name'] ?? 'Unknown Product';
$product['brand'] = $product['brand'] ?? 'Unknown Brand';
$product['price'] = $product['price'] ?? 0;
$product['original_price'] = $product['original_price'] ?? 0;
$product['image_url'] = $product['image_url'] ?? '';
$product['rating'] = $product['rating'] ?? 0;
$product['review_count'] = $product['review_count'] ?? 0;
$product['stock_quantity'] = $product['stock_quantity'] ?? 0;
$product['featured'] = $product['featured'] ?? false;
$product['on_sale'] = $product['on_sale'] ?? false;
$product['new_arrival'] = $product['new_arrival'] ?? false;

// Calculate discount if applicable
$discount = 0;
if (!empty($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] > $product['price']) {
    $discount = round((($product['original_price'] - $product['price']) / $product['original_price']) * 100);
}

// Determine card variant based on product properties
$card_class = 'product-card';
if (!empty($product['featured'])) {
    $card_class .= ' featured';
}
if (!empty($product['on_sale'])) {
    $card_class .= ' sale';
}
if ($discount > 0) {
    $card_class .= ' discounted';
}
if (!empty($product['new_arrival'])) {
    $card_class .= ' new-arrival';
}
?>

<div class="<?php echo $card_class; ?>" data-product-id="<?php echo $product['id']; ?>">
    <div class="product-image">
        <a href="products/detail.php?id=<?php echo $product['id']; ?>" class="product-image-link">
            <?php if (!empty($product['image_url'])): ?>
                <img src="<?php echo $product['image_url']; ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="product-image-main"
                     loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <?php endif; ?>
            <div class="product-image-placeholder" style="<?php echo !empty($product['image_url']) ? 'display: none;' : 'display: flex;'; ?>">
                <i class="fas fa-shoe-prints"></i>
                <span class="placeholder-text"><?php echo htmlspecialchars($product['brand']); ?></span>
            </div>
        </a>
        
        <!-- Product Badges -->
        <div class="product-badges">
            <?php if ($discount > 0): ?>
                <span class="badge discount">-<?php echo $discount; ?>%</span>
            <?php endif; ?>
            <?php if (!empty($product['new_arrival'])): ?>
                <span class="badge new">NEW</span>
            <?php endif; ?>
            <?php if (!empty($product['stock_quantity']) && $product['stock_quantity'] <= 0): ?>
                <span class="badge out-of-stock">SOLD OUT</span>
            <?php elseif (!empty($product['stock_quantity']) && $product['stock_quantity'] <= 5): ?>
                <span class="badge low-stock">LOW STOCK</span>
            <?php endif; ?>
            <?php if (!empty($product['featured'])): ?>
                <span class="badge featured">FEATURED</span>
            <?php endif; ?>
        </div>
        
        <!-- Product Actions -->
        <div class="product-actions">
            <button class="action-btn wishlist-btn" title="Add to Wishlist" data-product-id="<?php echo $product['id']; ?>">
                <i class="far fa-heart"></i>
            </button>
            <button class="action-btn quick-view-btn" title="Quick View" data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <!-- Add to Cart Button -->
        <?php if (!empty($product['stock_quantity']) && $product['stock_quantity'] > 0): ?>
            <button class="btn-add-cart" data-product-id="<?php echo $product['id']; ?>">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
            </button>
        <?php else: ?>
            <button class="btn-add-cart disabled" disabled>
                <i class="fas fa-ban"></i>
                Out of Stock
            </button>
        <?php endif; ?>
    </div>
    
    <div class="product-info">
        <!-- Product Brand -->
        <div class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></div>
        
        <!-- Product Title -->
        <h3 class="product-title">
            <a href="products/detail.php?id=<?php echo $product['id']; ?>">
                <?php echo htmlspecialchars($product['name']); ?>
            </a>
        </h3>
        
        <!-- Product Price -->
        <div class="product-price">
            <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
            <?php if (!empty($product['original_price']) && $product['original_price'] > 0 && $product['original_price'] > $product['price']): ?>
                <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Product Rating -->
        <div class="product-rating">
            <div class="stars">
                <?php 
                // Simple star rating generator
                $rating = !empty($product['rating']) ? $product['rating'] : 0;
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                
                for ($i = 0; $i < $fullStars; $i++) {
                    echo '<i class="fas fa-star"></i>';
                }
                if ($halfStar) {
                    echo '<i class="fas fa-star-half-alt"></i>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                    echo '<i class="far fa-star"></i>';
                }
                ?>
            </div>
            <span class="rating-count">(<?php echo !empty($product['review_count']) ? $product['review_count'] : 0; ?>)</span>
        </div>
    </div>
</div>