<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Video.php';

$auth = new Auth();
$auth->requireLogin();

$videoModel = new Video();
$isEdit = isset($_GET['edit']);
$video = null;
$error = '';
$youtubePreview = '';

if ($isEdit) {
    $video = $videoModel->getVideoById($_GET['edit']);
    if (!$video) {
        header('Location: videos-list.php');
        exit();
    }
    $youtubePreview = $video['youtube_id'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'youtube_url' => trim($_POST['youtube_url']),
        'description' => trim($_POST['description'] ?? ''),
        'display_order' => (int)$_POST['display_order'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if (empty($data['title'])) {
        $error = 'Title is required.';
    } elseif (empty($data['youtube_url'])) {
        $error = 'YouTube URL is required.';
    } else {
        $youtubeId = Video::extractYoutubeId($data['youtube_url']);
        if (!$youtubeId) {
            $error = 'Invalid YouTube URL. Please enter a valid YouTube link.';
        }
    }

    if (empty($error)) {
        if ($isEdit) {
            if ($videoModel->updateVideo($_GET['edit'], $data)) {
                header('Location: videos-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to update video.';
            }
        } else {
            if ($videoModel->createVideo($data)) {
                header('Location: videos-list.php?saved=1');
                exit();
            } else {
                $error = 'Failed to create video.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Video - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .preview-container {
            background: #1a1a1a;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-placeholder {
            color: #666;
            text-align: center;
        }
        .preview-placeholder i {
            font-size: 40px;
            display: block;
            margin-bottom: 10px;
        }
        .url-hint {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        .url-hint code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Video</h1>
                <a href="videos-list.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Form -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Video Title *</label>
                                    <input type="text" class="form-control" name="title"
                                           value="<?php echo $video ? htmlspecialchars($video['title']) : ''; ?>"
                                           placeholder="e.g. Lecture on Aim in Life..." required>
                                </div>

                                <!-- YouTube URL -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">YouTube URL *</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-danger text-white">
                                            <i class="fab fa-youtube"></i>
                                        </span>
                                        <input type="text" class="form-control" name="youtube_url" id="youtubeUrl"
                                               value="<?php echo $video ? htmlspecialchars($video['youtube_url']) : ''; ?>"
                                               placeholder="https://www.youtube.com/watch?v=XXXXX"
                                               oninput="previewYoutube(this.value)" required>
                                    </div>
                                    <div class="url-hint">
                                        Supported formats:<br>
                                        <code>https://www.youtube.com/watch?v=VIDEO_ID</code><br>
                                        <code>https://youtu.be/VIDEO_ID</code>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea class="form-control" name="description" rows="2"
                                              placeholder="Optional description of the video"><?php echo $video ? htmlspecialchars($video['description']) : ''; ?></textarea>
                                </div>

                                <!-- Display Order & Active -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Display Order</label>
                                            <input type="number" class="form-control" name="display_order"
                                                   value="<?php echo $video ? $video['display_order'] : '0'; ?>" min="0">
                                            <small class="text-muted">Lower numbers appear first</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                       <?php echo (!$video || $video['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Active (Show on website)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Add'; ?> Video
                                    </button>
                                    <a href="videos-list.php" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Preview Sidebar -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white text-center">
                            <i class="fas fa-eye"></i> Live Preview
                        </div>
                        <div class="card-body">
                            <div class="preview-container" id="previewContainer">
                                <?php if ($video && $video['youtube_id']): ?>
                                <img src="https://img.youtube.com/vi/<?php echo htmlspecialchars($video['youtube_id']); ?>/hqdefault.jpg" alt="Preview">
                                <?php else: ?>
                                <div class="preview-placeholder">
                                    <i class="fas fa-youtube text-danger"></i>
                                    <p>Paste YouTube URL<br>to see preview</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3" id="previewInfo">
                                <?php if ($video && $video['youtube_id']): ?>
                                <p class="text-muted small mb-1">
                                    <i class="fab fa-youtube text-danger"></i> 
                                    YouTube ID: <code><?php echo htmlspecialchars($video['youtube_id']); ?></code>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning"></i> Tips</h6>
                            <ul class="small text-muted mb-0">
                                <li>Copy the YouTube URL from the browser address bar</li>
                                <li>Preview updates automatically as you type</li>
                                <li>Set display order to control video sequence</li>
                                <li>Toggle Active/Inactive to hide without deleting</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function extractYoutubeId(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?v=)([a-zA-Z0-9_-]{11})/,
            /(?:youtu\.be\/)([a-zA-Z0-9_-]{11})/,
            /(?:youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/,
            /(?:youtube\.com\/v\/)([a-zA-Z0-9_-]{11})/
        ];
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    function previewYoutube(url) {
        const container = document.getElementById('previewContainer');
        const info = document.getElementById('previewInfo');
        const id = extractYoutubeId(url);

        if (id) {
            container.innerHTML = `<img src="https://img.youtube.com/vi/${id}/hqdefault.jpg" alt="Preview">`;
            info.innerHTML = `<p class="text-muted small mb-1"><i class="fab fa-youtube text-danger"></i> YouTube ID: <code>${id}</code></p>`;
        } else {
            container.innerHTML = `<div class="preview-placeholder"><i class="fas fa-youtube text-danger"></i><p>Invalid URL</p></div>`;
            info.innerHTML = '';
        }
    }
    </script>
</body>
</html>