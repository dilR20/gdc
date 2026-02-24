<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/HomepageCategory.php';
require_once '../includes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

$homepageCategory = new HomepageCategory();
$adminInfo = $auth->getAdminInfo();

$filterCategory = isset($_GET['category']) ? (int)$_GET['category'] : null;
$categories = $homepageCategory->getCategories();
$items = $homepageCategory->getAllItems($filterCategory);

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token']) && $auth->verifyCSRFToken($_GET['token'])) {
    $homepageCategory->deleteItem($_GET['delete']);
    header('Location: homepage-items-list.php?deleted=1');
    exit();
}

// Handle delete file
if (isset($_GET['delete_file']) && isset($_GET['token']) && $auth->verifyCSRFToken($_GET['token'])) {
    $homepageCategory->deleteItemFile($_GET['delete_file']);
    header('Location: homepage-items-list.php?file_deleted=1');
    exit();
}

$csrfToken = $auth->generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Items - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .new-tag { background: #ff0000; color: white; padding: 2px 6px; border-radius: 3px; 
                   font-size: 10px; font-weight: bold; animation: blink 1s infinite; }
        @keyframes blink { 0%, 50%, 100% { opacity: 1; } 25%, 75% { opacity: 0.5; } }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-home"></i> Homepage Items</h1>
                <a href="homepage-items-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Item
                </a>
            </div>
            
            <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Item saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Item deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <!-- Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Filter by Category</label>
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($filterCategory == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <a href="homepage-items-list.php" class="btn btn-secondary d-block">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>File</th>
                                    <th>NEW Tag</th>
                                    <th>Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No items yet. Add your first item!</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($item['category_name']); ?></span></td>
                                        <td>
                                            <?php echo htmlspecialchars(substr($item['title'], 0, 80)); ?>...
                                            <?php if ($item['link']): ?>
                                            <br><small class="text-muted"><i class="fas fa-link"></i> Has link</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $item['date'] ? date('M d, Y', strtotime($item['date'])) : '-'; ?></td>
                                        <td>
                                            <?php if ($item['file_path']): ?>
                                            <a href="../<?php echo htmlspecialchars($item['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-download"></i> <?php echo htmlspecialchars(substr($item['file_name'], 0, 15)); ?>...
                                            </a>
                                            <br><small class="text-muted"><?php echo FileUpload::formatFileSize($item['file_size']); ?></small>
                                            <br>
                                            <a href="?delete_file=<?php echo $item['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                               class="btn btn-sm btn-outline-danger mt-1" onclick="return confirm('Delete file?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php else: ?>
                                            <span class="text-muted">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['show_new']): ?>
                                            <span class="new-tag">ENABLED</span>
                                            <?php if ($item['new_tag_until']): ?>
                                            <br><small class="text-muted">Until: <?php echo date('M d', strtotime($item['new_tag_until'])); ?></small>
                                            <?php endif; ?>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Off</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $item['display_order']; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="homepage-items-add.php?edit=<?php echo $item['id']; ?>" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?php echo $item['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                                   class="btn btn-outline-danger" onclick="return confirm('Delete this item?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>