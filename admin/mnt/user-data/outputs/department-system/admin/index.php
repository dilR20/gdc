<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

$auth = new Auth();
$auth->requireLogin();

// Get statistics
$database = new Database();
$db = $database->getConnection();

// Count departments
$deptCount = $db->query("SELECT COUNT(*) as count FROM departments WHERE is_active = 1")->fetch()['count'];

// Count faculty
$facultyCount = $db->query("SELECT COUNT(*) as count FROM faculty WHERE is_active = 1")->fetch()['count'];

// Count courses
$courseCount = $db->query("SELECT COUNT(*) as count FROM courses WHERE is_active = 1")->fetch()['count'];

// Get recent faculty
$recentFaculty = $database->fetchAll("SELECT f.*, d.name as dept_name FROM faculty f 
                                      LEFT JOIN departments d ON f.department_id = d.id 
                                      ORDER BY f.created_at DESC LIMIT 5");

$admin = $auth->getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BN College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Top Bar -->
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h1>Dashboard</h1>
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
            
            <!-- Content Area -->
            <div class="content-area">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $deptCount; ?></h3>
                            <p>Departments</p>
                        </div>
                        <div class="stat-icon blue">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $facultyCount; ?></h3>
                            <p>Faculty Members</p>
                        </div>
                        <div class="stat-icon green">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-info">
                            <h3><?php echo $courseCount; ?></h3>
                            <p>Courses</p>
                        </div>
                        <div class="stat-icon orange">
                            <i class="fas fa-book"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Faculty Table -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>Recent Faculty</h2>
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
                            <?php if (count($recentFaculty) > 0): ?>
                                <?php foreach ($recentFaculty as $faculty): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($faculty['photo_path'] ?? 'uploads/faculty/default.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($faculty['name']); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($faculty['name']); ?></td>
                                    <td><?php echo htmlspecialchars($faculty['designation']); ?></td>
                                    <td><?php echo htmlspecialchars($faculty['dept_name']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-edit" onclick="location.href='faculty-edit.php?id=<?php echo $faculty['id']; ?>'">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteFaculty(<?php echo $faculty['id']; ?>)">
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
