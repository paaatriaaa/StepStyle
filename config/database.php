<?php
// Database Configuration
class Database {
    private $host = 'localhost';
    private $db_name = 'stepstyle_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Coba koneksi ke database yang sudah ada
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Jika database tidak ada, buat database dan tabel
            $this->createDatabaseAndTables();
        }

        return $this->conn;
    }

    private function createDatabaseAndTables() {
        try {
            // Koneksi tanpa database terlebih dahulu
            $dsn = "mysql:host=" . $this->host . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Buat database jika belum ada
            $this->conn->exec("CREATE DATABASE IF NOT EXISTS `$this->db_name`");
            $this->conn->exec("USE `$this->db_name`");

            // Create users table
            $sql = "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                phone VARCHAR(20),
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('customer', 'admin') DEFAULT 'customer',
                newsletter TINYINT(1) DEFAULT 0,
                email_verified TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL,
                status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($sql);
            
            // Create user_addresses table
            $sql = "CREATE TABLE IF NOT EXISTS user_addresses (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                address_type ENUM('billing', 'shipping') DEFAULT 'shipping',
                street_address TEXT NOT NULL,
                city VARCHAR(50) NOT NULL,
                state VARCHAR(50) NOT NULL,
                zip_code VARCHAR(20) NOT NULL,
                country VARCHAR(50) DEFAULT 'United States',
                is_default TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($sql);

            // Create products table
            $sql = "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                brand VARCHAR(100) NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                original_price DECIMAL(10,2) DEFAULT 0,
                description TEXT,
                image_url VARCHAR(500),
                stock_quantity INT DEFAULT 0,
                featured TINYINT(1) DEFAULT 0,
                on_sale TINYINT(1) DEFAULT 0,
                new_arrival TINYINT(1) DEFAULT 0,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->conn->exec($sql);

            // Create orders table
            $sql = "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
                shipping_address TEXT NOT NULL,
                billing_address TEXT,
                tracking_number VARCHAR(100),
                payment_method VARCHAR(50),
                payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->conn->exec($sql);

            // Create order_items table
            $sql = "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                quantity INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

            $this->conn->exec($sql);

            // Insert demo data
            $this->insertDemoData();
            
            error_log("Database and tables created successfully");
            
        } catch (PDOException $e) {
            error_log("Database creation failed: " . $e->getMessage());
            // Tetap buat koneksi meski gagal buat tabel
            try {
                $dsn = "mysql:host=" . $this->host . ";charset=" . $this->charset;
                $this->conn = new PDO($dsn, $this->username, $this->password);
            } catch (PDOException $e2) {
                error_log("Final connection attempt failed: " . $e2->getMessage());
            }
        }
    }

    private function insertDemoData() {
        try {
            // Insert admin user
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT IGNORE INTO users (first_name, last_name, email, password_hash, role, newsletter, email_verified) 
                    VALUES ('Admin', 'User', 'admin@stepstyle.com', ?, 'admin', 1, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$hashed_password]);

            // Insert demo customer
            $hashed_password = password_hash('password123', PASSWORD_DEFAULT);
            $sql = "INSERT IGNORE INTO users (first_name, last_name, email, password_hash, role, newsletter, email_verified) 
                    VALUES ('Demo', 'User', 'demo@stepstyle.com', ?, 'customer', 1, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$hashed_password]);

            // Insert sample products
            $sample_products = [
                [
                    'Puma MB.05', 'PUMA', 110.00, 0, 'Basketball shoes with advanced cushioning',
                    'https://images.puma.com/image/upload/f_auto,q_auto,b_rgb:fafafa,w_600,h_600/global/312131/01/sv02/fnd/PNA/fmt/png/PUMA-x-LAMELO-BALL-MB.05-Voltage-Basketball-Shoes',
                    15, 1, 0, 0
                ],
                [
                    'Vans Skate Loafer', 'VANS', 60.00, 0, 'Classic skateboarding shoes',
                    'https://assets.vans.eu/images/t_img/c_fill,g_center,f_auto,h_815,w_652,e_unsharp_mask:100/dpr_2.0/v1753291890/VN0A5DXUBKA-ALT2/Skate-Loafer-Shoes.jpg',
                    25, 1, 0, 0
                ],
                [
                    'Converse Chuck 70', 'CONVERSE', 85.00, 100.00, 'Iconic canvas sneakers',
                    'https://clothbase.s3.amazonaws.com/uploads/10c6f920-e854-4bc8-90c3-c2d86817751b/image.jpg',
                    3, 1, 1, 0
                ],
                [
                    'Reebok Court Advance', 'REEBOK', 75.00, 0, 'Comfortable court shoes',
                    'https://reebokbr.vtexassets.com/arquivos/ids/161812/HR1485--1-.jpg?v=638115718439370000',
                    12, 1, 0, 0
                ],
                [
                    'Nike Alphafly 3', 'NIKE', 150.00, 0, 'Professional running shoes',
                    'https://static.nike.com/a/images/t_PDP_1728_v1/f_auto,q_auto:eco/50484187-18b3-4373-8118-8ea0f0f37093/AIR+ZOOM+ALPHAFLY+NEXT%25+3+PRM.png',
                    8, 1, 0, 0
                ],
                [
                    'Nike Dunk Low', 'NIKE', 110.00, 0, 'Popular lifestyle sneakers',
                    'https://sneakerbardetroit.com/wp-content/uploads/2023/05/Nike-Dunk-Low-White-Oil-Green-Cargo-Khaki-FN6882-100.jpeg',
                    12, 0, 0, 1
                ],
                [
                    'Adidas Samba OG', 'ADIDAS', 130.00, 150.00, 'Classic football-inspired shoes',
                    'https://www.consortium.co.uk/media/catalog/product/cache/1/image/040ec09b1e35df139433887a97daa66f/a/d/adidas-originals-samba-og-maroon-cream-white-gold-metallic-id0477_0006_6.jpg',
                    18, 0, 1, 1
                ],
                [
                    'Puma Speedcat', 'PUMA', 75.00, 0, 'Racing-inspired sneakers',
                    'https://cdn02.plentymarkets.com/y556ywtxgskt/item/images/9559/full/puma--pum-339844-05--20.jpg',
                    25, 0, 0, 1
                ]
            ];

            $sql = "INSERT IGNORE INTO products (name, brand, price, original_price, description, image_url, stock_quantity, featured, on_sale, new_arrival) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($sample_products as $product) {
                $stmt->execute($product);
            }

            // Insert sample orders
            $sql = "INSERT IGNORE INTO orders (user_id, order_number, total_amount, status, shipping_address, payment_method, payment_status, tracking_number) 
                    VALUES 
                    (2, 'STP001', 245.00, 'processing', '123 Main St, Los Angeles, CA 90001', 'credit_card', 'paid', NULL),
                    (2, 'STP002', 180.00, 'shipped', '456 Oak Ave, New York, NY 10001', 'paypal', 'paid', 'TRK123456789'),
                    (2, 'STP003', 95.00, 'delivered', '789 Pine St, Chicago, IL 60601', 'credit_card', 'paid', 'TRK987654321'),
                    (2, 'STP004', 320.00, 'pending', '321 Elm St, Miami, FL 33101', 'credit_card', 'pending', NULL)";
            $this->conn->exec($sql);

            // Insert sample order items
            $order_items = [
                [1, 1, 1, 110.00],
                [1, 2, 2, 60.00],
                [2, 3, 1, 85.00],
                [2, 4, 1, 75.00],
                [3, 5, 1, 150.00],
                [3, 6, 1, 110.00],
                [4, 7, 2, 130.00],
                [4, 8, 1, 75.00]
            ];

            $sql = "INSERT IGNORE INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            
            foreach ($order_items as $item) {
                $stmt->execute($item);
            }
            
            error_log("Demo data inserted successfully");
            
        } catch (Exception $e) {
            error_log("Error inserting demo data: " . $e->getMessage());
        }
    }
}

// Initialize database connection
try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        error_log("Database connection established successfully");
    } else {
        error_log("Database connection failed");
    }
} catch (Exception $e) {
    error_log("Database initialization error: " . $e->getMessage());
    $conn = null;
}
?>