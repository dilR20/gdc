<?php
/**
 * Authentication Class
 * 
 * Handles admin authentication and authorization
 */

require_once __DIR__ . '/Database.php';

class Auth {
    private $database;
    private $db;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        $query = "SELECT * FROM admin_users WHERE username = ? AND is_active = 1";
        
        $stmt = $this->database->execute($query, [$username]);
        if (!$stmt) {
            return false;
        }
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['last_activity'] = time();
            
            // Update last login
            $updateQuery = "UPDATE admin_users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
            $this->database->execute($updateQuery, [$user['id']]);
            
            // Log activity
            $this->logActivity($user['id'], 'LOGIN', 'User logged in');
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['admin_id'])) {
            $this->logActivity($_SESSION['admin_id'], 'LOGOUT', 'User logged out');
        }
        
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['last_activity'])) {
            return false;
        }
        
        // Check session timeout
        if (defined('SESSION_TIMEOUT') && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Get current admin ID
     */
    public function getAdminId() {
        return $_SESSION['admin_id'] ?? null;
    }
    
    /**
     * Get current admin info
     */
    public function getAdminInfo() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $query = "SELECT id, username, full_name, email FROM admin_users WHERE id = ?";
        
        return $this->database->fetchOne($query, [$this->getAdminId()]);
    }
    
    /**
     * Generate CSRF token
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Verify CSRF token
     */
    public function verifyCSRFToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * Log admin activity
     */
    public function logActivity($admin_id, $action, $details = null) {
        $query = "INSERT INTO admin_logs (admin_id, action, details, ip_address) 
                  VALUES (?, ?, ?, ?)";
        
        $params = [
            $admin_id,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ];
        
        $this->database->execute($query, $params);
    }
    
    /**
     * Require login (redirect if not logged in)
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
}
?>
