<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $password_hash;
    public $role;
    public $newsletter;
    public $email_verified;
    public $status;
    public $created_at;
    public $updated_at;
    public $last_login;

    public function __construct($db) {
        $this->conn = $db;
    }

    // CREATE - Register new user
    public function register() {
        try {
            // Check if email already exists
            if ($this->emailExists()) {
                return array('success' => false, 'message' => 'Email already exists');
            }

            // Insert query
            $query = "INSERT INTO " . $this->table_name . " 
                     SET first_name=:first_name, last_name=:last_name, email=:email, 
                         phone=:phone, password_hash=:password_hash, newsletter=:newsletter,
                         role='customer', status='active'";

            $stmt = $this->conn->prepare($query);

            // Sanitize and bind parameters
            $this->first_name = htmlspecialchars(strip_tags($this->first_name));
            $this->last_name = htmlspecialchars(strip_tags($this->last_name));
            $this->email = htmlspecialchars(strip_tags($this->email));
            $this->phone = htmlspecialchars(strip_tags($this->phone));
            $this->password_hash = password_hash($this->password_hash, PASSWORD_DEFAULT);

            $stmt->bindParam(":first_name", $this->first_name);
            $stmt->bindParam(":last_name", $this->last_name);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":phone", $this->phone);
            $stmt->bindParam(":password_hash", $this->password_hash);
            $stmt->bindParam(":newsletter", $this->newsletter);

            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return array('success' => true, 'message' => 'User registered successfully', 'user_id' => $this->id);
            }

            return array('success' => false, 'message' => 'Unable to register user');

        } catch (PDOException $e) {
            return array('success' => false, 'message' => 'Database error: ' . $e->getMessage());
        }
    }

    // READ - Get user by email
    public function getUserByEmail($email) {
        try {
            $query = "SELECT id, first_name, last_name, email, phone, password_hash, 
                             role, newsletter, email_verified, status, created_at, 
                             updated_at, last_login 
                      FROM " . $this->table_name . " 
                      WHERE email = :email AND status = 'active' 
                      LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row;
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // READ - Get user by ID
    public function getUserById($id) {
        try {
            $query = "SELECT id, first_name, last_name, email, phone, password_hash, 
                             role, newsletter, email_verified, status, created_at, 
                             updated_at, last_login 
                      FROM " . $this->table_name . " 
                      WHERE id = :id AND status = 'active' 
                      LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row;
            }
            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE - Update last login
    public function updateLastLogin($user_id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET last_login = NOW() 
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $user_id);
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE - Update user profile
    public function updateProfile($user_id, $data) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET first_name = :first_name, last_name = :last_name, 
                          phone = :phone, newsletter = :newsletter, 
                          updated_at = NOW() 
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":first_name", $data['first_name']);
            $stmt->bindParam(":last_name", $data['last_name']);
            $stmt->bindParam(":phone", $data['phone']);
            $stmt->bindParam(":newsletter", $data['newsletter']);
            $stmt->bindParam(":id", $user_id);

            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE - Soft delete user (set status to inactive)
    public function deleteUser($user_id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET status = 'inactive', updated_at = NOW() 
                      WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $user_id);
            return $stmt->execute();

        } catch (PDOException $e) {
            return false;
        }
    }

    // Check if email exists
    private function emailExists() {
        try {
            $query = "SELECT id FROM " . $this->table_name . " 
                      WHERE email = :email 
                      LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $this->email);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            return false;
        }
    }

    // Verify password
    public function verifyPassword($password, $hashed_password) {
        return password_verify($password, $hashed_password);
    }
}
?>