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
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Connection error: " . $e->getMessage());
            // Don't show database errors to users in production
            throw new Exception("Database connection failed.");
        }

        return $this->conn;
    }
}

// Create database and tables if they don't exist
function initializeDatabase() {
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Check if users table exists, if not create it
        $check_table = $conn->query("SHOW TABLES LIKE 'users'");
        if ($check_table->rowCount() == 0) {
            // Create users table
            $sql = "CREATE TABLE users (
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
            )";
            
            $conn->exec($sql);
            
            // Create user_addresses table
            $sql = "CREATE TABLE user_addresses (
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
            )";
            
            $conn->exec($sql);
            
            // Insert a demo admin user
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (first_name, last_name, email, password_hash, role, newsletter, email_verified) 
                    VALUES ('Admin', 'User', 'admin@stepstyle.com', ?, 'admin', 1, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$hashed_password]);
            
            error_log("Database tables created successfully");
        }
        
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
    }
}

// Initialize database on include
initializeDatabase();
?>