<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Department.php';

$auth = new Auth();
$auth->requireLogin();

$dept_id = $_GET['id'] ?? 0;

if ($dept_id > 0) {
    $departmentModel = new Department();
    $dept = $departmentModel->getById($dept_id);
    
    if ($dept) {
        // Check if department has faculty
        $database = new Database();
        $facultyCount = $database->fetchOne(
            "SELECT COUNT(*) as count FROM faculty WHERE department_id = ?", 
            [$dept_id]
        );
        
        if ($facultyCount['count'] > 0) {
            $_SESSION['error_message'] = 'Cannot delete department with existing faculty members!';
        } else {
            if ($departmentModel->delete($dept_id)) {
                $auth->logActivity($auth->getAdminId(), 'DELETE_DEPARTMENT', 'Deleted department: ' . $dept['name']);
                $_SESSION['success_message'] = 'Department deleted successfully.';
            } else {
                $_SESSION['error_message'] = 'Failed to delete department.';
            }
        }
    } else {
        $_SESSION['error_message'] = 'Department not found.';
    }
} else {
    $_SESSION['error_message'] = 'Invalid department ID.';
}

header('Location: departments-list.php');
exit();
?>
