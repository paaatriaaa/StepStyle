<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    public $id;
    public $user_id;
    public $product_id;
    public $rating;
    public $comment;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
        
        // Create reviews table if not exists
        $this->createReviewsTable();
    }

    private function createReviewsTable() {
        try {
            $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                product_id INT NOT NULL,
                rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                comment TEXT,
                status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                UNIQUE KEY unique_user_product (user_id, product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->conn->exec($query);
        } catch (PDOException $e) {
            // Table might already exist
        }
    }

    // CREATE - Add review
    public function create() {
        try {
            // Check if user already reviewed this product
            if ($this->hasUserReviewed($this->user_id, $this->product_id)) {
                return array('success' => false, 'message' => 'You have already reviewed this product');
            }

            $query = "INSERT INTO " . $this->table_name . " 
                     SET user_id=:user_id, product_id=:product_id, rating=:rating, 
                         comment=:comment, status='pending'";

            $stmt = $this->conn->prepare($query);

            // Sanitize
            $this->comment = htmlspecialchars(strip_tags($this->comment));

            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":product_id", $this->product_id);
            $stmt->bindParam(":rating", $this->rating);
            $stmt->bindParam(":comment", $this->comment);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return array('success' => true, 'message' => 'Review submitted successfully', 'review_id' => $this->id);
            }

            return array('success' => false, 'message' => 'Unable to submit review');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get reviews for a product
    public function getProductReviews($product_id, $status = 'approved', $limit = 10) {
        try {
            $query = "SELECT r.*, u.first_name, u.last_name 
                      FROM " . $this->table_name . " r
                      LEFT JOIN users u ON r.user_id = u.id
                      WHERE r.product_id = :product_id AND r.status = :status
                      ORDER BY r.created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->bindParam(":status", $status);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Get user's reviews
    public function getUserReviews($user_id, $limit = 10) {
        try {
            $query = "SELECT r.*, p.name as product_name, p.image_url as product_image 
                      FROM " . $this->table_name . " r
                      LEFT JOIN products p ON r.product_id = p.id
                      WHERE r.user_id = :user_id
                      ORDER BY r.created_at DESC 
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

    // READ - Get review by ID
    public function getReviewById($review_id) {
        try {
            $query = "SELECT r.*, u.first_name, u.last_name, p.name as product_name 
                      FROM " . $this->table_name . " r
                      LEFT JOIN users u ON r.user_id = u.id
                      LEFT JOIN products p ON r.product_id = p.id
                      WHERE r.id = :review_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":review_id", $review_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE - Update review status (for admin)
    public function updateStatus($review_id, $status) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = :status, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $review_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Delete review
    public function delete($review_id, $user_id = null) {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            
            if ($user_id) {
                $query .= " AND user_id = :user_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $review_id);
            
            if ($user_id) {
                $stmt->bindParam(":user_id", $user_id);
            }
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Check if user has reviewed a product
    public function hasUserReviewed($user_id, $product_id) {
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

    // Get product rating statistics
    public function getProductRatingStats($product_id) {
        try {
            $query = "SELECT 
                        COUNT(*) as total_reviews,
                        AVG(rating) as average_rating,
                        COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                        COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                        COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                        COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                        COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                      FROM " . $this->table_name . " 
                      WHERE product_id = :product_id AND status = 'approved'";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Format average rating
            if ($result['average_rating']) {
                $result['average_rating'] = round($result['average_rating'], 1);
            }

            return $result;

        } catch (PDOException $e) {
            return array(
                'total_reviews' => 0,
                'average_rating' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0
            );
        }
    }

    // Get pending reviews (for admin)
    public function getPendingReviews($limit = 20) {
        try {
            $query = "SELECT r.*, u.first_name, u.last_name, p.name as product_name 
                      FROM " . $this->table_name . " r
                      LEFT JOIN users u ON r.user_id = u.id
                      LEFT JOIN products p ON r.product_id = p.id
                      WHERE r.status = 'pending'
                      ORDER BY r.created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }
}
?>