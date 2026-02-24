<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Course.php';
require_once '../includes/Department.php';

$auth = new Auth();
$auth->requireLogin();

$course = new Course();
$department = new Department();
$adminInfo = $auth->getAdminInfo();

$departments = $department->getAll(true); // Only active departments
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'department_id' => (int)$_POST['department_id'],
        'course_name' => trim($_POST['course_name']),
        'course_code' => trim($_POST['course_code'] ?? ''),
        'semester' => trim($_POST['semester'] ?? ''),
        'seat_capacity' => !empty($_POST['seat_capacity']) ? (int)$_POST['seat_capacity'] : null,
        'description' => trim($_POST['description'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validation
    if (empty($data['department_id'])) {
        $error = 'Please select a department';
    } elseif (empty($data['course_name'])) {
        $error = 'Course name is required';
    } else {
        $courseId = $course->create($data);
        
        if ($courseId) {
            $auth->logActivity($auth->getAdminId(), 'ADD_COURSE', 'Added course: ' . $data['course_name']);
            $_SESSION['success_message'] = 'Course added successfully!';
            header('Location: courses-list.php');
            exit();
        } else {
            $error = 'Failed to add course. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
            background: #f5f5f5;
        }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card-body { padding: 30px; }
        .form-label { font-weight: 600; color: #333; margin-bottom: 8px; display: block; }
        .form-control, .form-select { border: 1px solid #ddd; border-radius: 4px; padding: 10px 15px; font-size: 14px; width: 100%; }
        .form-control:focus, .form-select:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25); }
        h5 { color: #333; font-weight: 600; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007bff; }
        @media (max-width: 768px) { .main-content { margin-left: 0; padding: 15px; } }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-book"></i> Add New Course</h1>
                <a href="courses-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (empty($departments)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> No departments available. Please <a href="departments-add.php">add a department</a> first.
            </div>
            <?php else: ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department_id" name="department_id" required>
                                        <option value="">Select Department</option>
                                        <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>">
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="course_name" class="form-label">Course Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="course_name" name="course_name" required maxlength="200"
                                           placeholder="e.g., Microeconomics, Data Structures">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="course_code" class="form-label">Course Code</label>
                                    <input type="text" class="form-control" id="course_code" name="course_code" maxlength="50"
                                           placeholder="e.g., ECON101, CS201" style="text-transform: uppercase;">
                                    <small class="text-muted">Unique identifier for the course</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="fas fa-file-alt"></i> Additional Details</h5>
                                
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester/Year</label>
                                    <input type="text" class="form-control" id="semester" name="semester" maxlength="50"
                                           placeholder="e.g., 1st Semester, 2nd Year">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="seat_capacity" class="form-label">Seat Capacity</label>
                                    <input type="number" class="form-control" id="seat_capacity" name="seat_capacity" 
                                           min="1" max="1000" placeholder="e.g., 60">
                                    <small class="text-muted">Number of available seats</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"
                                              placeholder="Brief description about the course"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Course</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Uncheck to deactivate this course</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Add Course
                            </button>
                            <a href="courses-list.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-uppercase course code
        document.getElementById('course_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
