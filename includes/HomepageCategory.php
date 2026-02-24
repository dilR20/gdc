<?php
/**
 * Homepage Category Model
 * Manages Examination Notices, Notices, Tenders, Upcoming Events
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class HomepageCategory {
    private $database;
    
    public function __construct() {
        $this->database = new Database();
    }
    
    /**
     * Get all active categories
     */
    public function getCategories() {
        $query = "SELECT * FROM homepage_categories 
                  WHERE is_active = 1 
                  ORDER BY display_order ASC";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        $query = "SELECT * FROM homepage_categories WHERE slug = ? AND is_active = 1";
        return $this->database->fetchOne($query, [$slug]);
    }
    
    /**
     * Get items by category (with NEW tag logic)
     */
    public function getItemsByCategory($category_id, $limit = 5) {
        $query = "SELECT *, 
                  CASE 
                    WHEN show_new_tag = 1 AND (new_tag_until IS NULL OR new_tag_until >= CURDATE()) 
                    THEN 1 
                    ELSE 0 
                  END as show_new 
                  FROM homepage_items 
                  WHERE category_id = ? AND is_active = 1 
                  ORDER BY display_order ASC, created_at DESC 
                  LIMIT ?";
        
        $stmt = $this->database->execute($query, [$category_id, $limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Get all items for admin
     */
    public function getAllItems($category_id = null) {
        $query = "SELECT i.*, c.name as category_name, c.slug as category_slug,
                  CASE 
                    WHEN i.show_new_tag = 1 AND (i.new_tag_until IS NULL OR i.new_tag_until >= CURDATE()) 
                    THEN 1 
                    ELSE 0 
                  END as show_new
                  FROM homepage_items i
                  JOIN homepage_categories c ON i.category_id = c.id";
        
        $params = [];
        if ($category_id) {
            $query .= " WHERE i.category_id = ?";
            $params[] = $category_id;
        }
        
        $query .= " ORDER BY c.display_order, i.display_order ASC, i.created_at DESC";
        
        return $this->database->fetchAll($query, $params);
    }
    
    /**
     * Get item by ID
     */
    public function getItemById($id) {
        $query = "SELECT i.*, c.name as category_name 
                  FROM homepage_items i
                  JOIN homepage_categories c ON i.category_id = c.id
                  WHERE i.id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Create item
     */
    public function createItem($data) {
        $query = "INSERT INTO homepage_items 
                  (category_id, title, date, link, file_path, file_name, file_size, 
                   show_new_tag, new_tag_until, display_order, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['category_id'],
            $data['title'],
            $data['date'] ?? null,
            $data['link'] ?? null,
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['file_size'] ?? null,
            $data['show_new_tag'] ?? 1,
            $data['new_tag_until'] ?? null,
            $data['display_order'] ?? 0,
            $data['created_by'] ?? null
        ];
        
        $result = $this->database->execute($query, $params);
        return $result ? $this->database->lastInsertId() : false;
    }
    
    /**
     * Update item
     */
    public function updateItem($id, $data) {
        $query = "UPDATE homepage_items 
                  SET category_id = ?, title = ?, date = ?, link = ?, 
                      show_new_tag = ?, new_tag_until = ?, display_order = ?";
        
        $params = [
            $data['category_id'],
            $data['title'],
            $data['date'] ?? null,
            $data['link'] ?? null,
            $data['show_new_tag'] ?? 0,
            $data['new_tag_until'] ?? null,
            $data['display_order'] ?? 0
        ];
        
        if (isset($data['file_path']) && !empty($data['file_path'])) {
            $query .= ", file_path = ?, file_name = ?, file_size = ?";
            $params[] = $data['file_path'];
            $params[] = $data['file_name'];
            $params[] = $data['file_size'];
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->database->execute($query, $params);
    }
    
    /**
     * Delete item
     */
    public function deleteItem($id) {
        $item = $this->getItemById($id);
        if ($item && $item['file_path']) {
            $filePath = dirname(__DIR__) . '/' . $item['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "DELETE FROM homepage_items WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Delete item file only
     */
    public function deleteItemFile($id) {
        $item = $this->getItemById($id);
        if ($item && $item['file_path']) {
            $filePath = dirname(__DIR__) . '/' . $item['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "UPDATE homepage_items 
                  SET file_path = NULL, file_name = NULL, file_size = NULL 
                  WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
}
?>