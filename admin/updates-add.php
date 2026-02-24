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
$update = null;
$error = '';
$success = '';

// Load existing update if editing
if ($isEdit) {
    $update = $announcement->getUpdateById($_GET['edit']);
    if (!$update) {
        header('Location: updates-list.php');
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'link' => trim($_POST['link']),
        'icon' => trim($_POST['icon']),
        'display_order' => (int)$_POST['display_order'],
        'created_by' => $auth->getAdminId()
    ];
    
    if (empty($data['title'])) {
        $error = 'Title is required';
    } else {
        if ($isEdit) {
            if ($announcement->updateUpdate($_GET['edit'], $data)) {
                header('Location: updates-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to update. Please try again.';
            }
        } else {
            if ($announcement->createUpdate($data)) {
                header('Location: updates-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to create update. Please try again.';
            }
        }
    }
}

$iconOptions = [
    'bell' => 'Bell',
    'bullhorn' => 'Bullhorn',
    'info-circle' => 'Info',
    'calendar' => 'Calendar',
    'file-text' => 'Document',
    'graduation-cap' => 'Graduation',
    'exclamation-triangle' => 'Warning',
    'star' => 'Star',
    'bookmark' => 'Bookmark',
    'flag' => 'Flag'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Latest Update - GyanPeeth College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/admin.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="admin-content">
        <div class="admin-topbar">
            <div class="topbar-left">
                <h1>
                    <i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $isEdit ? 'Edit' : 'Add New'; ?> Latest Update
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
                <a href="updates-list.php" class="btn btn-secondary">
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
                                           value="<?php echo $update ? htmlspecialchars($update['title']) : ''; ?>" 
                                           required 
                                           maxlength="500">
                                    <small class="text-muted">This will appear in the scrolling ticker</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="link" class="form-label">Link (Optional)</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="link" 
                                           name="link" 
                                           value="<?php echo $update ? htmlspecialchars($update['link']) : ''; ?>"
                                           placeholder="notices/announcement.pdf">
                                    <small class="text-muted">Relative path or full URL</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon</label>
                                    <select class="form-select" id="icon" name="icon">
                                        <?php foreach ($iconOptions as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" 
                                                <?php echo ($update && $update['icon'] === $value) ? 'selected' : ''; ?>>
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
                                           value="<?php echo $update ? $update['display_order'] : '0'; ?>"
                                           min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Update
                            </button>
                            <a href="updates-list.php" class="btn btn-secondary">
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
                    <div class="alert alert-info">
                        <i class="fas fa-<?php echo $update ? htmlspecialchars($update['icon']) : 'bell'; ?>" id="preview-icon"></i>
                        <strong id="preview-title"><?php echo $update ? htmlspecialchars($update['title']) : 'Your title will appear here'; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Live preview
        document.getElementById('title').addEventListener('input', function() {
            document.getElementById('preview-title').textContent = this.value || 'Your title will appear here';
        });
        
        document.getElementById('icon').addEventListener('change', function() {
            const icon = document.getElementById('preview-icon');
            icon.className = 'fas fa-' + this.value;
        });
    </script>
</body>
</html>