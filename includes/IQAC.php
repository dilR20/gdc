<?php
/**
 * IQAC Model - Handles IQAC documents and members
 * UPDATED with getLatestByCategory method
 */

require_once __DIR__ . '/Database.php';

class IQAC {
    private $database;
    private $db;
    
    public function __construct() {
        $this->database = new Database();
        $this->db = $this->database->getConnection();
    }
    
    /**
     * Get documents by category
     */
    public function getDocumentsByCategory($category, $activeOnly = true) {
        $query = "SELECT * FROM iqac_documents WHERE category = ?";
        
        if ($activeOnly) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY display_order ASC, created_at DESC";
        
        return $this->database->fetchAll($query, [$category]);
    }
    
    /**
     * Get latest document by category (NEW METHOD)
     * Perfect for Downloads section
     */
    public function getLatestByCategory($category) {
        $query = "SELECT * FROM iqac_documents 
                  WHERE category = ? AND is_active = 1 
                  ORDER BY created_at DESC, id DESC 
                  LIMIT 1";
        
        return $this->database->fetchOne($query, [$category]);
    }
    
    /**
     * Get latest documents for homepage downloads (NEW METHOD)
     * Returns array with latest from each category
     */
    public function getLatestDownloads() {
        $categories = ['prospectus', 'academic', 'quest'];
        $downloads = [];
        
        foreach ($categories as $category) {
            $doc = $this->getLatestByCategory($category);
            if ($doc) {
                $downloads[$category] = $doc;
            }
        }
        
        return $downloads;
    }
    
    /**
     * Get document by ID
     */
    public function getDocumentById($id) {
        $query = "SELECT * FROM iqac_documents WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Create new document
     */
    public function createDocument($data) {
        $query = "INSERT INTO iqac_documents 
                  (category, title, date, year, file_path, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['category'],
            $data['title'],
            $data['date'] ?? null,
            $data['year'] ?? null,
            $data['file_path'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        $stmt = $this->database->execute($query, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update document
     */
    public function updateDocument($id, $data) {
        $query = "UPDATE iqac_documents 
                  SET title = ?, date = ?, year = ?, file_path = ?, 
                      display_order = ?, is_active = ? 
                  WHERE id = ?";
        
        $params = [
            $data['title'],
            $data['date'] ?? null,
            $data['year'] ?? null,
            $data['file_path'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ];
        
        return $this->database->execute($query, $params) !== false;
    }
    
    /**
     * Delete document
     */
    public function deleteDocument($id) {
        // Get file path first to delete file
        $doc = $this->getDocumentById($id);
        if ($doc && !empty($doc['file_path'])) {
            $filePath = __DIR__ . '/../' . $doc['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $query = "DELETE FROM iqac_documents WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
    
    /**
     * Get all IQAC members
     */
    public function getAllMembers($activeOnly = true) {
        $query = "SELECT * FROM iqac_members WHERE 1=1";
        
        if ($activeOnly) {
            $query .= " AND is_active = 1";
        }
        
        $query .= " ORDER BY display_order ASC";
        
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get member by ID
     */
    public function getMemberById($id) {
        $query = "SELECT * FROM iqac_members WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Create new member
     */
    public function createMember($data) {
        $query = "INSERT INTO iqac_members (name, designation, role, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $params = [
            $data['name'],
            $data['designation'],
            $data['role'],
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        $stmt = $this->database->execute($query, $params);
        return $stmt ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update member
     */
    public function updateMember($id, $data) {
        $query = "UPDATE iqac_members 
                  SET name = ?, designation = ?, role = ?, 
                      display_order = ?, is_active = ? 
                  WHERE id = ?";
        
        $params = [
            $data['name'],
            $data['designation'],
            $data['role'],
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ];
        
        return $this->database->execute($query, $params) !== false;
    }
    
    /**
     * Delete member
     */
    public function deleteMember($id) {
        $query = "DELETE FROM iqac_members WHERE id = ?";
        return $this->database->execute($query, [$id]) !== false;
    }
}
?>