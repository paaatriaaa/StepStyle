<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $brand;
    public $price;
    public $original_price;
    public $description;
    public $image_url;
    public $stock_quantity;
    public $featured;
    public $on_sale;
    public $new_arrival;
    public $status;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Add new product
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     SET name=:name, brand=:brand, price=:price, original_price=:original_price,
                         description=:description, image_url=:image_url, stock_quantity=:stock_quantity,
                         featured=:featured, on_sale=:on_sale, new_arrival=:new_arrival, status=:status";

            $stmt = $this->conn->prepare($query);

            // Sanitize and bind parameters
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->brand = htmlspecialchars(strip_tags($this->brand));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->image_url = htmlspecialchars(strip_tags($this->image_url));

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":brand", $this->brand);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":original_price", $this->original_price);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":image_url", $this->image_url);
            $stmt->bindParam(":stock_quantity", $this->stock_quantity);
            $stmt->bindParam(":featured", $this->featured);
            $stmt->bindParam(":on_sale", $this->on_sale);
            $stmt->bindParam(":new_arrival", $this->new_arrival);
            $stmt->bindParam(":status", $this->status);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return array('success' => true, 'message' => 'Product created successfully', 'product_id' => $this->id);
            }

            return array('success' => false, 'message' => 'Unable to create product');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get all products with filters
    public function getProducts($filters = array()) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'active'";
            $params = array();

            // Apply filters
            if (!empty($filters['brand'])) {
                $query .= " AND brand = :brand";
                $params[':brand'] = $filters['brand'];
            }

            if (!empty($filters['featured'])) {
                $query .= " AND featured = 1";
            }

            if (!empty($filters['on_sale'])) {
                $query .= " AND on_sale = 1";
            }

            if (!empty($filters['new_arrival'])) {
                $query .= " AND new_arrival = 1";
            }

            if (!empty($filters['min_price'])) {
                $query .= " AND price >= :min_price";
                $params[':min_price'] = $filters['min_price'];
            }

            if (!empty($filters['max_price'])) {
                $query .= " AND price <= :max_price";
                $params[':max_price'] = $filters['max_price'];
            }

            // Sorting
            $order_by = " ORDER BY created_at DESC";
            if (!empty($filters['sort'])) {
                switch ($filters['sort']) {
                    case 'price_low':
                        $order_by = " ORDER BY price ASC";
                        break;
                    case 'price_high':
                        $order_by = " ORDER BY price DESC";
                        break;
                    case 'name':
                        $order_by = " ORDER BY name ASC";
                        break;
                    case 'newest':
                        $order_by = " ORDER BY created_at DESC";
                        break;
                }
            }
            $query .= $order_by;

            // Pagination
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

            // Bind limit and offset if they exist
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

    // READ - Get product by ID
    public function getProductById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // READ - Get featured products
    public function getFeaturedProducts($limit = 8) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE featured = 1 AND status = 'active' 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Get new arrivals
    public function getNewArrivals($limit = 8) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE new_arrival = 1 AND status = 'active' 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // READ - Get products on sale
    public function getProductsOnSale($limit = 8) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE on_sale = 1 AND status = 'active' 
                      ORDER BY created_at DESC 
                      LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // UPDATE - Update product
    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET name=:name, brand=:brand, price=:price, original_price=:original_price,
                          description=:description, image_url=:image_url, stock_quantity=:stock_quantity,
                          featured=:featured, on_sale=:on_sale, new_arrival=:new_arrival, status=:status,
                          updated_at=NOW()
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Sanitize
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->brand = htmlspecialchars(strip_tags($this->brand));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->image_url = htmlspecialchars(strip_tags($this->image_url));

            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":brand", $this->brand);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":original_price", $this->original_price);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":image_url", $this->image_url);
            $stmt->bindParam(":stock_quantity", $this->stock_quantity);
            $stmt->bindParam(":featured", $this->featured);
            $stmt->bindParam(":on_sale", $this->on_sale);
            $stmt->bindParam(":new_arrival", $this->new_arrival);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":id", $this->id);

            if ($stmt->execute()) {
                return array('success' => true, 'message' => 'Product updated successfully');
            }

            return array('success' => false, 'message' => 'Unable to update product');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // UPDATE - Update stock quantity
    public function updateStock($product_id, $quantity) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET stock_quantity = :quantity, updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":id", $product_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Soft delete product
    public function delete($product_id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'inactive', updated_at = NOW() 
                      WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $product_id);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Search products
    public function searchProducts($search_term, $limit = 20) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE (name LIKE :search_term OR brand LIKE :search_term OR description LIKE :search_term) 
                      AND status = 'active' 
                      ORDER BY 
                        CASE 
                            WHEN name LIKE :search_term_exact THEN 1
                            WHEN brand LIKE :search_term_exact THEN 2
                            ELSE 3
                        END,
                        created_at DESC 
                      LIMIT :limit";
            
            $search_term = "%" . $search_term . "%";
            $search_term_exact = $search_term;
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":search_term", $search_term);
            $stmt->bindParam(":search_term_exact", $search_term_exact);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return array();
        }
    }

    // Get brands
    public function getBrands() {
        try {
            $query = "SELECT DISTINCT brand FROM " . $this->table_name . " WHERE status = 'active' ORDER BY brand";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        } catch (PDOException $e) {
            return array();
        }
    }
}
?>