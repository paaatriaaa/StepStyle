<?php
class OrderItem {
    private $conn;
    private $table_name = "order_items";

    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $price;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Add item to order
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     SET order_id=:order_id, product_id=:product_id, 
                         quantity=:quantity, price=:price";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":order_id", $this->order_id);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":quantity", $this->quantity);
            $stmt->bindParam(":price", $this->price);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Order item added successfully');
            }

            return array('success' => false, 'message' => 'Unable to add order item');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get items by order ID
    public function getItemsByOrder($order_id) {
        try {
            $query = "SELECT oi.*, p.name, p.brand, p.image_url 
                      FROM " . $this->table_name . " oi
                      LEFT JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = :order_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $order_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Get order items with product details
    public function getOrderItemsWithProducts($order_id) {
        try {
            $query = "SELECT 
                        oi.*,
                        p.name as product_name,
                        p.brand as product_brand,
                        p.image_url as product_image,
                        (oi.quantity * oi.price) as item_total
                      FROM " . $this->table_name . " oi
                      LEFT JOIN products p ON oi.product_id = p.id
                      WHERE oi.order_id = :order_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $order_id);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // UPDATE - Update item quantity
    public function updateQuantity($item_id, $quantity) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET quantity = :quantity 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":id", $item_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Remove item from order
    public function delete($item_id) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $item_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Calculate order total
    public function calculateOrderTotal($order_id) {
        try {
            $query = "SELECT SUM(quantity * price) as order_total 
                      FROM " . $this->table_name . " 
                      WHERE order_id = :order_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $order_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['order_total'] ?? 0;

        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>