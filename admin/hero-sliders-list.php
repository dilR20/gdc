<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/HeroSlider.php';

$auth = new Auth();
$auth->requireLogin();

$heroSlider = new HeroSlider();
$sliders = $heroSlider->getAllSliders();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if ($auth->verifyCSRFToken($_GET['token'])) {
        $heroSlider->deleteSlider($_GET['delete']);
        header('Location: hero-sliders-list.php?deleted=1');
        exit();
    }
}

// Handle toggle status
if (isset($_GET['toggle']) && isset($_GET['token'])) {
    if ($auth->verifyCSRFToken($_GET['token'])) {
        $heroSlider->toggleStatus($_GET['toggle']);
        header('Location: hero-sliders-list.php?toggled=1');
        exit();
    }
}

$csrfToken = $auth->generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Sliders - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .slider-preview { max-width: 200px; border-radius: 8px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-image"></i> Hero Sliders</h1>
                <a href="hero-sliders-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Slider
                </a>
            </div>
            
            <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Slider saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Slider deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sliders)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No sliders yet. Add your first slider!</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($sliders as $slider): ?>
                                    <tr>
                                        <td>
                                            <img src="../<?php echo htmlspecialchars($slider['image_path']); ?>" 
                                                 class="slider-preview" alt="Slider"
                                                 onerror="this.src='https://via.placeholder.com/200x100?text=No+Image'">
                                        </td>
                                        <td><strong><?php echo htmlspecialchars($slider['title']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($slider['subtitle'], 0, 50)); ?>...</td>
                                        <td><?php echo $slider['display_order']; ?></td>
                                        <td>
                                            <?php if ($slider['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="hero-sliders-add.php?edit=<?php echo $slider['id']; ?>" 
                                                   class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?toggle=<?php echo $slider['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                                   class="btn btn-outline-warning" title="Toggle Status">
                                                    <i class="fas fa-toggle-on"></i>
                                                </a>
                                                <a href="?delete=<?php echo $slider['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                                   class="btn btn-outline-danger" 
                                                   onclick="return confirm('Delete this slider?');" title="Delete">
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