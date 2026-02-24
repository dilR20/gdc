<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Announcement.php';

$auth = new Auth();
$auth->requireLogin();

$announcement = new Announcement();
$admin = $auth->getAdminInfo();
$adminInfo = $admin; // For sidebar

$isEdit = isset($_GET['edit']);
$notification = null;
$error = '';

// Load existing notification if editing
if ($isEdit) {
    $notification = $announcement->getNotificationById($_GET['edit']);
    if (!$notification) {
        header('Location: notifications-list.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'link' => trim($_POST['link']),
        'posted_date' => $_POST['posted_date'],
        'icon' => trim($_POST['icon']),
        'is_important' => isset($_POST['is_important']) ? 1 : 0,
        'display_order' => (int)$_POST['display_order'],
        'created_by' => $auth->getAdminId()
    ];
    
    if (empty($data['title'])) {
        $error = 'Title is required';
    } elseif (empty($data['posted_date'])) {
        $error = 'Posted date is required';
    } else {
        if ($isEdit) {
            if ($announcement->updateNotification($_GET['edit'], $data)) {
                header('Location: notifications-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to update. Please try again.';
            }
        } else {
            if ($announcement->createNotification($data)) {
                header('Location: notifications-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to create notification. Please try again.';
            }
        }
    }
}

$iconOptions = [
    'circle' => 'Circle (Default)',
    'dot-circle' => 'Dot Circle',
    'star' => 'Star',
    'bookmark' => 'Bookmark',
    'bell' => 'Bell',
    'bullhorn' => 'Bullhorn',
    'exclamation-triangle' => 'Warning',
    'info-circle' => 'Info',
    'check-circle' => 'Check',
    'calendar' => 'Calendar'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Notification - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
    
    <style>
        .notification-preview {
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>
                    <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Notification
                </h1>
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
                <a href="notifications-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="title" 
                                           name="title" 
                                           value="<?php echo $notification ? htmlspecialchars($notification['title']) : ''; ?>" 
                                           required 
                                           maxlength="500">
                                    <small class="text-muted">This will appear in the notifications sidebar</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="link" class="form-label">Link (Optional)</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="link" 
                                           name="link" 
                                           value="<?php echo $notification ? htmlspecialchars($notification['link']) : ''; ?>"
                                           placeholder="notices/notification.pdf">
                                    <small class="text-muted">Relative path or full URL</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="posted_date" class="form-label">Posted Date *</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="posted_date" 
                                           name="posted_date" 
                                           value="<?php echo $notification ? $notification['posted_date'] : date('Y-m-d'); ?>" 
                                           required>
                                    <small class="text-muted">Date shown to users</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <select class="form-select" id="icon" name="icon">
                                        <?php foreach ($iconOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" 
                                                <?php echo ($notification && $notification['icon'] === $value) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Icon shown next to title</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="display_order" class="form-label">Display Order</label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="display_order" 
                                           name="display_order" 
                                           value="<?php echo $notification ? $notification['display_order'] : '0'; ?>"
                                           min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_important" 
                                               name="is_important"
                                               <?php echo ($notification && $notification['is_important']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_important">
                                            <i class="fas fa-star text-warning"></i> Mark as Important
                                        </label>
                                    </div>
                                    <small class="text-muted">Shows star icon</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Notification
                            </button>
                            <a href="notifications-list.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-eye"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <div class="notification-preview">
                        <i class="fas fa-<?php echo $notification ? htmlspecialchars($notification['icon']) : 'circle'; ?>" id="preview-icon"></i>
                        <strong id="preview-title"><?php echo $notification ? htmlspecialchars($notification['title']) : 'Your notification will appear here'; ?></strong>
                        <br>
                        <small class="text-muted">Posted on <span id="preview-date"><?php echo $notification ? date('Y-m-d', strtotime($notification['posted_date'])) : date('Y-m-d'); ?></span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('preview-title').textContent = this.value || 'Your notification will appear here';
        });
        
        document.getElementById('posted_date').addEventListener('change', function() {
            document.getElementById('preview-date').textContent = this.value;
        });
        
        document.getElementById('icon').addEventListener('change', function() {
            document.getElementById('preview-icon').className = 'fas fa-' + this.value;
        });
    </script>
</body>
</html>