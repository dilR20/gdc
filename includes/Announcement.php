<?php
/**
 * Announcement Model
 * Handles Latest Updates and Notifications
 */

// IMPORTANT: Load config first!
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Announcement {
    private $database;
    
    public function __construct() {
        $this->database = new Database();
    }
    
    /**
     * Get all latest updates with NEW tag logic
     */
    public function getLatestUpdates($limit = 10, $active_only = true) {
        $query = "SELECT *, 
                  CASE 
                    WHEN show_new_tag = 1 AND (new_tag_until IS NULL OR new_tag_until >= CURDATE()) 
                    THEN 1 
                    ELSE 0 
                  END as show_new 
                  FROM latest_updates";
        
        if ($active_only) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY display_order ASC, created_at DESC LIMIT ?";
        
        $stmt = $this->database->execute($query, [$limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Get all notifications with NEW tag logic
     */
    public function getNotifications($limit = 10, $active_only = true) {
        $query = "SELECT *, 
                  CASE 
                    WHEN show_new_tag = 1 AND (new_tag_until IS NULL OR new_tag_until >= CURDATE()) 
                    THEN 1 
                    ELSE 0 
                  END as show_new 
                  FROM notifications";
        
        if ($active_only) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY display_order ASC, posted_date DESC LIMIT ?";
        
        $stmt = $this->database->execute($query, [$limit]);
        return $stmt ? $stmt->fetchAll() : [];
    }
    
    /**
     * Get update by ID
     */
    public function getUpdateById($id) {
        $query = "SELECT * FROM latest_updates WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Get notification by ID
     */
    public function getNotificationById($id) {
        $query = "SELECT * FROM notifications WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }
    
    /**
     * Create latest update
     */
    public function createUpdate($data) {
        $query = "INSERT INTO latest_updates 
                  (title, link, file_path, file_name, file_size, icon, display_order, 
                   show_new_tag, new_tag_until, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['link'] ?? null,
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['file_size'] ?? null,
            $data['icon'] ?? 'bell',
            $data['display_order'] ?? 0,
            $data['show_new_tag'] ?? 1,
            $data['new_tag_until'] ?? null,
            $data['created_by'] ?? null
        ];
        
        $result = $this->database->execute($query, $params);
        return $result ? $this->database->lastInsertId() : false;
    }
    
    /**
     * Create notification
     */
    public function createNotification($data) {
        $query = "INSERT INTO notifications 
                  (title, link, file_path, file_name, file_size, posted_date, icon, 
                   is_important, display_order, show_new_tag, new_tag_until, created_by) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['title'],
            $data['link'] ?? null,
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['file_size'] ?? null,
            $data['posted_date'],
            $data['icon'] ?? 'circle',
            $data['is_important'] ?? 0,
            $data['display_order'] ?? 0,
            $data['show_new_tag'] ?? 1,
            $data['new_tag_until'] ?? null,
            $data['created_by'] ?? null
        ];
        
        $result = $this->database->execute($query, $params);
        return $result ? $this->database->lastInsertId() : false;
    }
    
    /**
     * Update latest update
     */
    public function updateUpdate($id, $data) {
        $query = "UPDATE latest_updates 
                  SET title = ?, link = ?, icon = ?, display_order = ?, 
                      show_new_tag = ?, new_tag_until = ?";
        
        $params = [
            $data['title'],
            $data['link'] ?? null,
            $data['icon'] ?? 'bell',
            $data['display_order'] ?? 0,
            $data['show_new_tag'] ?? 0,
            $data['new_tag_until'] ?? null
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
     * Update notification
     */
    public function updateNotification($id, $data) {
        $query = "UPDATE notifications 
                  SET title = ?, link = ?, posted_date = ?, icon = ?, 
                      is_important = ?, display_order = ?, show_new_tag = ?, new_tag_until = ?";
        
        $params = [
            $data['title'],
            $data['link'] ?? null,
            $data['posted_date'],
            $data['icon'] ?? 'circle',
            $data['is_important'] ?? 0,
            $data['display_order'] ?? 0,
            $data['show_new_tag'] ?? 0,
            $data['new_tag_until'] ?? null
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
     * Delete update file
     */
    public function deleteUpdateFile($id) {
        $update = $this->getUpdateById($id);
        if ($update && $update['file_path']) {
            $filePath = dirname(__DIR__) . '/' . $update['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "UPDATE latest_updates SET file_path = NULL, file_name = NULL, file_size = NULL WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Delete notification file
     */
    public function deleteNotificationFile($id) {
        $notification = $this->getNotificationById($id);
        if ($notification && $notification['file_path']) {
            $filePath = dirname(__DIR__) . '/' . $notification['file_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        $query = "UPDATE notifications SET file_path = NULL, file_name = NULL, file_size = NULL WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Delete update
     */
    public function deleteUpdate($id) {
        $this->deleteUpdateFile($id);
        $query = "UPDATE latest_updates SET is_active = 0 WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Delete notification
     */
    public function deleteNotification($id) {
        $this->deleteNotificationFile($id);
        $query = "UPDATE notifications SET is_active = 0 WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Toggle active status
     */
    public function toggleUpdateStatus($id) {
        $query = "UPDATE latest_updates SET is_active = NOT is_active WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
    
    /**
     * Toggle notification status
     */
    public function toggleNotificationStatus($id) {
        $query = "UPDATE notifications SET is_active = NOT is_active WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
}
?>