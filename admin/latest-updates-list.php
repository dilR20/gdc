<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/LatestUpdate.php';

$auth = new Auth();
$auth->requireLogin();

$model = new LatestUpdate();

// Handle delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $model->delete($_GET['id']);
    header('Location: latest-updates-list.php?deleted=1');
    exit();
}

// Handle toggle
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $model->toggleStatus($_GET['id']);
    header('Location: latest-updates-list.php');
    exit();
}

// Handle inline add
// Handle inline add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quick_add'])) {
    if (!empty(trim($_POST['title']))) {
        $data = [
            'title' => trim($_POST['title']),
            'link' => trim($_POST['link'] ?? ''),
            'display_order' => (int)$_POST['display_order'],
            'is_active' => 1
        ];

        $fileResult = $model->handleFileUpload();
        if ($fileResult && isset($fileResult['error'])) {
            $error = $fileResult['error'];
        } else {
            if ($fileResult) {
                $data['file_path'] = $fileResult['file_path'];
                $data['file_name'] = $fileResult['file_name'];
            }
            $model->create($data);
            header('Location: latest-updates-list.php?saved=1');
            exit();
        }
    }
}

$updates = $model->getAllUpdates();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Updates - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <style>
        .main-content { margin-left: 260px; padding: 30px; }
        .ticker-preview {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            overflow: hidden;
            white-space: nowrap;
            margin-bottom: 20px;
        }
        .ticker-preview .ticker-text {
            display: inline-block;
            animation: ticker-scroll 15s linear infinite;
        }
        @keyframes ticker-scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
        .quick-add-row {
            background: #f0fff4;
            border: 2px dashed #28a745;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .order-badge {
            background: #e9ecef;
            color: #495057;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .drag-handle {
            cursor: grab;
            color: #aaa;
            padding: 0 8px;
        }
        .drag-handle:hover { color: #666; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-bullhorn"></i> Latest Updates (Ticker)</h1>
                <a href="latest-updates-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Update
                </a>
            </div>

            <!-- Alerts -->
            <?php if (isset($_GET['saved'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Update saved successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Update deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Live Preview -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-eye"></i> Live Ticker Preview
                </div>
                <div class="card-body">
                    <div class="ticker-preview">
                        <span class="ticker-text">
                            <?php
                            $activeUpdates = $model->getActiveUpdates();
                            if (!empty($activeUpdates)) {
                                $texts = array_map(fn($u) => $u['title'], $activeUpdates);
                                echo htmlspecialchars(implode('  |  ', $texts));
                            } else {
                                echo 'No active updates';
                            }
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Add Form -->
            <!-- Quick Add Form -->
            <div class="quick-add-row">
                <h6 class="fw-bold mb-3"><i class="fas fa-bolt text-success"></i> Quick Add Update</h6>
                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-sm py-2 mb-2">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="quick_add" value="1">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Title *</label>
                            <input type="text" class="form-control" name="title" placeholder="e.g. Admission Notice 2025-26 released" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold">Link (optional)</label>
                            <input type="text" class="form-control" name="link" placeholder="https://...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">
                                File Upload (optional)
                                <span class="text-muted fw-normal">Max 5MB</span>
                            </label>
                            <input type="file" class="form-control form-control-sm" name="update_file" 
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                            <div class="text-muted" style="font-size:11px; margin-top:3px;">
                                PDF, JPG, PNG, DOC, DOCX, XLS, XLSX
                            </div>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small fw-bold">Order</label>
                            <input type="number" class="form-control" name="display_order" value="0" min="0">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Quick Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Updates Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th style="width:60px">Order</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th style="width:80px">Status</th>
                                    <th style="width:140px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($updates)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-bullhorn fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No updates yet. Add your first update!</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($updates as $index => $update): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="order-badge"><?php echo $update['display_order']; ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($update['title']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            Added: <?php echo date('d M Y', strtotime($update['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($update['file_path']): ?>
                                        <a href="../<?php echo htmlspecialchars($update['file_path']); ?>" target="_blank" class="text-success small d-block mb-1">
                                            <i class="fas fa-paperclip"></i> <?php echo htmlspecialchars($update['file_name']); ?>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($update['link']): ?>
                                        <a href="<?php echo htmlspecialchars($update['link']); ?>" target="_blank" class="text-primary small">
                                            <i class="fas fa-external-link-alt"></i> Link
                                        </a>
                                        <?php elseif (!$update['file_path']): ?>
                                        <span class="text-muted small">No file / link</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($update['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="latest-updates-add.php?edit=<?php echo $update['id']; ?>" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?toggle=1&id=<?php echo $update['id']; ?>" 
                                               class="btn btn-outline-warning" title="Toggle Status">
                                                <i class="fas fa-toggle-on"></i>
                                            </a>
                                            <a href="?delete=1&id=<?php echo $update['id']; ?>" 
                                               class="btn btn-outline-danger" 
                                               onclick="return confirm('Delete this update?');" title="Delete">
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