<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/IQAC.php';
require_once '../includes/FileUpload.php';

$auth = new Auth();
$auth->requireLogin();

$iqacModel = new IQAC();
$category = 'annual'; // Change for each page: utility, aqar, quest, academic, prospectus, activity, nirf, minutes, annual, accreditation

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $data = [
                'category' => $category,
                'title' => $_POST['title'],
                'date' => !empty($_POST['date']) ? $_POST['date'] : null,
                'year' => !empty($_POST['year']) ? $_POST['year'] : null,
                'display_order' => (int)$_POST['display_order'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            // Handle file upload
            if (!empty($_FILES['file']['name'])) {
                $upload = new FileUpload();
                $result = $upload->uploadFile($_FILES['file'], 'iqac');
                
                if ($result['success']) {
                    $data['file_path'] = $result['file_path'];
                } else {
                    $_SESSION['error_message'] = $result['error'];
                    header('Location: iqac-notice.php');
                    exit();
                }
            } elseif ($_POST['action'] === 'edit' && !empty($_POST['existing_file'])) {
                $data['file_path'] = $_POST['existing_file'];
            }
            
            if ($_POST['action'] === 'add') {
                if ($iqacModel->createDocument($data)) {
                    $_SESSION['success_message'] = 'Document added successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to add document.';
                }
            } else {
                if ($iqacModel->updateDocument($_POST['id'], $data)) {
                    $_SESSION['success_message'] = 'Document updated successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to update document.';
                }
            }
        }
    }
    header('Location: iqac-notice.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    if ($iqacModel->deleteDocument($_GET['delete'])) {
        $_SESSION['success_message'] = 'Document deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete document.';
    }
    header('Location: iqac-notice.php');
    exit();
}

// Get all documents
$documents = $iqacModel->getDocumentsByCategory($category, false);

// Get document for editing
$editDoc = null;
if (isset($_GET['edit'])) {
    $editDoc = $iqacModel->getDocumentById($_GET['edit']);
}

$admin = $auth->getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQAC Notice Management - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <div class="admin-topbar">
                <div class="topbar-left">
                    <h1><i class="fas fa-sticky-note"></i> IQAC Notice Management</h1>
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
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- Add/Edit Form -->
                <div class="admin-form-container">
                    <div class="form-header">
                        <h2><?php echo $editDoc ? 'Edit' : 'Add New'; ?> Notice</h2>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="admin-form">
                        <input type="hidden" name="action" value="<?php echo $editDoc ? 'edit' : 'add'; ?>">
                        <?php if ($editDoc): ?>
                        <input type="hidden" name="id" value="<?php echo $editDoc['id']; ?>">
                        <input type="hidden" name="existing_file" value="<?php echo $editDoc['file_path']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Title *</label>
                                    <input type="text" name="title" class="form-control" 
                                           value="<?php echo $editDoc ? htmlspecialchars($editDoc['title']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" 
                                           value="<?php echo $editDoc ? $editDoc['date'] : ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Upload File (PDF)</label>
                                    <input type="file" name="file" class="form-control" accept=".pdf">
                                    <?php if ($editDoc && $editDoc['file_path']): ?>
                                    <small class="text-muted">
                                        Current: <a href="../<?php echo $editDoc['file_path']; ?>" target="_blank">View File</a>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Order</label>
                                    <input type="number" name="display_order" class="form-control" 
                                           value="<?php echo $editDoc ? $editDoc['display_order'] : 0; ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox" name="is_active" class="form-check-input" 
                                               <?php echo (!$editDoc || $editDoc['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $editDoc ? 'Update' : 'Add'; ?> Notice
                            </button>
                            <?php if ($editDoc): ?>
                            <a href="iqac-notice.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Documents List -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>All Notices</h2>
                        <p class="text-muted mb-0">Total: <?php echo count($documents); ?></p>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Order</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>File</th>
                                <th>Status</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($documents) > 0): ?>
                                <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?php echo $doc['display_order']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($doc['title']); ?></strong></td>
                                    <td><?php echo $doc['date'] ? date('M d, Y', strtotime($doc['date'])) : '-'; ?></td>
                                    <td>
                                        <?php if ($doc['file_path']): ?>
                                        <a href="../<?php echo $doc['file_path']; ?>" target="_blank" class="btn-action btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($doc['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo $doc['id']; ?>" class="btn-action btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $doc['id']; ?>" class="btn-action btn-delete" 
                                               onclick="return confirm('Are you sure you want to delete this notice?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No notices found. Add one above.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>