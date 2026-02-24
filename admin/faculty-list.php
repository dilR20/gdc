<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

$auth = new Auth();
$auth->requireLogin();

$database = new Database();
$db = $database->getConnection();

$faculty = $database->fetchAll("SELECT f.*, d.name as dept_name FROM faculty f 
                                 LEFT JOIN departments d ON f.department_id = d.id 
                                 ORDER BY f.name ASC");

$admin = $auth->getAdminInfo();
$adminInfo = $admin;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Management - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Faculty Management</h1>
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
            <div class="admin-table-container">
                <div class="table-header">
                    <h2>All Faculty Members</h2>
                    <a href="faculty-add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Faculty
                    </a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($faculty) > 0): ?>
                            <?php foreach ($faculty as $member): ?>
                            <tr>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($member['photo_path'] ?? 'uploads/faculty/default.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($member['name']); ?>"
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['designation']); ?></td>
                                <td><?php echo htmlspecialchars($member['dept_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="location.href='faculty-edit.php?id=<?php echo $member['id']; ?>'">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteFaculty(<?php echo $member['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    No faculty members found. <a href="faculty-add.php">Add one now</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteFaculty(id) {
            if (confirm('Are you sure you want to delete this faculty member?')) {
                window.location.href = 'faculty-delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>