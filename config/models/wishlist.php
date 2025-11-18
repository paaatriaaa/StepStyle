<?php
class Wishlist {
    private $conn;
    private $table_name = "wishlist";

    public $id;
    public $user_id;
    public $product_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
        
        // Create wishlist table if not exists
        $this->createWishlistTable();
    }

    private function createWishlistTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                UNIQUE KEY unique_wishlist_item (user_id, product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->conn->exec($query);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }

    // CREATE - Add item to wishlist
    public function addToWishlist($user_id, $product_id) {
        try {
            // Check if item already exists in wishlist
            if ($this->isInWishlist($user_id, $product_id)) {
                return array('success' => false, 'message' => 'Item already in wishlist');
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     SET user_id=:user_id, product_id=:product_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Item added to wishlist successfully');
            }

            return array('success' => false, 'message' => 'Unable to add item to wishlist');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get user's wishlist items
    public function getWishlistItems($user_id) {
        try {
            $query = "SELECT w.*, p.name, p.brand, p.price, p.original_price, 
                             p.image_url, p.stock_quantity, p.on_sale
                      FROM " . $this->table_name . " w
                      LEFT JOIN products p ON w.product_id = p.id
                      WHERE w.user_id = :user_id AND p.status = 'active'
                      ORDER BY w.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Check if item is in wishlist
    public function isInWishlist($user_id, $product_id) {
        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Remove item from wishlist
    public function removeFromWishlist($user_id, $product_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " 
                      WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            
            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Item removed from wishlist successfully');
            }

            return array('success' => false, 'message' => 'Unable to remove item from wishlist');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // DELETE - Clear user's wishlist
    public function clearWishlist($user_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Get wishlist count
    public function getWishlistCount($user_id) {
        try {
            $query = "SELECT COUNT(*) as wishlist_count 
                      FROM " . $this->table_name . " 
                      WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['wishlist_count'] ?? 0;

        } catch (PDOException $e) {
            return 0;
        }
    }

    // Move wishlist item to cart
    public function moveToCart($user_id, $product_id, $cart_model) {
        try {
            // Add to cart
            $result = $cart_model->addToCart($user_id, $product_id);
            
            if ($result['success']) {
                // Remove from wishlist
                $this->removeFromWishlist($user_id, $product_id);
                return array('success' => true, 'message' => 'Item moved to cart successfully');
            }

            return $result;

        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Error moving item to cart: ' . $e->getMessage());
        }
    }
}
?>