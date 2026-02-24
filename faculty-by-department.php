<?php
require_once 'config/config.php';
require_once 'includes/Faculty.php';
require_once 'includes/Department.php';

$faculty = new Faculty();
$department = new Department();

// Get department by ID or code
$dept = null;
if (isset($_GET['id'])) {
    $dept = $department->getById((int)$_GET['id']);
} elseif (isset($_GET['code'])) {
    $dept = $department->getByCode($_GET['code']);
}

if (!$dept) {
    header('Location: index.php');
    exit();
}

// Get only ACTIVE faculty members for this department
$facultyMembers = $faculty->getByDepartment($dept['id'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($dept['name']); ?> - Faculty | BN College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/department.css">
    
    <style>
        .faculty-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }
        .faculty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .faculty-photo {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        .faculty-photo-placeholder {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px 8px 0 0;
        }
        .dept-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        .contact-info {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation (include your existing navigation here) -->
    
    <!-- Department Header -->
    <section class="dept-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-3"><?php echo htmlspecialchars($dept['name']); ?></h1>
                    <?php if ($dept['description']): ?>
                    <p class="lead"><?php echo htmlspecialchars($dept['description']); ?></p>
                    <?php endif; ?>
                    <?php if ($dept['head_of_department']): ?>
                    <p><strong>Head of Department:</strong> <?php echo htmlspecialchars($dept['head_of_department']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-light text-dark fs-5 p-3">
                        <i class="fas fa-users"></i> 
                        <?php echo count($facultyMembers); ?> Faculty Members
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Faculty Members -->
    <section class="faculty-section py-5">
        <div class="container">
            <h2 class="mb-4">Our Faculty</h2>
            
            <?php if (empty($facultyMembers)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Faculty information will be updated soon.
            </div>
            <?php else: ?>
            <div class="row">
                <?php foreach ($facultyMembers as $member): ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card faculty-card">
                        <?php if ($member['photo_path']): ?>
                        <img src="<?php echo htmlspecialchars($member['photo_path']); ?>" 
                             alt="<?php echo htmlspecialchars($member['name']); ?>"
                             class="faculty-photo">
                        <?php else: ?>
                        <div class="faculty-photo-placeholder">
                            <i class="fas fa-user fa-5x text-white opacity-50"></i>
                        </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title mb-2"><?php echo htmlspecialchars($member['name']); ?></h5>
                            <p class="text-muted mb-2">
                                <strong><?php echo htmlspecialchars($member['designation']); ?></strong>
                            </p>
                            
                            <?php if ($member['qualification']): ?>
                            <p class="small mb-2">
                                <i class="fas fa-graduation-cap text-primary"></i>
                                <?php echo htmlspecialchars($member['qualification']); ?>
                            </p>
                            <?php endif; ?>
                            
                            <div class="contact-info mt-3">
                                <?php if ($member['email']): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-envelope text-primary"></i>
                                    <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>">
                                        <?php echo htmlspecialchars($member['email']); ?>
                                    </a>
                                </p>
                                <?php endif; ?>
                                
                                <?php if ($member['phone']): ?>
                                <p class="mb-1 small">
                                    <i class="fas fa-phone text-primary"></i>
                                    <?php echo htmlspecialchars($member['phone']); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($member['bio']): ?>
                            <div class="mt-3">
                                <button class="btn btn-sm btn-outline-primary" type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#bio<?php echo $member['id']; ?>">
                                    <i class="fas fa-info-circle"></i> View Profile
                                </button>
                                <div class="collapse mt-2" id="bio<?php echo $member['id']; ?>">
                                    <div class="card card-body small">
                                        <?php echo nl2br(htmlspecialchars($member['bio'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Back to Departments -->
    <section class="py-4 bg-light">
        <div class="container text-center">
            <a href="departments.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> View All Departments
            </a>
        </div>
    </section>
    
    <!-- Footer (include your existing footer here) -->
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
