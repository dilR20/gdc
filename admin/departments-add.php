<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Department.php';

$auth = new Auth();
$auth->requireLogin();

$department = new Department();
$admin = $auth->getAdminInfo();
$adminInfo = $admin; // For sidebar

// Get all active faculty for HOD dropdown
$allFaculty = $department->getAllActiveFaculty();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => trim($_POST['name']),
        'code' => trim($_POST['code']),
        'hod_faculty_id' => !empty($_POST['hod_faculty_id']) ? (int)$_POST['hod_faculty_id'] : null,
        'description' => trim($_POST['description'] ?? ''),
        'established_year' => !empty($_POST['established_year']) ? (int)$_POST['established_year'] : null,
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validation
    if (empty($data['name'])) {
        $error = 'Department name is required';
    } elseif (empty($data['code'])) {
        $error = 'Department code is required';
    } else {
        // Check if code already exists
        $existing = $department->getByCode($data['code']);
        if ($existing) {
            $error = 'Department code already exists';
        } else {
            $deptId = $department->create($data);
            
            if ($deptId) {
                $auth->logActivity($auth->getAdminId(), 'ADD_DEPARTMENT', 'Added department: ' . $data['name']);
                $_SESSION['success_message'] = 'Department added successfully!';
                header('Location: departments-list.php');
                exit();
            } else {
                $error = 'Failed to add department. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Department - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    
    <style>
        .faculty-select-info { 
            background: #e7f3ff; 
            border-left: 4px solid #007bff; 
            padding: 12px; 
            margin-top: 8px;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1><i class="fas fa-building"></i> Add New Department</h1>
            </div>
            <div class="topbar-right">
                <div class="admin-user">
                    <div class="admin-avatar">
                        <?php echo strtoupper(substr($admin['full_name'] ?? 'A', 0, 1)); ?>
                    </div>
                    <div>
                        <div><strong><?php echo htmlspecialchars($admin['full_name'] ?? 'Admin'); ?></strong></div>
                        <small><?php echo htmlspecialchars($admin['email'] ?? ''); ?></small>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <div class="content-area">
            <div class="mb-3">
                <a href="departments-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Department Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required maxlength="200"
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                           placeholder="e.g., Economics, Computer Science">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Department Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="code" name="code" required maxlength="20"
                                           value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>"
                                           placeholder="e.g., ECON, CS" style="text-transform: uppercase;">
                                    <small class="text-muted">Unique code for the department</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="hod_faculty_id" class="form-label">
                                        <i class="fas fa-user-tie"></i> Head of Department (HOD)
                                    </label>
                                    <select class="form-select" id="hod_faculty_id" name="hod_faculty_id">
                                        <option value="">-- Select HOD (Optional) --</option>
                                        <?php foreach ($allFaculty as $faculty): ?>
                                        <option value="<?php echo $faculty['id']; ?>"
                                                <?php echo (isset($_POST['hod_faculty_id']) && $_POST['hod_faculty_id'] == $faculty['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($faculty['name']); ?> 
                                            (<?php echo htmlspecialchars($faculty['designation']); ?>)
                                            <?php if ($faculty['dept_code']): ?>
                                            - [<?php echo htmlspecialchars($faculty['dept_code']); ?>]
                                            <?php endif; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="faculty-select-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Tip:</strong> You can select any active faculty member as HOD. 
                                        If the faculty member you need isn't listed, add them first in the Faculty section.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5><i class="fas fa-file-alt"></i> Additional Details</h5>
                                
                                <div class="mb-3">
                                    <label for="established_year" class="form-label">Established Year</label>
                                    <input type="number" class="form-control" id="established_year" name="established_year" 
                                           value="<?php echo htmlspecialchars($_POST['established_year'] ?? ''); ?>"
                                           min="1900" max="2100" placeholder="e.g., 1995">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"
                                              placeholder="Brief description about the department"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Department</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Uncheck to deactivate this department</small>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Add Department
                            </button>
                            <a href="departments-list.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-uppercase department code
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>