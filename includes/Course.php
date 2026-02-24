<?php
/**
 * Course Model
 * Handles all course-related database operations
 */

require_once __DIR__ . '/Database.php';

class Course {
    private $database;
    private $db;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
    
    /**
     * Get all courses
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT c.*, d.name as department_name, d.code as department_code 
                  FROM courses c 
                  LEFT JOIN departments d ON c.department_id = d.id 
                  WHERE 1=1";
        
        if ($activeOnly) {
            $query .= " AND c.is_active = 1";
        }
        
        $query .= " ORDER BY d.name, c.course_name";
        
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get course by ID
     */
    public function getById($id) {
        $query = "SELECT c.*, d.name as department_name 
                  FROM courses c 
                  LEFT JOIN departments d ON c.department_id = d.id 
                  WHERE c.id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Get courses by department
     */
    public function getByDepartment($departmentId, $activeOnly = true) {
        $query = "SELECT * FROM courses WHERE department_id = ?";
        
        if ($activeOnly) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY semester, course_name";
        
        return $this->database->fetchAll($query, [$departmentId]);
    }
    
    /**
     * Create new course
     */
    public function create($data) {
        $query = "INSERT INTO courses (department_id, course_name, course_code, semester, 
                  seat_capacity, description, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['department_id'],
            $data['course_name'],
            $data['course_code'] ?? '',
            $data['semester'] ?? '',
            $data['seat_capacity'] ?? null,
            $data['description'] ?? '',
            $data['is_active'] ?? 1
        ];
        
        $stmt = $this->database->execute($query, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update course
     */
    public function update($id, $data) {
        $query = "UPDATE courses 
                  SET department_id = ?, course_name = ?, course_code = ?, semester = ?, 
                      seat_capacity = ?, description = ?, is_active = ? 
                  WHERE id = ?";
        
        $params = [
            $data['department_id'],
            $data['course_name'],
            $data['course_code'] ?? '',
            $data['semester'] ?? '',
            $data['seat_capacity'] ?? null,
            $data['description'] ?? '',
            $data['is_active'] ?? 1,
            $id
        ];
        
        return $this->database->execute($query, $params) !== false;
    }
    
    /**
     * Delete course (soft delete)
     */
    public function delete($id) {
        $query = "UPDATE courses SET is_active = 0 WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
    
    /**
     * Hard delete course
     */
    public function hardDelete($id) {
        $query = "DELETE FROM courses WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
}
?>