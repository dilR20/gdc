<?php
/**
 * Department Model - UPDATED WITH HOD FOREIGN KEY
 * Handles all department-related database operations
 */

require_once __DIR__ . '/Database.php';

class Department {
    private $database;
    private $db;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
    
    /**
     * Get all departments with HOD details
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT d.*, 
                  COUNT(DISTINCT f.id) as faculty_count,
                  hod.name as hod_name,
                  hod.designation as hod_designation
                  FROM departments d 
                  LEFT JOIN faculty f ON d.id = f.department_id AND f.is_active = 1
                  LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
                  WHERE 1=1";
        
        if ($activeOnly) {
            $query .= " AND d.is_active = 1";
        }
        
        $query .= " GROUP BY d.id ORDER BY d.name ASC";
        
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get department by ID with HOD details
     */
    public function getById($id) {
        $query = "SELECT d.*, 
                  hod.name as hod_name,
                  hod.designation as hod_designation,
                  hod.photo_path as hod_photo
                  FROM departments d 
                  LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
                  WHERE d.id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Get department by code
     */
    public function getByCode($code) {
        $query = "SELECT d.*, 
                  hod.name as hod_name,
                  hod.designation as hod_designation
                  FROM departments d 
                  LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
                  WHERE d.code = ?";
        return $this->database->fetchOne($query, [$code]);
    }
    
    /**
     * Get faculty members by department (for HOD dropdown)
     */
    public function getFacultyByDepartment($departmentId) {
        $query = "SELECT id, name, designation 
                  FROM faculty 
                  WHERE department_id = ? AND is_active = 1 
                  ORDER BY name ASC";
        return $this->database->fetchAll($query, [$departmentId]);
    }
    
    /**
     * Get all active faculty (for HOD dropdown when no department selected)
     */
    public function getAllActiveFaculty() {
        $query = "SELECT f.id, f.name, f.designation, d.code as dept_code, d.name as dept_name
                  FROM faculty f
                  LEFT JOIN departments d ON f.department_id = d.id
                  WHERE f.is_active = 1 
                  ORDER BY f.name ASC";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Create new department
     */
    public function create($data) {
        $query = "INSERT INTO departments (name, code, hod_faculty_id, description, established_year, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['code'],
            !empty($data['hod_faculty_id']) ? $data['hod_faculty_id'] : null,
            $data['description'] ?? '',
            $data['established_year'] ?? null,
            $data['is_active'] ?? 1
        ];
        
        $stmt = $this->database->execute($query, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update department
     */
    public function update($id, $data) {
        $query = "UPDATE departments 
                  SET name = ?, code = ?, hod_faculty_id = ?, description = ?, 
                      established_year = ?, is_active = ? 
                  WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['code'],
            !empty($data['hod_faculty_id']) ? $data['hod_faculty_id'] : null,
            $data['description'] ?? '',
            $data['established_year'] ?? null,
            $data['is_active'] ?? 1,
            $id
        ];
        
        return $this->database->execute($query, $params) !== false;
    }
    
    /**
     * Delete department (soft delete)
     */
    public function delete($id) {
        // Check if department has faculty
        $facultyCount = $this->database->fetchOne(
            "SELECT COUNT(*) as count FROM faculty WHERE department_id = ?", 
            [$id]
        );
        
        if ($facultyCount['count'] > 0) {
            return false; // Cannot delete department with faculty
        }
        
        $query = "UPDATE departments SET is_active = 0 WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
    
    /**
     * Hard delete department
     */
    public function hardDelete($id) {
        $query = "DELETE FROM departments WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
    
    /**
     * Get courses by department
     */
    public function getCourses($departmentId) {
        $query = "SELECT * FROM courses WHERE department_id = ? AND is_active = 1 ORDER BY course_name";
        return $this->database->fetchAll($query, [$departmentId]);
    }
    
    /**
     * Get results by department
     */
    public function getResults($departmentId, $limit = 2) {
        $query = "SELECT * FROM department_results 
                  WHERE department_id = ? 
                  ORDER BY year DESC 
                  LIMIT ?";
        return $this->database->fetchAll($query, [$departmentId, $limit]);
    }
    
    /**
     * Check if faculty is HOD of any department
     */
    public function isFacultyHOD($facultyId) {
        $query = "SELECT COUNT(*) as count FROM departments WHERE hod_faculty_id = ?";
        $result = $this->database->fetchOne($query, [$facultyId]);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Get departments where faculty is HOD
     */
    public function getDepartmentsByHOD($facultyId) {
        $query = "SELECT * FROM departments WHERE hod_faculty_id = ?";
        return $this->database->fetchAll($query, [$facultyId]);
    }
}
?>