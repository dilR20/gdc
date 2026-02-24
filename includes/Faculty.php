<?php
/**
 * Faculty Model - Updated with joining/leave dates
 * 
 * Handles all faculty-related database operations
 */

require_once __DIR__ . '/Database.php';

class Faculty {
    private $database;
    private $db;
    private $table = 'faculty';
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
    
    /**
     * Get all faculty by department (only active for public display)
     */
    public function getByDepartment($department_id, $active_only = true) {
        $query = "SELECT * FROM " . $this->table . " WHERE department_id = ?";
        
        if ($active_only) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY display_order ASC, name ASC";
        
        return $this->database->fetchAll($query, [$department_id]);
    }
    
    /**
     * Get faculty by ID with department info
     */
    public function getById($id) {
        $query = "SELECT f.*, d.name as department_name, d.code as department_code 
                  FROM " . $this->table . " f
                  LEFT JOIN departments d ON f.department_id = d.id
                  WHERE f.id = ?";
        
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Get all faculty (for admin - includes inactive)
     */
    public function getAll($active_only = false) {
        $query = "SELECT f.*, d.name as department_name 
                  FROM " . $this->table . " f
                  LEFT JOIN departments d ON f.department_id = d.id";
        
        if ($active_only) {
            $query .= " WHERE f.is_active = 1";
        }
        
        $query .= " ORDER BY f.is_active DESC, d.name ASC, f.display_order ASC";
        
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get active faculty count by department
     */
    public function getCountByDepartment($department_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE department_id = ? AND is_active = 1";
        
        $result = $this->database->fetchOne($query, [$department_id]);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Get total active faculty count
     */
    public function getTotalActiveCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE is_active = 1";
        
        $result = $this->database->fetchOne($query);
        return $result ? $result['count'] : 0;
    }
    
    /**
     * Get retired faculty
     */
    public function getRetired() {
        $query = "SELECT f.*, d.name as department_name 
                  FROM " . $this->table . " f
                  LEFT JOIN departments d ON f.department_id = d.id
                  WHERE f.is_active = 0 AND f.leave_date IS NOT NULL
                  ORDER BY f.leave_date DESC";
        
        return $this->database->fetchAll($query);
    }
    
    /**
     * Create new faculty
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (department_id, name, designation, qualification, email, phone, 
                   photo_path, bio, joining_date, leave_date, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['department_id'],
            $data['name'],
            $data['designation'],
            $data['qualification'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['photo_path'] ?? null,
            $data['bio'] ?? null,
            $data['joining_date'] ?? null,
            $data['leave_date'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        $result = $this->database->execute($query, $params);
        
        if ($result) {
            return $this->database->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Update faculty
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET department_id = ?, name = ?, designation = ?, qualification = ?, 
                      email = ?, phone = ?, bio = ?, joining_date = ?, leave_date = ?, 
                      display_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP";
        
        $params = [
            $data['department_id'],
            $data['name'],
            $data['designation'],
            $data['qualification'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['bio'] ?? null,
            $data['joining_date'] ?? null,
            $data['leave_date'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        // Add photo update if provided
        if (isset($data['photo_path']) && !empty($data['photo_path'])) {
            $query .= ", photo_path = ?";
            $params[] = $data['photo_path'];
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->database->execute($query, $params);
    }
    
    /**
     * Delete faculty (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET is_active = 0 WHERE id = ?";
        
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Permanently delete faculty
     */
    public function permanentDelete($id) {
        // Delete photo file if exists
        $faculty = $this->getById($id);
        if ($faculty && $faculty['photo_path']) {
            $photoPath = dirname(__DIR__) . '/' . $faculty['photo_path'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Mark faculty as retired
     */
    public function markAsRetired($id, $leave_date = null) {
        if (!$leave_date) {
            $leave_date = date('Y-m-d');
        }
        
        $query = "UPDATE " . $this->table . " 
                  SET is_active = 0, leave_date = ? 
                  WHERE id = ?";
        
        return $this->database->execute($query, [$leave_date, $id]);
    }
    
    /**
     * Reactivate faculty
     */
    public function reactivate($id) {
        $query = "UPDATE " . $this->table . " 
                  SET is_active = 1, leave_date = NULL 
                  WHERE id = ?";
        
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Update display order
     */
    public function updateOrder($id, $order) {
        $query = "UPDATE " . $this->table . " SET display_order = ? WHERE id = ?";
        
        return $this->database->execute($query, [$order, $id]);
    }
    
    /**
     * Get faculty statistics
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as retired
                  FROM " . $this->table;
        
        return $this->database->fetchOne($query);
    }
}
?>