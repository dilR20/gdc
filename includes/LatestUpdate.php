<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class LatestUpdate {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function getActiveUpdates() {
        $query = "SELECT * FROM latest_updates WHERE is_active = 1 ORDER BY display_order ASC, created_at DESC";
        return $this->database->fetchAll($query);
    }

    public function getAllUpdates() {
        $query = "SELECT * FROM latest_updates ORDER BY display_order ASC, created_at DESC";
        return $this->database->fetchAll($query);
    }

    public function getById($id) {
        $query = "SELECT * FROM latest_updates WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }

    public function handleFileUpload() {
        if (!isset($_FILES['update_file']) || $_FILES['update_file']['error'] === UPLOAD_ERR_INI_SIZE) {
            return null;
        }
        if ($_FILES['update_file']['error'] !== UPLOAD_ERR_OK || empty($_FILES['update_file']['size'])) {
            return null;
        }

        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword',
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                         'application/vnd.ms-excel',
                         'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];

        $fileType = $_FILES['update_file']['type'];
        $ext = strtolower(pathinfo($_FILES['update_file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return ['error' => 'Invalid file type. Allowed: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX'];
        }

        $uploadDir = dirname(__DIR__) . '/uploads/updates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = uniqid('update_') . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['update_file']['tmp_name'], $uploadPath)) {
            return [
                'file_path' => 'uploads/updates/' . $newFileName,
                'file_name' => $_FILES['update_file']['name']
            ];
        }

        return ['error' => 'Upload failed.'];
    }

    public function deleteFile($id) {
        $record = $this->getById($id);
        if ($record && $record['file_path']) {
            $fullPath = dirname(__DIR__) . '/' . $record['file_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    public function create($data) {
        $query = "INSERT INTO latest_updates (title, link, file_path, file_name, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->database->execute($query, [
            $data['title'],
            $data['link'] ?? '',
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data) {
        $query = "UPDATE latest_updates SET title = ?, link = ?, file_path = ?, file_name = ?, display_order = ?, is_active = ? WHERE id = ?";
        return $this->database->execute($query, [
            $data['title'],
            $data['link'] ?? '',
            $data['file_path'] ?? null,
            $data['file_name'] ?? null,
            $data['display_order'] ?? 0,
            $data['is_active'] ?? 1,
            $id
        ]);
    }

    public function delete($id) {
        $this->deleteFile($id);
        $query = "DELETE FROM latest_updates WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }

    public function toggleStatus($id) {
        $query = "UPDATE latest_updates SET is_active = NOT is_active WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }
}
?>