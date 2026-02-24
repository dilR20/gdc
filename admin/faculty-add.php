<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Faculty.php';
require_once '../includes/Department.php';
require_once '../includes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

$faculty = new Faculty();
$department = new Department();
$admin = $auth->getAdminInfo();
$adminInfo = $admin; // For sidebar

$departments = $department->getAll();
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'department_id' => (int)$_POST['department_id'],
        'name' => trim($_POST['name']),
        'designation' => trim($_POST['designation']),
        'qualification' => trim($_POST['qualification'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'joining_date' => !empty($_POST['joining_date']) ? $_POST['joining_date'] : null,
        'leave_date' => !empty($_POST['leave_date']) ? $_POST['leave_date'] : null,
        'display_order' => (int)($_POST['display_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validation
    if (empty($data['department_id'])) {
        $error = 'Please select a department';
    } elseif (empty($data['name'])) {
        $error = 'Faculty name is required';
    } elseif (empty($data['designation'])) {
        $error = 'Designation is required';
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->upload($_FILES['photo'], 'faculty');
            
            if ($uploadResult['success']) {
                $data['photo_path'] = $uploadResult['path'];
            } else {
                $error = 'Photo upload failed: ' . $uploadResult['error'];
            }
        }
        
        if (empty($error)) {
            $facultyId = $faculty->create($data);
            
            if ($facultyId) {
                header('Location: faculty-list.php?added=1');
                exit();
            } else {
                $error = 'Failed to add faculty. Please try again.';
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
    <title>Add Faculty - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1><i class="fas fa-user-plus"></i> Add New Faculty</h1>
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
                <a href="faculty-list.php" class="btn btn-secondary">
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
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h5><i class="fas fa-user"></i> Basic Information</h5>
                                
                                <div class="mb-3">
                                    <label for="department_id" class="form-label">Department *</label>
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
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required maxlength="200">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="designation" class="form-label">Designation *</label>
                                    <input type="text" class="form-control" id="designation" name="designation" required maxlength="200"
                                           placeholder="e.g., Associate Professor, Assistant Professor">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="qualification" class="form-label">Qualification</label>
                                    <input type="text" class="form-control" id="qualification" name="qualification" maxlength="500"
                                           placeholder="e.g., Ph.D. in Economics, M.A.">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="200">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" maxlength="50">
                                </div>
                            </div>
                            
                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h5><i class="fas fa-image"></i> Photo & Additional Details</h5>
                                
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    <small class="text-muted">Max size: 5MB. Formats: JPG, PNG, GIF</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio / About</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                                </div>
                                
                                <h5><i class="fas fa-calendar"></i> Employment Details</h5>
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <i class="fas fa-info-circle"></i> <small>These dates are for internal records only and will NOT be shown on the public website</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="joining_date" class="form-label">Joining Date</label>
                                    <input type="date" class="form-control" id="joining_date" name="joining_date">
                                    <small class="text-muted">Date when faculty joined the college</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="leave_date" class="form-label">Leave Date (if retired)</label>
                                    <input type="date" class="form-control" id="leave_date" name="leave_date">
                                    <small class="text-muted">Leave this empty for active faculty</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" class="form-control" id="display_order" name="display_order" value="0" min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active Faculty</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">
                                        Uncheck to mark as retired/inactive (will not show on public website)
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Add Faculty Member
                            </button>
                            <a href="faculty-list.php" class="btn btn-secondary btn-lg">
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
        // Toggle leave date field based on active status
        document.getElementById('is_active').addEventListener('change', function() {
            const leaveDateField = document.getElementById('leave_date');
            if (!this.checked) {
                leaveDateField.value = new Date().toISOString().split('T')[0];
            } else {
                leaveDateField.value = '';
            }
        });
    </script>
</body>
</html>