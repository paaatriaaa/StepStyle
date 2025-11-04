<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['success' => false, 'message' => 'Invalid request method']);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['cart_id']) || !isset($input['quantity']) || !isset($input['csrf_token'])) {
        sendJSONResponse(['success' => false, 'message' => 'Missing required fields']);
    }
    
    if (!validateCSRFToken($input['csrf_token'])) {
        sendJSONResponse(['success' => false, 'message' => 'Invalid CSRF token']);
    }
    
    $database = new Database();
    $db = $database->getConnection();
    
    $cart_id = sanitize($input['cart_id']);
    $quantity = max(1, intval($input['quantity']));
    
    // Verify cart item belongs to user
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $session_id = session_id();
    
    $verify_query = "
        SELECT c.*, p.quantity as max_quantity, v.quantity as variant_max_quantity
        FROM carts c
        JOIN products p ON c.product_id = p.id
        LEFT JOIN product_variants v ON c.variant_id = v.id
        WHERE c.id = :cart_id AND (c.user_id = :user_id OR c.session_id = :session_id)
    ";
    $verify_stmt = $db->prepare($verify_query);
    $verify_stmt->bindParam(':cart_id', $cart_id);
    $verify_stmt->bindParam(':user_id', $user_id);
    $verify_stmt->bindParam(':session_id', $session_id);
    $verify_stmt->execute();
    $cart_item = $verify_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$cart_item) {
        sendJSONResponse(['success' => false, 'message' => 'Cart item not found']);
    }
    
    $max_quantity = $cart_item['variant_id'] ? $cart_item['variant_max_quantity'] : $cart_item['max_quantity'];
    
    if ($quantity > $max_quantity) {
        sendJSONResponse(['success' => false, 'message' => 'Quantity exceeds available stock']);
    }
    
    // Update quantity
    $update_query = "UPDATE carts SET quantity = :quantity WHERE id = :cart_id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':quantity', $quantity);
    $update_stmt->bindParam(':cart_id', $cart_id);
    $update_stmt->execute();
    
    // Get updated cart count
    $cart_count = getCartCount($db, $user_id);
    
    sendJSONResponse([
        'success' => true,
        'message' => 'Cart updated successfully',
        'cart_count' => $cart_count
    ]);
    
} catch (Exception $e) {
    error_log("Cart update error: " . $e->getMessage());
    sendJSONResponse(['success' => false, 'message' => 'Failed to update cart']);
}
?>