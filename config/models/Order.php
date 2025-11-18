<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $user_id;
    public $order_number;
    public $total_amount;
    public $status;
    public $shipping_address;
    public $billing_address;
    public $tracking_number;
    public $payment_method;
    public $payment_status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Create new order
    public function create() {
        try {
            // Generate order number
            $this->order_number = $this->generateOrderNumber();

            $query = "INSERT INTO " . $this->table_name . " 
                     SET user_id=:user_id, order_number=:order_number, total_amount=:total_amount,
                         status=:status, shipping_address=:shipping_address, billing_address=:billing_address,
                         payment_method=:payment_method, payment_status=:payment_status";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":order_number", $this->order_number);
            $stmt->bindParam(":total_amount", $this->total_amount);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":shipping_address", $this->shipping_address);
            $stmt->bindParam(":billing_address", $this->billing_address);
            $stmt->bindParam(":payment_method", $this->payment_method);
            $stmt->bindParam(":payment_status", $this->payment_status);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return array('success' => true, 'message' => 'Order created successfully', 'order_id' => $this->id, 'order_number' => $this->order_number);
            }

            return array('success' => false, 'message' => 'Unable to create order');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get order by ID
    public function getOrderById($order_id) {
        try {
            $query = "SELECT o.*, u.first_name, u.last_name, u.email 
                      FROM " . $this->table_name . " o
                      LEFT JOIN users u ON o.user_id = u.id
                      WHERE o.id = :order_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_id", $order_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // READ - Get order by order number
    public function getOrderByNumber($order_number) {
        try {
            $query = "SELECT o.*, u.first_name, u.last_name, u.email 
                      FROM " . $this->table_name . " o
                      LEFT JOIN users u ON o.user_id = u.id
                      WHERE o.order_number = :order_number";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":order_number", $order_number);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // READ - Get orders by user ID
    public function getOrdersByUser($user_id, $limit = 10) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE user_id = :user_id 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Get all orders with filters (for admin)
    public function getAllOrders($filters = array()) {
        try {
            $query = "SELECT o.*, u.first_name, u.last_name, u.email 
                      FROM " . $this->table_name . " o
                      LEFT JOIN users u ON o.user_id = u.id 
                      WHERE 1=1";
            $params = array();

            if (!empty($filters['status'])) {
                $query .= " AND o.status = :status";
                $params[':status'] = $filters['status'];
            }

            if (!empty($filters['payment_status'])) {
                $query .= " AND o.payment_status = :payment_status";
                $params[':payment_status'] = $filters['payment_status'];
            }

            if (!empty($filters['date_from'])) {
                $query .= " AND DATE(o.created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }

            if (!empty($filters['date_to'])) {
                $query .= " AND DATE(o.created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            $query .= " ORDER BY o.created_at DESC";

            if (!empty($filters['limit'])) {
                $query .= " LIMIT :limit";
                if (!empty($filters['offset'])) {
                    $query .= " OFFSET :offset";
                }
            }

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            if (!empty($filters['limit'])) {
                $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
                if (!empty($filters['offset'])) {
                    $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
                }
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // UPDATE - Update order status
    public function updateStatus($order_id, $status) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $order_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE - Update payment status
    public function updatePaymentStatus($order_id, $payment_status) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET payment_status = :payment_status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":payment_status", $payment_status);
            $stmt->bindParam(":id", $order_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE - Update tracking number
    public function updateTrackingNumber($order_id, $tracking_number) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET tracking_number = :tracking_number, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":tracking_number", $tracking_number);
            $stmt->bindParam(":id", $order_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Cancel order (soft delete)
    public function cancel($order_id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'cancelled', updated_at = NOW() 
                      WHERE id = :id AND status = 'pending'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $order_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Generate unique order number
    private function generateOrderNumber() {
        return 'STP' . date('Ymd') . strtoupper(uniqid());
    }

    // Get order statistics
    public function getOrderStats($period = 'month') {
        try {
            $date_format = "";
            switch ($period) {
                case 'day':
                    $date_format = "%Y-%m-%d";
                    break;
                case 'week':
                    $date_format = "%Y-%u";
                    break;
                case 'month':
                    $date_format = "%Y-%m";
                    break;
                case 'year':
                    $date_format = "%Y";
                    break;
            }

            $query = "SELECT 
                        DATE_FORMAT(created_at, :date_format) as period,
                        COUNT(*) as order_count,
                        SUM(total_amount) as total_revenue
                      FROM " . $this->table_name . " 
                      WHERE status != 'cancelled' 
                      GROUP BY period 
                      ORDER BY period DESC 
                      LIMIT 12";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":date_format", $date_format);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }
}
?>