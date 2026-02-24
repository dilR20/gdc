<?php
/**
 * Department Model - COMPLETE VERSION
 * With getCourses() method for department page
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
     * Generate URL-friendly slug from department code or name
     */
    private function generateSlug($text) {
        $slug = strtolower($text);
        $slug = str_replace(' ', '-', $slug);
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        return $slug;
    }
    
    /**
     * Ensure slug is unique
     */
    private function ensureUniqueSlug($slug, $excludeId = null) {
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $query = "SELECT COUNT(*) as count FROM departments WHERE slug = ?";
            $params = [$slug];
            
            if ($excludeId) {
                $query .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $result = $this->database->fetchOne($query, $params);
            
            if ($result['count'] == 0) {
                return $slug;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }
    
    /**
     * Get all departments with HOD details
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT d.*, 
                  COUNT(DISTINCT f.id) as faculty_count,
                  IFNULL(hod.name, NULL) as hod_name,
                  IFNULL(hod.designation, NULL) as hod_designation,
                  IFNULL(hod.photo_path, NULL) as hod_photo
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
                  IFNULL(hod.name, NULL) as hod_name,
                  IFNULL(hod.designation, NULL) as hod_designation,
                  IFNULL(hod.photo_path, NULL) as hod_photo
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
                  IFNULL(hod.name, NULL) as hod_name,
                  IFNULL(hod.designation, NULL) as hod_designation
                  FROM departments d 
                  LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
                  WHERE d.code = ?";
        return $this->database->fetchOne($query, [$code]);
    }
    
    /**
     * Get department by slug
     */
    public function getBySlug($slug) {
        $query = "SELECT d.*, 
                  IFNULL(hod.name, NULL) as hod_name,
                  IFNULL(hod.designation, NULL) as hod_designation
                  FROM departments d 
                  LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
                  WHERE d.slug = ?";
        return $this->database->fetchOne($query, [$slug]);
    }
    
    /**
     * Get faculty members by department
     */
    public function getFacultyByDepartment($departmentId) {
        $query = "SELECT id, name, designation 
                  FROM faculty 
                  WHERE department_id = ? AND is_active = 1 
                  ORDER BY name ASC";
        return $this->database->fetchAll($query, [$departmentId]);
    }
    
    /**
     * Get all active faculty for HOD dropdown
     */
    public function getAllActiveFaculty() {
        $query = "SELECT f.id, f.name, f.designation, 
                  IFNULL(d.code, '') as dept_code, 
                  IFNULL(d.name, '') as dept_name
                  FROM faculty f
                  LEFT JOIN departments d ON f.department_id = d.id
                  WHERE f.is_active = 1 
                  ORDER BY f.name ASC";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get courses by department (FOR PUBLIC DEPARTMENT PAGE)
     */
    public function getCourses($departmentId) {
        $query = "SELECT * FROM courses 
                  WHERE department_id = ? AND is_active = 1 
                  ORDER BY course_name ASC";
        return $this->database->fetchAll($query, [$departmentId]);
    }
    
    /**
     * Create new department
     */
    public function create($data) {
        $slugSource = !empty($data['code']) ? $data['code'] : $data['name'];
        $slug = $this->generateSlug($slugSource);
        $slug = $this->ensureUniqueSlug($slug);
        
        $query = "INSERT INTO departments (name, code, slug, hod_faculty_id, description, established_year, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['code'],
            $slug,
            !empty($data['hod_faculty_id']) ? (int)$data['hod_faculty_id'] : null,
            $data['description'] ?? '',
            !empty($data['established_year']) ? (int)$data['established_year'] : null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ];
        
        $stmt = $this->database->execute($query, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update department
     */
    public function update($id, $data) {
        $current = $this->getById($id);
        
        $slugSource = !empty($data['code']) ? $data['code'] : $data['name'];
        $newSlug = $this->generateSlug($slugSource);
        
        if ($newSlug !== $current['slug']) {
            $newSlug = $this->ensureUniqueSlug($newSlug, $id);
        } else {
            $newSlug = $current['slug'];
        }
        
        $query = "UPDATE departments 
                  SET name = ?, code = ?, slug = ?, hod_faculty_id = ?, description = ?, 
                      established_year = ?, is_active = ? 
                  WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['code'],
            $newSlug,
            !empty($data['hod_faculty_id']) ? (int)$data['hod_faculty_id'] : null,
            $data['description'] ?? '',
            !empty($data['established_year']) ? (int)$data['established_year'] : null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $id
        ];
        
        return $this->database->execute($query, $params) !== false;
    }
    
    /**
     * Delete department (soft delete)
     */
    public function delete($id) {
        $facultyCount = $this->database->fetchOne(
            "SELECT COUNT(*) as count FROM faculty WHERE department_id = ?", 
            [$id]
        );
        
        if ($facultyCount['count'] > 0) {
            return false;
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
     * Check if faculty is HOD
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