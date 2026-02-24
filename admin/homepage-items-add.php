<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/HomepageCategory.php';
require_once '../includes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

$homepageCategory = new HomepageCategory();
$adminInfo = $auth->getAdminInfo();

$isEdit = isset($_GET['edit']);
$item = null;
$error = '';

if ($isEdit) {
    $item = $homepageCategory->getItemById($_GET['edit']);
    if (!$item) {
        header('Location: homepage-items-list.php');
        exit();
    }
}

$categories = $homepageCategory->getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => (int)$_POST['category_id'],
        'title' => trim($_POST['title']),
        'date' => !empty($_POST['date']) ? $_POST['date'] : null,
        'link' => trim($_POST['link'] ?? ''),
        'show_new_tag' => isset($_POST['show_new_tag']) ? 1 : 0,
        'new_tag_until' => !empty($_POST['new_tag_until']) ? $_POST['new_tag_until'] : null,
        'display_order' => (int)$_POST['display_order'],
        'created_by' => $auth->getAdminId()
    ];
    
    if (empty($data['category_id'])) {
        $error = 'Please select a category';
    } elseif (empty($data['title'])) {
        $error = 'Title is required';
    } else {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->uploadDocument($_FILES['file'], 'homepage');
            
            if ($uploadResult['success']) {
                if ($isEdit && $item['file_path']) {
                    $fileUpload->delete($item['file_path']);
                }
                $data['file_path'] = $uploadResult['path'];
                $data['file_name'] = $uploadResult['name'];
                $data['file_size'] = $uploadResult['size'];
                $data['link'] = $uploadResult['path'];
            } else {
                $error = 'File upload failed: ' . $uploadResult['error'];
            }
        }
        
        if (empty($error)) {
            if ($isEdit) {
                if ($homepageCategory->updateItem($_GET['edit'], $data)) {
                    header('Location: homepage-items-list.php?saved=1');
                    exit();
                } else {
                    $error = 'Failed to update item';
                }
            } else {
                if ($homepageCategory->createItem($data)) {
                    header('Location: homepage-items-list.php?saved=1');
                    exit();
                } else {
                    $error = 'Failed to create item';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Homepage Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .new-tag-demo { background: #ff0000; color: white; padding: 2px 8px; border-radius: 3px; 
                        font-size: 11px; font-weight: bold; animation: blink 1s infinite; margin-left: 10px; }
        @keyframes blink { 0%, 50%, 100% { opacity: 1; } 25%, 75% { opacity: 0.5; } }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Item</h1>
                <a href="homepage-items-list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Category *</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($item && $item['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Title *</label>
                                    <textarea class="form-control" name="title" rows="2" required><?php echo $item ? htmlspecialchars($item['title']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date (Optional)</label>
                                    <input type="date" class="form-control" name="date" value="<?php echo $item ? $item['date'] : ''; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Upload File <span class="badge bg-info">PDF, DOC, XLS - Max 10MB</span></label>
                                    <?php if ($item && $item['file_path']): ?>
                                    <div class="alert alert-info py-2 mb-2">
                                        <i class="fas fa-file"></i> Current: <a href="../<?php echo htmlspecialchars($item['file_path']); ?>" target="_blank"><?php echo htmlspecialchars($item['file_name']); ?></a>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">OR Manual Link</label>
                                    <input type="text" class="form-control" name="link" value="<?php echo $item ? htmlspecialchars($item['link']) : ''; ?>" placeholder="notices/file.pdf">
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" class="form-control" name="display_order" value="<?php echo $item ? $item['display_order'] : '0'; ?>" min="0">
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="show_new_tag" <?php echo (!$item || $item['show_new_tag']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label"><strong>Show NEW Tag</strong><span class="new-tag-demo">NEW</span></label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Show NEW Until (Optional)</label>
                                    <input type="date" class="form-control" name="new_tag_until" value="<?php echo $item ? $item['new_tag_until'] : ''; ?>">
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <strong><i class="fas fa-info-circle"></i> How it works:</strong>
                                    <ul class="small mb-0 mt-2">
                                        <li>Choose category</li>
                                        <li>Enter title</li>
                                        <li>Upload file OR paste link</li>
                                        <li>NEW tag attracts attention</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?></button>
                            <a href="homepage-items-list.php" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>