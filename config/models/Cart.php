<?php
class Cart {
    private $conn;
    private $table_name = "cart";

    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get cart items for user
    public function getCartItems($user_id) {
        try {
            $query = "SELECT c.*, p.name, p.brand, p.price, p.original_price, p.image_url, p.stock_quantity 
                      FROM " . $this->table_name . " c
                      JOIN products p ON c.product_id = p.id
                      WHERE c.user_id = :user_id AND p.status = 'active'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Get cart items error: " . $e->getMessage());
            return [];
        }
    }

    // Add item to cart
    public function addToCart($user_id, $product_id, $quantity = 1) {
        try {
            // Check if item already exists in cart
            $existing_item = $this->getCartItem($user_id, $product_id);
            
            if ($existing_item) {
                // Update quantity
                $new_quantity = $existing_item['quantity'] + $quantity;
                return $this->updateQuantity($user_id, $product_id, $new_quantity);
            } else {
                // Add new item
                $query = "INSERT INTO " . $this->table_name . " 
                         SET user_id=:user_id, product_id=:product_id, quantity=:quantity, created_at=NOW()";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->bindParam(":product_id", $product_id);
                $stmt->bindParam(":quantity", $quantity);
                
                if ($stmt->execute()) {
                    return ['success' => true, 'message' => 'Item added to cart'];
                }
                
                return ['success' => false, 'message' => 'Unable to add item to cart'];
            }

        } catch (PDOException $e) {
            error_log("Add to cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Update item quantity
    public function updateQuantity($user_id, $product_id, $quantity) {
        try {
            if ($quantity <= 0) {
                return $this->removeFromCart($user_id, $product_id);
            }

            $query = "UPDATE " . $this->table_name . " 
                      SET quantity=:quantity, updated_at=NOW() 
                      WHERE user_id=:user_id AND product_id=:product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Cart updated'];
            }
            
            return ['success' => false, 'message' => 'Unable to update cart'];

        } catch (PDOException $e) {
            error_log("Update cart quantity error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Remove item from cart
    public function removeFromCart($user_id, $product_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE user_id=:user_id AND product_id=:product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Item removed from cart'];
            }
            
            return ['success' => false, 'message' => 'Unable to remove item from cart'];

        } catch (PDOException $e) {
            error_log("Remove from cart error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    // Get specific cart item
    public function getCartItem($user_id, $product_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE user_id=:user_id AND product_id=:product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return false;

        } catch (PDOException $e) {
            error_log("Get cart item error: " . $e->getMessage());
            return false;
        }
    }

    // Get cart total
    public function getCartTotal($user_id) {
        try {
            $query = "SELECT SUM(c.quantity * p.price) as total 
                      FROM " . $this->table_name . " c
                      JOIN products p ON c.product_id = p.id
                      WHERE c.user_id = :user_id AND p.status = 'active'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;

        } catch (PDOException $e) {
            error_log("Get cart total error: " . $e->getMessage());
            return 0;
        }
    }

    // Get cart item count
    public function getCartItemCount($user_id) {
        try {
            $query = "SELECT SUM(quantity) as total_count 
                      FROM " . $this->table_name . " 
                      WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_count'] ?? 0;

        } catch (PDOException $e) {
            error_log("Get cart item count error: " . $e->getMessage());
            return 0;
        }
    }

    // Clear user's cart
    public function clearCart($user_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE user_id=:user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Clear cart error: " . $e->getMessage());
            return false;
        }
    }
}
?>