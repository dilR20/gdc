<?php
/**
 * Database Class
 * 
 * Handles all database operations with PDO and prepared statements
 * for security against SQL injection
 */

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    private $conn = null;
    
    /**
     * Get database connection
     */
    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn;
        }
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Connection error: " . $e->getMessage());
            } else {
                error_log("Database connection error: " . $e->getMessage());
                die("Database connection failed. Please try again later.");
            }
        }
        
        return $this->conn;
    }
    
    /**
     * Execute a prepared statement
     */
    public function execute($query, $params = []) {
        try {
            if ($this->conn === null) {
                $this->getConnection();
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                throw $e;
            } else {
                error_log("Query error: " . $e->getMessage());
                return false;
            }
        }
    }
    
    /**
     * Fetch all results
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Fetch single row
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt ? $stmt->fetch() : null;
    }
    
    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        if ($this->conn === null) {
            $this->getConnection();
        }
        return $this->conn->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        if ($this->conn === null) {
            $this->getConnection();
        }
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        if ($this->conn === null) {
            $this->getConnection();
        }
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        if ($this->conn === null) {
            $this->getConnection();
        }
        return $this->conn->rollBack();
    }
}
?>
