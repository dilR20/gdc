<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/Principal.php';

$auth = new Auth();
$auth->requireLogin();

$principalModel = new Principal();
$adminInfo = $auth->getAdminInfo();

$principals = $principalModel->getAll();

// Handle actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'set_current' && $auth->verifyCSRFToken($_GET['token'] ?? '')) {
        $principalModel->setAsCurrent($id);
        header('Location: principal-list.php?updated=1');
        exit();
    } elseif ($_GET['action'] === 'delete' && $auth->verifyCSRFToken($_GET['token'] ?? '')) {
        $principalModel->delete($id);
        header('Location: principal-list.php?deleted=1');
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
    <title>Principal Management - Admin Panel</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-user-tie"></i> Principal Management</h1>
                <a href="principal-add.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Principal
                </a>
            </div>
            
            <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Principal added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Principal updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> Principal deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> Only ONE principal can be marked as "Current" at a time. The current principal will be displayed on the public website.
            </div>
            
            <?php if (empty($principals)): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-tie fa-4x text-muted mb-3"></i>
                    <h4>No Principal Records Found</h4>
                    <p class="text-muted">Add your college principal's information to get started.</p>
                    <a href="principal-add.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Principal
                    </a>
                </div>
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($principals as $principal): ?>
                <div class="col-md-6 mb-4">
                    <div class="card <?php echo $principal['is_current'] ? 'border-success' : ''; ?>">
                        <?php if ($principal['is_current']): ?>
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-star"></i> Current Principal
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php if ($principal['photo_path']): ?>
                                    <img src="../<?php echo htmlspecialchars($principal['photo_path']); ?>" 
                                         alt="Principal Photo" 
                                         class="img-thumbnail" 
                                         style="width: 100%;">
                                    <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 100%; height: 150px;">
                                        <i class="fas fa-user fa-3x"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-9">
                                    <h4><?php echo htmlspecialchars($principal['name']); ?></h4>
                                    <p class="text-muted mb-2">
                                        <strong><?php echo htmlspecialchars($principal['designation']); ?></strong>
                                    </p>
                                    
                                    <?php if ($principal['qualification']): ?>
                                    <p class="small mb-2">
                                        <i class="fas fa-graduation-cap"></i>
                                        <?php echo htmlspecialchars($principal['qualification']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($principal['email']): ?>
                                    <p class="small mb-1">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($principal['email']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($principal['phone']): ?>
                                    <p class="small mb-1">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($principal['phone']); ?>
                                    </p>
                                    <?php endif; ?>
                                    
                                    <?php if ($principal['joining_date']): ?>
                                    <p class="small mb-1 text-muted">
                                        <i class="fas fa-calendar"></i>
                                        Joined: <?php echo date('M d, Y', strtotime($principal['joining_date'])); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if ($principal['message']): ?>
                            <div class="mt-3">
                                <strong>Message:</strong>
                                <p class="small"><?php echo nl2br(htmlspecialchars(substr($principal['message'], 0, 200))); ?>...</p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <div class="btn-group" role="group">
                                    <a href="principal-add.php?edit=<?php echo $principal['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <?php if (!$principal['is_current']): ?>
                                    <a href="?action=set_current&id=<?php echo $principal['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                       class="btn btn-sm btn-outline-success"
                                       onclick="return confirm('Set this as the current principal?');">
                                        <i class="fas fa-star"></i> Set as Current
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="?action=delete&id=<?php echo $principal['id']; ?>&token=<?php echo $csrfToken; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this principal record?');">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
