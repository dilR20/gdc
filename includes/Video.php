<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Video {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    /**
     * Extract YouTube ID from URL
     */
    public static function extractYoutubeId($url) {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=)([a-zA-Z0-9_-]{11})/',
            '/(?:youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            '/(?:youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/',
            '/(?:youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Get all active videos (for frontend)
     */
    public function getActiveVideos() {
        $query = "SELECT * FROM videos WHERE is_active = 1 ORDER BY display_order ASC";
        return $this->database->fetchAll($query);
    }

    /**
     * Get all videos (for admin)
     */
    public function getAllVideos() {
        $query = "SELECT * FROM videos ORDER BY display_order ASC, created_at DESC";
        return $this->database->fetchAll($query);
    }

    /**
     * Get video by ID
     */
    public function getVideoById($id) {
        $query = "SELECT * FROM videos WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }

    /**
     * Create video
     */
    public function createVideo($data) {
        $youtubeId = self::extractYoutubeId($data['youtube_url']);

        $query = "INSERT INTO videos (title, youtube_url, youtube_id, thumbnail_path, description, display_order, is_active) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['title'],
            $data['youtube_url'],
            $youtubeId,
            $data['thumbnail_path'] ?? null,
            $data['description'] ?? '',
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ];
        return $this->database->execute($query, $params);
    }

    /**
     * Update video
     */
    public function updateVideo($id, $data) {
        $youtubeId = self::extractYoutubeId($data['youtube_url']);

        $query = "UPDATE videos SET title = ?, youtube_url = ?, youtube_id = ?, description = ?, display_order = ?, is_active = ? WHERE id = ?";
        $params = [
            $data['title'],
            $data['youtube_url'],
            $youtubeId,
            $data['description'] ?? '',
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ];

        // Update thumbnail if provided
        if (isset($data['thumbnail_path']) && !empty($data['thumbnail_path'])) {
            $query = "UPDATE videos SET title = ?, youtube_url = ?, youtube_id = ?, thumbnail_path = ?, description = ?, display_order = ?, is_active = ? WHERE id = ?";
            array_splice($params, 3, 0, [$data['thumbnail_path']]);
        }

        return $this->database->execute($query, $params);
    }

    /**
     * Delete video
     */
    public function deleteVideo($id) {
        $video = $this->getVideoById($id);
        if ($video && $video['thumbnail_path']) {
            $filePath = dirname(__DIR__) . '/' . $video['thumbnail_path'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        $query = "DELETE FROM videos WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id) {
        $query = "UPDATE videos SET is_active = NOT is_active WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
}
?>