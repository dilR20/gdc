<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Video.php';

$auth = new Auth();
$auth->requireLogin();

$video = new Video();
$videos = $video->getAllVideos();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $video->deleteVideo($_GET['id']);
    header('Location: videos-list.php?deleted=1');
    exit();
}

// Handle toggle
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $video->toggleStatus($_GET['id']);
    header('Location: videos-list.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videos - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .video-thumb-preview {
            width: 160px;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
            background: #1a1a1a;
        }
        .yt-badge {
            background: #ff0000;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-video"></i> Video Gallery</h1>
                <a href="videos-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Video
                </a>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Video saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Video deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Thumbnail</th>
                                    <th>Title</th>
                                    <th>YouTube ID</th>
                                    <th>Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($videos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-video fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No videos yet. Add your first video!</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($videos as $index => $v): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php if ($v['youtube_id']): ?>
                                        <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($v['youtube_id']); ?>/hqdefault.jpg" 
                                             class="video-thumb-preview" alt="Thumbnail">
                                        <?php else: ?>
                                        <div class="video-thumb-preview d-flex align-items-center justify-content-center text-muted">
                                            <i class="fas fa-video fa-2x"></i>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($v['title']); ?></strong>
                                        <br>
                                        <span class="yt-badge"><i class="fab fa-youtube"></i> YouTube</span>
                                    </td>
                                    <td>
                                        <code><?php echo htmlspecialchars($v['youtube_id'] ?? 'N/A'); ?></code>
                                    </td>
                                    <td><?php echo $v['display_order']; ?></td>
                                    <td>
                                        <?php if ($v['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="videos-add.php?edit=<?php echo $v['id']; ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle=1&id=<?php echo $v['id']; ?>" 
                                               class="btn btn-outline-warning" title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                            <a href="?delete=1&id=<?php echo $v['id']; ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Delete this video?');" title="Delete">
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