<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Course.php';

$auth = new Auth();
$auth->requireLogin();

$course_id = $_GET['id'] ?? 0;

if ($course_id > 0) {
    $courseModel = new Course();
    $course = $courseModel->getById($course_id);
    
    if ($course) {
        if ($courseModel->delete($course_id)) {
            $auth->logActivity($auth->getAdminId(), 'DELETE_COURSE', 'Deleted course: ' . $course['course_name']);
            $_SESSION['success_message'] = 'Course deleted successfully.';
        } else {
            $_SESSION['error_message'] = 'Failed to delete course.';
        }
    } else {
        $_SESSION['error_message'] = 'Course not found.';
    }
} else {
    $_SESSION['error_message'] = 'Invalid course ID.';
}

header('Location: courses-list.php');
exit();
?>
