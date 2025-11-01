<?php
class Database {
    private $host = "localhost";
    private $db_name = "stepstyle_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
            $this->conn->exec("SET sql_mode = 'STRICT_TRANS_TABLES'");
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
        return $this->conn;
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function commit() {
        return $this->conn->commit();
    }

    public function rollBack() {
        return $this->conn->rollBack();
    }
}

// Database Schema Helper
class DatabaseSchema {
    public static function initialize($db) {
        $tables = [
            "users" => "
                CREATE TABLE IF NOT EXISTS users (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    phone VARCHAR(20),
                    address TEXT,
                    role ENUM('user', 'admin') DEFAULT 'user',
                    avatar VARCHAR(255) DEFAULT '/assets/images/avatars/default.png',
                    email_verified BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "categories" => "
                CREATE TABLE IF NOT EXISTS categories (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    slug VARCHAR(100) UNIQUE NOT NULL,
                    description TEXT,
                    image VARCHAR(255),
                    parent_id INT DEFAULT NULL,
                    is_active BOOLEAN DEFAULT TRUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "brands" => "
                CREATE TABLE IF NOT EXISTS brands (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(100) NOT NULL,
                    slug VARCHAR(100) UNIQUE NOT NULL,
                    description TEXT,
                    logo VARCHAR(255),
                    is_featured BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "products" => "
                CREATE TABLE IF NOT EXISTS products (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    description TEXT,
                    short_description TEXT,
                    price DECIMAL(10,2) NOT NULL,
                    compare_price DECIMAL(10,2),
                    cost_price DECIMAL(10,2),
                    sku VARCHAR(100) UNIQUE,
                    barcode VARCHAR(100),
                    quantity INT DEFAULT 0,
                    track_quantity BOOLEAN DEFAULT TRUE,
                    is_featured BOOLEAN DEFAULT FALSE,
                    is_published BOOLEAN DEFAULT TRUE,
                    brand_id INT,
                    category_id INT,
                    weight DECIMAL(8,2),
                    dimensions VARCHAR(100),
                    seo_title VARCHAR(255),
                    seo_description TEXT,
                    seo_keywords TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
                    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "product_images" => "
                CREATE TABLE IF NOT EXISTS product_images (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    product_id INT NOT NULL,
                    image_url VARCHAR(255) NOT NULL,
                    alt_text VARCHAR(255),
                    is_primary BOOLEAN DEFAULT FALSE,
                    sort_order INT DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "product_variants" => "
                CREATE TABLE IF NOT EXISTS product_variants (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    product_id INT NOT NULL,
                    size VARCHAR(20),
                    color VARCHAR(50),
                    material VARCHAR(50),
                    additional_price DECIMAL(10,2) DEFAULT 0,
                    sku VARCHAR(100),
                    quantity INT DEFAULT 0,
                    image_url VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_variant (product_id, size, color)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "reviews" => "
                CREATE TABLE IF NOT EXISTS reviews (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    product_id INT NOT NULL,
                    user_id INT NOT NULL,
                    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
                    title VARCHAR(255),
                    comment TEXT,
                    is_approved BOOLEAN DEFAULT FALSE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "wishlists" => "
                CREATE TABLE IF NOT EXISTS wishlists (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NOT NULL,
                    product_id INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    UNIQUE KEY unique_wishlist (user_id, product_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "carts" => "
                CREATE TABLE IF NOT EXISTS carts (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NOT NULL,
                    session_id VARCHAR(255),
                    product_id INT NOT NULL,
                    variant_id INT,
                    quantity INT NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "orders" => "
                CREATE TABLE IF NOT EXISTS orders (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    order_number VARCHAR(50) UNIQUE NOT NULL,
                    user_id INT NOT NULL,
                    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
                    subtotal DECIMAL(10,2) NOT NULL,
                    tax_amount DECIMAL(10,2) DEFAULT 0,
                    shipping_amount DECIMAL(10,2) DEFAULT 0,
                    discount_amount DECIMAL(10,2) DEFAULT 0,
                    total_amount DECIMAL(10,2) NOT NULL,
                    payment_method VARCHAR(50),
                    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
                    shipping_address TEXT,
                    billing_address TEXT,
                    customer_note TEXT,
                    admin_note TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ",
            "order_items" => "
                CREATE TABLE IF NOT EXISTS order_items (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    order_id INT NOT NULL,
                    product_id INT NOT NULL,
                    variant_id INT,
                    product_name VARCHAR(255) NOT NULL,
                    variant_info TEXT,
                    quantity INT NOT NULL,
                    unit_price DECIMAL(10,2) NOT NULL,
                    total_price DECIMAL(10,2) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id),
                    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            "
        ];

        try {
            $db->beginTransaction();
            
            foreach ($tables as $tableName => $sql) {
                $db->exec($sql);
                error_log("Table {$tableName} created or already exists");
            }
            
            // Insert default data
            self::insertDefaultData($db);
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Schema initialization failed: " . $e->getMessage());
            return false;
        }
    }

    private static function insertDefaultData($db) {
        // Insert default admin user
        $checkAdmin = $db->query("SELECT COUNT(*) FROM users WHERE email = 'admin@stepstyle.com'")->fetchColumn();
        if ($checkAdmin == 0) {
            $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute(['Administrator', 'admin@stepstyle.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
        }

        // Insert default categories
        $categories = [
            ['Sneakers', 'sneakers', 'Latest sneaker collection'],
            ['Running', 'running', 'Running and athletic shoes'],
            ['Basketball', 'basketball', 'Basketball shoes and gear'],
            ['Casual', 'casual', 'Everyday casual footwear'],
            ['Lifestyle', 'lifestyle', 'Fashion and lifestyle shoes']
        ];

        $checkCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($checkCategories == 0) {
            $stmt = $db->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
            foreach ($categories as $category) {
                $stmt->execute($category);
            }
        }

        // Insert default brands
        $brands = [
            ['Nike', 'nike', 'Just Do It', true],
            ['Adidas', 'adidas', 'Impossible is Nothing', true],
            ['Jordan', 'jordan', 'Air Jordan Collection', true],
            ['Puma', 'puma', 'Forever Faster', false],
            ['New Balance', 'new-balance', 'Fearlessly Independent', false],
            ['Vans', 'vans', 'Off The Wall', false]
        ];

        $checkBrands = $db->query("SELECT COUNT(*) FROM brands")->fetchColumn();
        if ($checkBrands == 0) {
            $stmt = $db->prepare("INSERT INTO brands (name, slug, description, is_featured) VALUES (?, ?, ?, ?)");
            foreach ($brands as $brand) {
                $stmt->execute($brand);
            }
        }
    }
}
?>