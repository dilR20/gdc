<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Course.php';

$auth = new Auth();
$auth->requireLogin();

$courseModel = new Course();
$courses = $courseModel->getAll();
$adminInfo = $auth->getAdminInfo();

// Handle success/error messages
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses List - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h1>Course Management</h1>
                </div>
                <div class="topbar-right">
                    <a href="courses-add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Course
                    </a>
                </div>
            </div>
            
            <div class="content-area">
                <!-- Success/Error Messages -->
                <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>All Courses</h2>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course Name</th>
                                <th>Course Code</th>
                                <th>Department</th>
                                <th>Semester</th>
                                <th>Seat Capacity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($courses) > 0): ?>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo $course['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($course['course_name']); ?></strong>
                                        <?php if ($course['description']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 50)); ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($course['course_code']): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($course['course_code']); ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($course['department_name']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($course['semester'] ?: '-'); ?></td>
                                    <td>
                                        <?php if ($course['seat_capacity']): ?>
                                        <span class="badge bg-info"><?php echo $course['seat_capacity']; ?> seats</span>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($course['is_active']): ?>
                                            <span style="color: green;"><i class="fas fa-check-circle"></i> Active</span>
                                        <?php else: ?>
                                            <span style="color: red;"><i class="fas fa-times-circle"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-edit" onclick="location.href='courses-edit.php?id=<?php echo $course['id']; ?>'">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteCourse(<?php echo $course['id']; ?>, '<?php echo addslashes($course['course_name']); ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No courses found. <a href="courses-add.php">Add one now</a></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCourse(id, name) {
            if (confirm('Are you sure you want to delete ' + name + '?')) {
                window.location.href = 'courses-delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>
