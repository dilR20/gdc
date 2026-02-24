<?php
/**
 * File Upload Handler
 * Handles secure file uploads with validation
 */

class FileUpload {
    private $allowedImageTypes = [
        'image/jpeg',
        'image/jpg', 
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    private $allowedDocTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    
    private $maxImageSize = 5242880; // 5MB in bytes
    private $maxDocSize = 20971520; // 20MB in bytes
    
    /**
     * Upload file (PDF, DOC, etc.)
     * 
     * @param array $file - $_FILES['fieldname']
     * @param string $folder - upload folder (e.g., 'iqac', 'documents')
     * @return array ['success' => bool, 'file_path' => string, 'error' => string]
     */
    public function uploadFile($file, $folder = 'documents') {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => false,
                'file_path' => '',
                'error' => 'No file uploaded'
            ];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'file_path' => '',
                'error' => 'Upload error: ' . $this->getUploadError($file['error'])
            ];
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedDocTypes)) {
            return [
                'success' => false,
                'file_path' => '',
                'error' => 'Invalid file type. Only PDF, DOC, DOCX, XLS, XLSX files are allowed.'
            ];
        }
        
        // Validate file size (20MB max for documents)
        if ($file['size'] > $this->maxDocSize) {
            return [
                'success' => false,
                'file_path' => '',
                'error' => 'File is too large. Maximum size is 20MB.'
            ];
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = dirname(__DIR__) . '/uploads/' . $folder;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return [
                    'success' => false,
                    'file_path' => '',
                    'error' => 'Failed to create upload directory'
                ];
            }
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'file_path' => 'uploads/' . $folder . '/' . $filename,
                'error' => ''
            ];
        } else {
            return [
                'success' => false,
                'file_path' => '',
                'error' => 'Failed to move uploaded file'
            ];
        }
    }
    
    /**
     * Upload photo/image
     * 
     * @param array $file - $_FILES['fieldname']
     * @param string $folder - upload folder (e.g., 'faculty', 'principal')
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    public function upload($file, $folder = 'uploads') {
        return $this->uploadPhoto($file, $folder);
    }
    
    /**
     * Upload photo (for backward compatibility)
     * 
     * @param array $file - $_FILES['fieldname']
     * @param string $folder - upload folder (e.g., 'faculty', 'principal')
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    public function uploadPhoto($file, $folder = 'uploads') {
        // Check if file was uploaded
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'success' => false,
                'path' => '',
                'error' => 'No file uploaded'
            ];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'path' => '',
                'error' => 'Upload error: ' . $this->getUploadError($file['error'])
            ];
        }
        
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedImageTypes)) {
            return [
                'success' => false,
                'path' => '',
                'error' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP images are allowed.'
            ];
        }
        
        // Validate file size
        if ($file['size'] > $this->maxImageSize) {
            return [
                'success' => false,
                'path' => '',
                'error' => 'File is too large. Maximum size is 5MB.'
            ];
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = dirname(__DIR__) . '/uploads/' . $folder;
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                return [
                    'success' => false,
                    'path' => '',
                    'error' => 'Failed to create upload directory'
                ];
            }
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . '/' . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => 'uploads/' . $folder . '/' . $filename,
                'error' => ''
            ];
        } else {
            return [
                'success' => false,
                'path' => '',
                'error' => 'Failed to move uploaded file'
            ];
        }
    }
    
    /**
     * Get upload error message
     */
    private function getUploadError($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                return 'File exceeds upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'File exceeds MAX_FILE_SIZE directive in HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'File was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
    
    /**
     * Delete file
     */
    public function delete($filepath) {
        $fullPath = dirname(__DIR__) . '/' . $filepath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
    
    /**
     * Validate image
     */
    public function validateImage($filepath) {
        $fullPath = dirname(__DIR__) . '/' . $filepath;
        if (!file_exists($fullPath)) {
            return false;
        }
        
        $imageInfo = @getimagesize($fullPath);
        return $imageInfo !== false;
    }
    
    /**
     * Get file info
     */
    public function getFileInfo($filepath) {
        $fullPath = dirname(__DIR__) . '/' . $filepath;
        if (!file_exists($fullPath)) {
            return null;
        }
        
        return [
            'size' => filesize($fullPath),
            'type' => mime_content_type($fullPath),
            'modified' => filemtime($fullPath)
        ];
    }
    
    /**
     * Format file size for display
     */
    public function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
?>