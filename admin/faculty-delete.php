<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Faculty.php';

$auth = new Auth();
$auth->requireLogin();

$faculty_id = $_GET['id'] ?? 0;

if ($faculty_id > 0) {
    $facultyModel = new Faculty();
    $faculty = $facultyModel->getById($faculty_id);
    
    if ($faculty) {
        // Soft delete (set is_active = 0)
        if ($facultyModel->delete($faculty_id)) {
            $auth->logActivity($auth->getAdminId(), 'DELETE_FACULTY', 'Deleted faculty: ' . $faculty['name']);
            
            $_SESSION['success_message'] = 'Faculty member deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete faculty member.';
        }
    } else {
        $_SESSION['error_message'] = 'Faculty member not found.';
    }
} else {
    $_SESSION['error_message'] = 'Invalid faculty ID.';
}

header('Location: faculty-list.php');
exit();
?>
