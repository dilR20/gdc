<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/LatestUpdate.php';

$auth = new Auth();
$auth->requireLogin();

$model = new LatestUpdate();
$isEdit = isset($_GET['edit']);
$update = null;
$error = '';

if ($isEdit) {
    $update = $model->getById($_GET['edit']);
    if (!$update) {
        header('Location: latest-updates-list.php');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => trim($_POST['title']),
        'link' => trim($_POST['link'] ?? ''),
        'display_order' => (int)$_POST['display_order'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    // Keep existing file by default on edit
    if ($isEdit && $update) {
        $data['file_path'] = $update['file_path'];
        $data['file_name'] = $update['file_name'];
    }

    if (empty($data['title'])) {
        $error = 'Title is required.';
    }

    // Handle remove file checkbox
    if (isset($_POST['remove_file']) && $isEdit) {
        $model->deleteFile($_GET['edit']);
        $data['file_path'] = null;
        $data['file_name'] = null;
    }

    // Handle new file upload
    $fileResult = $model->handleFileUpload();
    if ($fileResult && isset($fileResult['error'])) {
        $error = $fileResult['error'];
    } elseif ($fileResult) {
        // Delete old file if replacing
        if ($isEdit) {
            $model->deleteFile($_GET['edit']);
        }
        $data['file_path'] = $fileResult['file_path'];
        $data['file_name'] = $fileResult['file_name'];
    }

    if (empty($error)) {
        if ($isEdit) {
            $model->update($_GET['edit'], $data);
        } else {
            $model->create($data);
        }
        header('Location: latest-updates-list.php?saved=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Update - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .ticker-preview {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-size: 14px;
            overflow: hidden;
            white-space: nowrap;
        }
        .ticker-preview .ticker-text {
            display: inline-block;
            animation: ticker-scroll 10s linear infinite;
        }
        @keyframes ticker-scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .char-count { font-size: 12px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit' : 'Add'; ?> Update</h1>
                <a href="latest-updates-list.php" class="btn btn-secondary">
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
                        <form method="POST" enctype="multipart/form-data">
                                <!-- Title -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Update Title *</label>
                                    <input type="text" class="form-control" name="title" id="titleInput"
                                           value="<?php echo $update ? htmlspecialchars($update['title']) : ''; ?>"
                                           placeholder="e.g. Admission Notice 2025-26 has been released"
                                           oninput="updatePreview()" required>
                                    <div class="char-count"><span id="charCount">0</span> / 500 characters</div>
                                </div>

                                <!-- Link -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Link <span class="text-muted fw-normal">(optional)</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-link"></i>
                                        </span>
                                        <input type="text" class="form-control" name="link"
                                               value="<?php echo $update ? htmlspecialchars($update['link']) : ''; ?>"
                                               placeholder="https://example.com/notice">
                                    </div>
                                    <small class="text-muted">If provided, the ticker text will be clickable</small>
                                </div>

                                <!-- Order & Status -->

                                <!-- File Upload -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        Attach File <span class="text-muted fw-normal">(optional, max 5MB)</span>
                                    </label>
                                    <?php if ($update && $update['file_path']): ?>
                                    <div class="existing-file mb-2 p-2 bg-light rounded border">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="small">
                                                <i class="fas fa-paperclip text-success me-2"></i>
                                                <strong><?php echo htmlspecialchars($update['file_name']); ?></strong>
                                            </span>
                                            <div>
                                                <a href="../<?php echo htmlspecialchars($update['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_file" id="removeFile">
                                            <label class="form-check-label text-danger small" for="removeFile">
                                                <i class="fas fa-trash"></i> Remove this file
                                            </label>
                                        </div>
                                    </div>
                                    <label class="form-label small text-muted">Or upload a new file to replace:</label>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="update_file"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                                           <?php echo ($update && $update['file_path']) ? 'id="replaceFile"' : ''; ?>>
                                    <small class="text-muted">Allowed: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX</small>
                                </div>

                                <!-- Order & Status -->
                                <!-- <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Display Order</label> -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Display Order</label>
                                            <input type="number" class="form-control" name="display_order"
                                                   value="<?php echo $update ? $update['display_order'] : '0'; ?>" min="0">
                                            <small class="text-muted">Lower numbers appear first in the ticker</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active"
                                                       <?php echo (!$update || $update['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label">Active (Show in ticker)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Add'; ?> Update
                                    </button>
                                    <a href="latest-updates-list.php" class="btn btn-secondary btn-lg">
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
                            <p class="small text-muted mb-2">How it will look in the ticker:</p>
                            <div class="ticker-preview">
                                <span class="ticker-text" id="previewText">
                                    <?php echo $update ? htmlspecialchars($update['title']) : 'Type a title to see preview...'; ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="fw-bold"><i class="fas fa-lightbulb text-warning"></i> Tips</h6>
                            <ul class="small text-muted mb-0">
                                <li>Keep titles concise and clear</li>
                                <li>Add a link if users need to read more</li>
                                <li>Use display order to prioritize important updates</li>
                                <li>Toggle inactive to hide without deleting</li>
                                <li>Multiple updates scroll continuously</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function updatePreview() {
        const title = document.getElementById('titleInput').value;
        document.getElementById('previewText').textContent = title || 'Type a title to see preview...';
        document.getElementById('charCount').textContent = title.length;
    }
    // Initial char count
    updatePreview();
    </script>
</body>
</html>