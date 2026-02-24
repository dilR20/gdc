<?php
require_once 'config/config.php';
require_once 'includes/Faculty.php';
require_once 'includes/Department.php';

// Get faculty ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$facultyModel = new Faculty();
$departmentModel = new Department();

$facultyId = (int)$_GET['id'];
$faculty = $facultyModel->getById($facultyId);

if (!$faculty) {
    header('Location: index.php');
    exit();
}

// Get department info
$department = null;
if (!empty($faculty['department_id'])) {
    $department = $departmentModel->getById($faculty['department_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($faculty['name']); ?> - Gyanpeeth Degree College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Page Banner */
        .page-banner {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('assets/images/college-bg.jpg') center/cover no-repeat;
            padding: 60px 0 40px;
            color: white;
            position: relative;
        }
        
        .page-banner::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(0,0,0,0.1) 10px,
                rgba(0,0,0,0.1) 20px
            );
        }
        
        .page-banner-content {
            position: relative;
            z-index: 1;
        }
        
        .page-banner h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: #ffc107;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #fff;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            color: #ccc;
            content: "›";
        }
        
        /* Profile Content */
        .profile-content {
            padding: 50px 0;
            background: #f8f9fa;
        }
        
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #0f3460 0%, #1a5490 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        
        .profile-photo-wrapper {
            width: 200px;
            height: 200px;
            margin: 0 auto 20px;
            border-radius: 50%;
            overflow: hidden;
            border: 6px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .profile-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .profile-name {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .profile-designation {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        
        .profile-department {
            font-size: 16px;
            opacity: 0.8;
        }
        
        .profile-body {
            padding: 40px;
        }
        
        .info-section {
            margin-bottom: 35px;
        }
        
        .info-section h3 {
            font-size: 20px;
            font-weight: bold;
            color: #0f3460;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ffc107;
            display: inline-block;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .info-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0f3460;
        }
        
        .info-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .info-value a {
            color: #0f3460;
            text-decoration: none;
        }
        
        .info-value a:hover {
            text-decoration: underline;
        }
        
        .bio-text {
            font-size: 15px;
            line-height: 1.8;
            color: #444;
            text-align: justify;
        }
        
        .back-btn {
            display: inline-block;
            padding: 12px 30px;
            background: #0f3460;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #1a5490;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .no-info {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Include your header -->
    <?php include 'components/header.php'; ?>
    <?php include 'components/navigation.php'; ?>
    
    <!-- Page Banner -->
    <div class="page-banner">
        <div class="container">
            <div class="page-banner-content">
                <h1>Faculty Profile</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Faculty</a></li>
                        <?php if ($department): ?>
                        <li class="breadcrumb-item"><a href="department.php?id=<?php echo $department['id']; ?>"><?php echo htmlspecialchars($department['name']); ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($faculty['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Profile Content -->
    <div class="profile-content">
        <div class="container">
            <div class="profile-card">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-photo-wrapper">
                        <?php if (!empty($faculty['photo_path'])): ?>
                        <img src="<?php echo htmlspecialchars($faculty['photo_path']); ?>" 
                             alt="<?php echo htmlspecialchars($faculty['name']); ?>"
                             class="profile-photo">
                        <?php else: ?>
                        <div class="profile-photo-placeholder">
                            <i class="fas fa-user fa-5x" style="color: rgba(255,255,255,0.3);"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-name"><?php echo htmlspecialchars($faculty['name']); ?></div>
                    <div class="profile-designation"><?php echo htmlspecialchars($faculty['designation'] ?? 'Faculty Member'); ?></div>
                    <?php if ($department): ?>
                    <div class="profile-department">
                        <i class="fas fa-building"></i> <?php echo htmlspecialchars($department['name']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Profile Body -->
                <div class="profile-body">
                    <!-- Contact Information -->
                    <?php if (!empty($faculty['email']) || !empty($faculty['phone'])): ?>
                    <div class="info-section">
                        <h3><i class="fas fa-address-card"></i> Contact Information</h3>
                        <div class="info-grid">
                            <?php if (!empty($faculty['email'])): ?>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="info-value">
                                    <a href="mailto:<?php echo htmlspecialchars($faculty['email']); ?>">
                                        <?php echo htmlspecialchars($faculty['email']); ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($faculty['phone'])): ?>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-phone"></i> Phone</div>
                                <div class="info-value">
                                    <a href="tel:<?php echo htmlspecialchars($faculty['phone']); ?>">
                                        <?php echo htmlspecialchars($faculty['phone']); ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Educational Qualifications -->
                    <?php if (!empty($faculty['qualification'])): ?>
                    <div class="info-section">
                        <h3><i class="fas fa-graduation-cap"></i> Educational Qualification</h3>
                        <div class="bio-text">
                            <?php echo nl2br(htmlspecialchars($faculty['qualification'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Biography -->
                    <?php if (!empty($faculty['bio'])): ?>
                    <div class="info-section">
                        <h3><i class="fas fa-user-circle"></i> About</h3>
                        <div class="bio-text">
                            <?php echo nl2br(htmlspecialchars($faculty['bio'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Service Details -->
                    <?php if (!empty($faculty['joining_date']) || !empty($faculty['leave_date'])): ?>
                    <div class="info-section">
                        <h3><i class="fas fa-calendar-alt"></i> Service Details</h3>
                        <div class="info-grid">
                            <?php if (!empty($faculty['joining_date'])): ?>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-sign-in-alt"></i> Joining Date</div>
                                <div class="info-value"><?php echo date('F d, Y', strtotime($faculty['joining_date'])); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($faculty['leave_date'])): ?>
                            <div class="info-item">
                                <div class="info-label"><i class="fas fa-sign-out-alt"></i> Leave Date</div>
                                <div class="info-value"><?php echo date('F d, Y', strtotime($faculty['leave_date'])); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Back Button -->
                    <div class="text-center mt-4">
                        <?php if ($department): ?>
                        <a href="department.php?id=<?php echo $department['id']; ?>" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Back to <?php echo htmlspecialchars($department['name']); ?>
                        </a>
                        <?php else: ?>
                        <a href="index.php" class="back-btn">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include your footer -->
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>