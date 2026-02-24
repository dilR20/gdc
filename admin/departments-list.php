<?php
// session_start();
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Department.php';

$auth = new Auth();
$auth->requireLogin();

$departmentModel = new Department();
$departments = $departmentModel->getAll();
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
    <title>Departments List - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        .hod-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3cd;
            color: #856404;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            border: 1px solid #ffc107;
        }
        .hod-badge i {
            color: #ffc107;
        }
        .no-hod {
            color: #999;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h1>Department Management</h1>
                </div>
                <div class="topbar-right">
                    <a href="departments-add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Department
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
                        <h2>All Departments</h2>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle"></i> 
                            Showing <?php echo count($departments); ?> department(s)
                        </p>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Code</th>
                                <th>Head of Department (HOD)</th>
                                <th>Faculty Count</th>
                                <th>Established</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($departments) > 0): ?>
                                <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <td><?php echo $dept['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($dept['name']); ?></strong>
                                        <?php if (!empty($dept['description'])): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($dept['description'], 0, 60)); ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($dept['code']); ?></span>
                                    </td>
                                    <td>
                                        <?php if (!empty($dept['hod_name']) && !empty($dept['hod_designation'])): ?>
                                        <div class="hod-badge">
                                            <i class="fas fa-user-tie"></i>
                                            <div>
                                                <strong><?php echo htmlspecialchars($dept['hod_name']); ?></strong>
                                                <br>
                                                <small><?php echo htmlspecialchars($dept['hod_designation']); ?></small>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <span class="no-hod">
                                            <i class="fas fa-user-slash"></i> Not assigned
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-users"></i> 
                                            <?php echo $dept['faculty_count']; ?> 
                                            <?php echo $dept['faculty_count'] == 1 ? 'member' : 'members'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo !empty($dept['established_year']) ? $dept['established_year'] : '-'; ?></td>
                                    <td>
                                        <?php if ($dept['is_active']): ?>
                                            <span style="color: green;"><i class="fas fa-check-circle"></i> Active</span>
                                        <?php else: ?>
                                            <span style="color: red;"><i class="fas fa-times-circle"></i> Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-edit" onclick="location.href='departments-edit.php?id=<?php echo $dept['id']; ?>'">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteDepartment(<?php echo $dept['id']; ?>, '<?php echo addslashes($dept['name']); ?>', <?php echo $dept['faculty_count']; ?>)">
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
                                        <p class="text-muted">No departments found. <a href="departments-add.php">Add one now</a></p>
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
        function deleteDepartment(id, name, facultyCount) {
            let message = 'Are you sure you want to delete ' + name + '?';
            
            if (facultyCount > 0) {
                message += '\n\n⚠️ WARNING: This department has ' + facultyCount + ' faculty member(s).\n';
                message += 'Departments with faculty members cannot be deleted!';
                alert(message);
                return;
            }
            
            if (confirm(message)) {
                window.location.href = 'departments-delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>