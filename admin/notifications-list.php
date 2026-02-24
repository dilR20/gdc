<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Database.php';

$auth = new Auth();
$auth->requireLogin();

$database = new Database();
$db = $database->getConnection();

$notifications = $database->fetchAll("SELECT * FROM notifications ORDER BY created_at DESC");

$admin = $auth->getAdminInfo();
$adminInfo = $admin;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>Notifications</h1>
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
                    <h2>All Notifications</h2>
                    <a href="notifications-add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Notification
                    </a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($notifications) > 0): ?>
                            <?php foreach ($notifications as $notification): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($notification['title']); ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars($notification['type'] ?? 'General'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($notification['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($notification['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-edit" onclick="location.href='notifications-edit.php?id=<?php echo $notification['id']; ?>'">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn-action btn-delete" onclick="deleteNotification(<?php echo $notification['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    No notifications found. <a href="notifications-add.php">Add one now</a>
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
        function deleteNotification(id) {
            if (confirm('Are you sure you want to delete this notification?')) {
                window.location.href = 'notifications-delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>