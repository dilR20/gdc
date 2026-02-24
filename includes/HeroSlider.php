<?php
/**
 * Hero Slider Model
 * Manages homepage hero sliders
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class HeroSlider {
    private $database;
    
    public function __construct() {
        $this->database = new Database();
    }
    
    /**
     * Get all active sliders
     */
    public function getActiveSliders() {
        $query = "SELECT * FROM hero_sliders 
                  WHERE is_active = 1 
                  ORDER BY display_order ASC";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get all sliders (for admin)
     */
    public function getAllSliders() {
        $query = "SELECT * FROM hero_sliders ORDER BY display_order ASC, created_at DESC";
        return $this->database->fetchAll($query);
    }
    
    /**
     * Get slider by ID
     */
    public function getSliderById($id) {
        $query = "SELECT * FROM hero_sliders WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Create slider
     */
    public function createSlider($data) {
        $query = "INSERT INTO hero_sliders (title, subtitle, image_path, link, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? '',
            $data['image_path'],
            $data['link'] ?? '',
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        $result = $this->database->execute($query, $params);
        return $result ? $this->database->lastInsertId() : false;
    }
    
    /**
     * Update slider
     */
    public function updateSlider($id, $data) {
        $query = "UPDATE hero_sliders 
                  SET title = ?, subtitle = ?, link = ?, display_order = ?, is_active = ?";
        
        $params = [
            $data['title'],
            $data['subtitle'] ?? '',
            $data['link'] ?? '',
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        
        // Update image if provided
        if (isset($data['image_path']) && !empty($data['image_path'])) {
            $query .= ", image_path = ?";
            $params[] = $data['image_path'];
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->database->execute($query, $params);
    }
    
    /**
     * Delete slider
     */
    public function deleteSlider($id) {
        // Delete image file
        $slider = $this->getSliderById($id);
        if ($slider && $slider['image_path']) {
            $filePath = dirname(__DIR__) . '/' . $slider['image_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "DELETE FROM hero_sliders WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Toggle active status
     */
    public function toggleStatus($id) {
        $query = "UPDATE hero_sliders SET is_active = NOT is_active WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
}
?>