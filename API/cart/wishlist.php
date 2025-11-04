<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['success' => false, 'message' => 'Invalid request method']);
}

if (!isLoggedIn()) {
    sendJSONResponse(['success' => false, 'message' => 'Please login to add items to wishlist']);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['product_id']) || !isset($input['csrf_token'])) {
        sendJSONResponse(['success' => false, 'message' => 'Missing required fields']);
    }
    
    if (!validateCSRFToken($input['csrf_token'])) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid CSRF token']);
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $product_id = sanitize($input['product_id']);
    $user_id = $_SESSION['user_id'];
    
    // Check if product exists
    $product_query = "SELECT id FROM products WHERE id = :id AND is_published = TRUE";
    $product_stmt = $db->prepare($product_query);
    $product_stmt->bindParam(':id', $product_id);
    $product_stmt->execute();
    
    if (!$product_stmt->fetch()) {
        sendJSONResponse(['success' => false, 'message' => 'Product not found']);
    }
    
    // Check if already in wishlist
    $check_query = "SELECT id FROM wishlists WHERE user_id = :user_id AND product_id = :product_id";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':user_id', $user_id);
    $check_stmt->bindParam(':product_id', $product_id);
    $check_stmt->execute();
    
    if ($check_stmt->fetch()) {
        sendJSONResponse(['success' => false, 'message' => 'Product already in wishlist']);
    }
    
    // Add to wishlist
    $insert_query = "INSERT INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)";
    $insert_stmt = $db->prepare($insert_query);
    $insert_stmt->bindParam(':user_id', $user_id);
    $insert_stmt->bindParam(':product_id', $product_id);
    $insert_stmt->execute();
    
    // Get updated wishlist count
    $wishlist_count = getWishlistCount($db, $user_id);
    
    sendJSONResponse([
        'success' => true,
        'message' => 'Product added to wishlist',
        'wishlist_count' => $wishlist_count
    ]);
    
} catch (Exception $e) {
    error_log("Wishlist add error: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to add product to wishlist']);
}
?>