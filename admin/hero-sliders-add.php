<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/HeroSlider.php';
require_once '../includes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

$heroSlider = new HeroSlider();
$isEdit = isset($_GET['edit']);
$slider = null;
$error = '';

if ($isEdit) {
    $slider = $heroSlider->getSliderById($_GET['edit']);
    if (!$slider) {
        header('Location: hero-sliders-list.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'subtitle' => trim($_POST['subtitle']),
        'link' => trim($_POST['link'] ?? ''),
        'display_order' => (int)$_POST['display_order'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    if (empty($data['title'])) {
        $error = 'Title is required';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
            // Create sliders folder if not exists
            $uploadDir = dirname(__DIR__) . '/uploads/sliders/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileUpload = new FileUpload();
            $uploadResult = $fileUpload->uploadPhoto($_FILES['image'], 'sliders');
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($isEdit && $slider['image_path']) {
                    $oldPath = dirname(__DIR__) . '/' . $slider['image_path'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
                $data['image_path'] = $uploadResult['path'];
            } else {
                $error = 'Image upload failed: ' . $uploadResult['error'];
            }
        } elseif (!$isEdit) {
            $error = 'Image is required';
        }
        
        if (empty($error)) {
            if ($isEdit) {
                if ($heroSlider->updateSlider($_GET['edit'], $data)) {
                    header('Location: hero-sliders-list.php?saved=1');
                    exit();
                } else {
                    $error = 'Failed to update slider';
                }
            } else {
                if ($heroSlider->createSlider($data)) {
                    header('Location: hero-sliders-list.php?saved=1');
                    exit();
                } else {
                    $error = 'Failed to create slider';
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
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Hero Slider</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .preview-image { max-width: 300px; margin-top: 10px; border-radius: 8px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Slider</h1>
                <a href="hero-sliders-list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
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
                                    <label class="form-label">Title *</label>
                                    <input type="text" class="form-control" name="title" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['title']) : ''; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Subtitle</label>
                                    <textarea class="form-control" name="subtitle" rows="2"><?php echo $slider ? htmlspecialchars($slider['subtitle']) : ''; ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Link (Optional)</label>
                                    <input type="text" class="form-control" name="link" 
                                           value="<?php echo $slider ? htmlspecialchars($slider['link']) : ''; ?>" 
                                           placeholder="e.g., admission.php or https://example.com">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Upload Image * <span class="badge bg-info">1920x600px recommended</span></label>
                                    <?php if ($slider && $slider['image_path']): ?>
                                    <div class="mb-2">
                                        <img src="../<?php echo htmlspecialchars($slider['image_path']); ?>" 
                                             class="preview-image" alt="Current Image">
                                        <p class="small text-muted mt-1">Current image (upload new to replace)</p>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="image" accept="image/*" <?php echo !$isEdit ? 'required' : ''; ?>>
                                    <small class="text-muted">Supported: JPG, PNG, GIF - Max 5MB</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" class="form-control" name="display_order" 
                                           value="<?php echo $slider ? $slider['display_order'] : '0'; ?>" min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" 
                                               <?php echo (!$slider || $slider['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Active (Show on website)</label>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <strong><i class="fas fa-lightbulb"></i> Tips:</strong>
                                    <ul class="small mb-0 mt-2">
                                        <li>Use high-quality images (1920x600px)</li>
                                        <li>Keep text short and impactful</li>
                                        <li>Add 3-5 sliders for variety</li>
                                        <li>Set display order to control sequence</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Slider
                            </button>
                            <a href="hero-sliders-list.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>