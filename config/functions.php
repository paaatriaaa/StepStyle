<?php
// Security and Validation Functions
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Authentication Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('/auth/login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = "Access denied. Admin privileges required.";
        redirect('/index.php');
    }
}

// Database Helper Functions
function getCartCount($db, $user_id = null) {
    if ($user_id) {
        $query = "SELECT SUM(quantity) as total FROM carts WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
    } else {
        $query = "SELECT SUM(quantity) as total FROM carts WHERE session_id = :session_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':session_id', session_id());
    }
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getWishlistCount($db, $user_id) {
    $query = "SELECT COUNT(*) as total FROM wishlists WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

// Product Functions
function formatPrice($price, $currency = 'USD') {
    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($price, $currency);
}

function calculateDiscount($original, $sale) {
    if ($original <= 0) return 0;
    return round((($original - $sale) / $original) * 100);
}

function generateSlug($string) {
    $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($string));
    $slug = trim($slug, '-');
    return $slug;
}

function getProductRating($db, $product_id) {
    $query = "
        SELECT 
            AVG(rating) as average_rating,
            COUNT(*) as review_count
        FROM reviews 
        WHERE product_id = :product_id AND is_approved = TRUE
    ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Order Functions
function generateOrderNumber() {
    return 'STP' . date('Ymd') . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function getOrderStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge badge-warning">Pending</span>',
        'confirmed' => '<span class="badge badge-info">Confirmed</span>',
        'processing' => '<span class="badge badge-primary">Processing</span>',
        'shipped' => '<span class="badge badge-success">Shipped</span>',
        'delivered' => '<span class="badge badge-success">Delivered</span>',
        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
        'refunded' => '<span class="badge badge-secondary">Refunded</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
}

// File Upload Functions
function handleFileUpload($file, $uploadDir = '/assets/images/uploads/') {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $file['error']);
    }
    
    if ($file['size'] > $maxSize) {
        throw new Exception('File size exceeds maximum allowed size (5MB)');
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime, $allowedTypes)) {
        throw new Exception('Invalid file type. Allowed: JPEG, PNG, GIF, WebP');
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $uploadPath = $_SERVER['DOCUMENT_ROOT'] . $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    return $uploadDir . $filename;
}

// Pagination Function
function paginate($page, $perPage, $total) {
    $totalPages = ceil($total / $perPage);
    $offset = ($page - 1) * $perPage;
    
    return [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_previous' => $page > 1,
        'has_next' => $page < $totalPages
    ];
}

// Response Helper Functions
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function redirect($url, $permanent = false) {
    if ($permanent) {
        header('HTTP/1.1 301 Moved Permanently');
    }
    header("Location: $url");
    exit;
}

// Error Handling
function logError($message, $context = []) {
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . ' ' . json_encode($context));
}

function handleException($exception) {
    logError('Uncaught Exception: ' . $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    if (php_sapi_name() === 'cli') {
        echo "Error: " . $exception->getMessage() . "\n";
    } else {
        http_response_code(500);
        if (defined('DEBUG') && DEBUG) {
            echo "Error: " . $exception->getMessage();
        } else {
            echo "An unexpected error occurred. Please try again later.";
        }
    }
    exit;
}

set_exception_handler('handleException');
?>