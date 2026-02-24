<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Principal {
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    /**
     * Get current principal
     */
    public function getCurrentPrincipal() {
        $query = "SELECT * FROM principal WHERE is_current = 1 LIMIT 1";
        return $this->database->fetchOne($query);
    }

    /**
     * Get all principals
     */
    // public function getAllPrincipals() {
    //     $query = "SELECT * FROM principal ORDER BY is_current DESC, id DESC";
    //     return $this->database->fetchAll($query);
    // }

    /**
     * Get all principals
     */
    public function getAllPrincipals() {
        $query = "SELECT * FROM principal ORDER BY is_current DESC, id DESC";
        return $this->database->fetchAll($query);
    }

    /**
     * Get all principals (alias for backward compatibility)
     */
    public function getAll() {
        return $this->getAllPrincipals();
    }

    /**
     * Get principal by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM principal WHERE id = ?";
        return $this->database->fetchOne($query, [$id]);
    }

    /**
     * Create new principal
     */
    public function create($data) {
        // If new principal is current, unset all others
        if (isset($data['is_current']) && $data['is_current']) {
            $this->database->execute("UPDATE principal SET is_current = 0");
        }

        $query = "INSERT INTO principal (name, designation, phone, email, photo_path, profile_pdf, message, 
                  joining_date, achievements, is_current, education, teaching_exp, admin_exp, 
                  books_published, books_as_editor, research_projects, publications, pub_books, 
                  pub_conference, programmes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return $this->database->execute($query, [
            $data['name'] ?? '',
            $data['designation'] ?? 'Principal',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['photo_path'] ?? null,
            $data['profile_pdf'] ?? null,
            $data['message'] ?? '',
            $data['joining_date'] ?? null,
            $data['achievements'] ?? null,
            $data['is_current'] ?? 0,
            $data['education'] ?? null,
            $data['teaching_exp'] ?? '',
            $data['admin_exp'] ?? '',
            $data['books_published'] ?? null,
            $data['books_as_editor'] ?? null,
            $data['research_projects'] ?? null,
            $data['publications'] ?? null,
            $data['pub_books'] ?? null,
            $data['pub_conference'] ?? null,
            $data['programmes'] ?? null
        ]);
    }

    /**
     * Update principal
     */
    public function update($id, $data) {
        // If setting as current, unset all others
        if (isset($data['is_current']) && $data['is_current']) {
            $this->database->execute("UPDATE principal SET is_current = 0 WHERE id != ?", [$id]);
        }

        $query = "UPDATE principal SET 
                  name = ?, designation = ?, phone = ?, email = ?, photo_path = ?, profile_pdf = ?, 
                  message = ?, joining_date = ?, achievements = ?, is_current = ?,
                  education = ?, teaching_exp = ?, admin_exp = ?, 
                  books_published = ?, books_as_editor = ?, research_projects = ?, 
                  publications = ?, pub_books = ?, pub_conference = ?, programmes = ?
                  WHERE id = ?";

        return $this->database->execute($query, [
            $data['name'] ?? '',
            $data['designation'] ?? 'Principal',
            $data['phone'] ?? '',
            $data['email'] ?? '',
            $data['photo_path'] ?? null,
            $data['profile_pdf'] ?? null,
            $data['message'] ?? '',
            $data['joining_date'] ?? null,
            $data['achievements'] ?? null,
            $data['is_current'] ?? 0,
            $data['education'] ?? null,
            $data['teaching_exp'] ?? '',
            $data['admin_exp'] ?? '',
            $data['books_published'] ?? null,
            $data['books_as_editor'] ?? null,
            $data['research_projects'] ?? null,
            $data['publications'] ?? null,
            $data['pub_books'] ?? null,
            $data['pub_conference'] ?? null,
            $data['programmes'] ?? null,
            $id
        ]);
    }

    /**
     * Delete principal
     */
    public function delete($id) {
        $principal = $this->getById($id);
        // Delete photo if exists
        if ($principal && $principal['photo_path']) {
            $photoPath = dirname(__DIR__) . '/' . $principal['photo_path'];
            if (file_exists($photoPath)) {
                @unlink($photoPath);
            }
        }
        // Delete profile PDF if exists
        if ($principal && $principal['profile_pdf']) {
            $pdfPath = dirname(__DIR__) . '/' . $principal['profile_pdf'];
            if (file_exists($pdfPath)) {
                @unlink($pdfPath);
            }
        }
        $query = "DELETE FROM principal WHERE id = ?";
        return $this->database->execute($query, [$id]);
    }

    /**
     * Handle photo upload
     */
    public function handlePhotoUpload() {
        if (!isset($_FILES['principal_photo']) || $_FILES['principal_photo']['error'] !== UPLOAD_ERR_OK || empty($_FILES['principal_photo']['size'])) {
            return null;
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['principal_photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return ['error' => 'Invalid photo type. Allowed: JPG, PNG, GIF'];
        }

        if ($_FILES['principal_photo']['size'] > 5 * 1024 * 1024) {
            return ['error' => 'Photo size must be under 5MB'];
        }

        $uploadDir = dirname(__DIR__) . '/uploads/principal/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = 'principal_photo_' . uniqid() . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['principal_photo']['tmp_name'], $uploadPath)) {
            return ['file_path' => 'uploads/principal/' . $newFileName];
        }

        return ['error' => 'Photo upload failed.'];
    }

    /**
     * Handle profile PDF upload
     */
    public function handlePdfUpload() {
        if (!isset($_FILES['principal_pdf']) || $_FILES['principal_pdf']['error'] !== UPLOAD_ERR_OK || empty($_FILES['principal_pdf']['size'])) {
            return null;
        }

        $ext = strtolower(pathinfo($_FILES['principal_pdf']['name'], PATHINFO_EXTENSION));

        if ($ext !== 'pdf') {
            return ['error' => 'Only PDF files are allowed for profile.'];
        }

        if ($_FILES['principal_pdf']['size'] > 10 * 1024 * 1024) {
            return ['error' => 'PDF size must be under 10MB'];
        }

        $uploadDir = dirname(__DIR__) . '/uploads/principal/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = 'principal_pdf_' . uniqid() . '_' . time() . '.pdf';
        $uploadPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['principal_pdf']['tmp_name'], $uploadPath)) {
            return ['file_path' => 'uploads/principal/' . $newFileName];
        }

        return ['error' => 'PDF upload failed.'];
    }

    /**
     * Delete old photo file from disk
     */
    public function deletePhotoFile($id) {
        $principal = $this->getById($id);
        if ($principal && $principal['photo_path']) {
            $fullPath = dirname(__DIR__) . '/' . $principal['photo_path'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    /**
     * Delete old PDF file from disk
     */
    public function deletePdfFile($id) {
        $principal = $this->getById($id);
        if ($principal && $principal['profile_pdf']) {
            $fullPath = dirname(__DIR__) . '/' . $principal['profile_pdf'];
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    // =========================================================
    // JSON FIELD HELPERS (for profile page)
    // =========================================================

    /**
     * Safely decode JSON, returns empty array on failure
     */
    public static function jsonDecode($field) {
        if (empty($field)) return [];
        $data = json_decode($field, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Get education records
     */
    public function getEducation() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['education']) : [];
    }

    /**
     * Get books published
     */
    public function getBooksPublished() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['books_published']) : [];
    }

    /**
     * Get books as editor
     */
    public function getBooksAsEditor() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['books_as_editor']) : [];
    }

    /**
     * Get research projects
     */
    public function getResearchProjects() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['research_projects']) : [];
    }

    /**
     * Get publications - research papers
     */
    public function getPubResearch() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['publications']) : [];
    }

    /**
     * Get publications - books
     */
    public function getPubBooks() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['pub_books']) : [];
    }

    /**
     * Get publications - conference
     */
    public function getPubConference() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['pub_conference']) : [];
    }

    /**
     * Get programmes (OP/RC/STC/FDP)
     */
    public function getProgrammes() {
        $p = $this->getCurrentPrincipal();
        return $p ? self::jsonDecode($p['programmes']) : [];
    }
}
?>