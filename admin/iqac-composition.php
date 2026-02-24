<?php
require_once '../config/config.php';
require_once '../includes/Auth.php';
require_once '../includes/IQAC.php';

$auth = new Auth();
$auth->requireLogin();

$iqacModel = new IQAC();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $data = [
                'name' => $_POST['name'],
                'designation' => $_POST['designation'],
                'role' => $_POST['role'],
                'display_order' => (int)$_POST['display_order'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($_POST['action'] === 'add') {
                if ($iqacModel->createMember($data)) {
                    $_SESSION['success_message'] = 'Member added successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to add member.';
                }
            } else {
                if ($iqacModel->updateMember($_POST['id'], $data)) {
                    $_SESSION['success_message'] = 'Member updated successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to update member.';
                }
            }
        }
    }
    header('Location: iqac-composition.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    if ($iqacModel->deleteMember($_GET['delete'])) {
        $_SESSION['success_message'] = 'Member deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete member.';
    }
    header('Location: iqac-composition.php');
    exit();
}

// Get all members
$members = $iqacModel->getAllMembers(false);

// Get member for editing
$editMember = null;
if (isset($_GET['edit'])) {
    $editMember = $iqacModel->getMemberById($_GET['edit']);
}

$admin = $auth->getAdminInfo();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQAC Composition Management - Admin Panel</title>
    
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
                    <h1><i class="fas fa-users"></i> IQAC Composition Management</h1>
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
                        <h2><?php echo $editMember ? 'Edit' : 'Add New'; ?> IQAC Member</h2>
                    </div>
                    
                    <form method="POST" class="admin-form">
                        <input type="hidden" name="action" value="<?php echo $editMember ? 'edit' : 'add'; ?>">
                        <?php if ($editMember): ?>
                        <input type="hidden" name="id" value="<?php echo $editMember['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Name *</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?php echo $editMember ? htmlspecialchars($editMember['name']) : ''; ?>" 
                                           placeholder="e.g., Dr. John Doe" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Designation *</label>
                                    <input type="text" name="designation" class="form-control" 
                                           value="<?php echo $editMember ? htmlspecialchars($editMember['designation']) : ''; ?>" 
                                           placeholder="e.g., Principal, Assistant Professor" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Role *</label>
                                    <select name="role" class="form-select" required>
                                        <option value="">Select Role</option>
                                        <option value="Chairperson" <?php echo ($editMember && $editMember['role'] == 'Chairperson') ? 'selected' : ''; ?>>Chairperson</option>
                                        <option value="Coordinator" <?php echo ($editMember && $editMember['role'] == 'Coordinator') ? 'selected' : ''; ?>>Coordinator</option>
                                        <option value="Member" <?php echo ($editMember && $editMember['role'] == 'Member') ? 'selected' : ''; ?>>Member</option>
                                        <option value="External Member" <?php echo ($editMember && $editMember['role'] == 'External Member') ? 'selected' : ''; ?>>External Member</option>
                                        <option value="Alumni Member" <?php echo ($editMember && $editMember['role'] == 'Alumni Member') ? 'selected' : ''; ?>>Alumni Member</option>
                                        <option value="Student Member" <?php echo ($editMember && $editMember['role'] == 'Student Member') ? 'selected' : ''; ?>>Student Member</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Display Order</label>
                                    <input type="number" name="display_order" class="form-control" 
                                           value="<?php echo $editMember ? $editMember['display_order'] : 0; ?>"
                                           min="0">
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="checkbox" name="is_active" class="form-check-input" 
                                               <?php echo (!$editMember || $editMember['is_active']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Active</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $editMember ? 'Update' : 'Add'; ?> Member
                            </button>
                            <?php if ($editMember): ?>
                            <a href="iqac-composition.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Members List -->
                <div class="admin-table-container">
                    <div class="table-header">
                        <h2>All IQAC Members</h2>
                        <p class="text-muted mb-0">Total: <?php echo count($members); ?></p>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Order</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($members) > 0): ?>
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?php echo $member['display_order']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($member['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($member['designation']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($member['role']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($member['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo $member['id']; ?>" class="btn-action btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo $member['id']; ?>" class="btn-action btn-delete" 
                                               onclick="return confirm('Are you sure you want to delete this member?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 40px;">
                                        <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                        <p class="text-muted">No IQAC members found. Add one above.</p>
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