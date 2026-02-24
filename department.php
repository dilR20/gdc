<?php
require_once 'config/config.php';
require_once 'includes/Department.php';
require_once 'includes/Faculty.php';

$departmentModel = new Department();
$facultyModel = new Faculty();

// Get department by slug or code
$dept = null;
if (isset($_GET['slug'])) {
    $dept = $departmentModel->getBySlug($_GET['slug']);
} elseif (isset($_GET['code'])) {
    $dept = $departmentModel->getByCode($_GET['code']);
} elseif (isset($_GET['id'])) {
    $dept = $departmentModel->getById($_GET['id']);
}

if (!$dept) {
    header('Location: index.php');
    exit();
}

// Get faculty members for this department
$faculty = $facultyModel->getByDepartment($dept['id'], true); // true = active only

// Get courses for this department
$courses = $departmentModel->getCourses($dept['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($dept['name']); ?> - Gyanpeeth Degree College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Page Banner */
        .page-banner {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                        url('assets/images/college-bg.jpg') center/cover no-repeat;
            padding: 80px 0 40px;
            color: white;
            position: relative;
        }
        
        .page-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 15px;
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
        
        /* Department Content */
        .department-content {
            padding: 50px 0;
            background: #f8f9fa;
        }
        
        /* Tab Navigation */
        .dept-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 30px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .dept-tab {
            padding: 18px 35px;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            background: #f8f9fa;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }
        
        .dept-tab:first-child {
            border-radius: 8px 0 0 0;
        }
        
        .dept-tab:hover {
            background: #e9ecef;
            color: #333;
        }
        
        .dept-tab.active {
            background: #0f3460;
            color: white;
            border-bottom: 3px solid #ffc107;
        }
        
        /* Tab Content */
        .tab-content-item {
            display: none;
            animation: fadeIn 0.4s;
        }
        
        .tab-content-item.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Department Info */
        .dept-info {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            line-height: 1.8;
            font-size: 16px;
            color: #444;
            text-align: justify;
        }
        
        /* Faculty Grid */
        .faculty-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .faculty-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .faculty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .faculty-photo {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background: #f0f0f0;
        }
        
        .faculty-info {
            padding: 15px;
            text-align: center;
        }
        
        .faculty-name {
            font-size: 15px;
            font-weight: bold;
            color: #0f3460;
            margin-bottom: 5px;
        }
        
        .faculty-designation {
            color: #666;
            font-size: 13px;
        }
        
        /* Courses Table */
        .courses-table-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .courses-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .courses-table thead {
            background: #0f3460;
            color: white;
        }
        
        .courses-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        .courses-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .courses-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
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
                <h1>Department Of <?php echo htmlspecialchars($dept['name']); ?></h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item"><a href="#">Department</a></li>
                        <li class="breadcrumb-item"><a href="#">arts</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($dept['name']); ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Department Content -->
    <div class="department-content">
        <div class="container">
            <!-- Tabs Navigation -->
            <div class="dept-tabs">
                <button class="dept-tab active" onclick="switchTab('department')">Department</button>
                <button class="dept-tab" onclick="switchTab('faculty')">Faculty</button>
                <button class="dept-tab" onclick="switchTab('courses')">Courses Offered & Seat Capacity</button>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Department Info Tab -->
                <div id="tab-department" class="tab-content-item active">
                    <div class="dept-info">
                        <?php if (!empty($dept['description'])): ?>
                            <?php echo nl2br(htmlspecialchars($dept['description'])); ?>
                        <?php else: ?>
                            <div class="no-data">
                                <i class="fas fa-info-circle"></i>
                                <p>Department information will be updated soon.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Faculty Tab -->
                <div id="tab-faculty" class="tab-content-item">
                    <?php if (count($faculty) > 0): ?>
                    <div class="faculty-grid">
                        <?php foreach ($faculty as $member): ?>
                        <a href="faculty-profile.php?id=<?php echo $member['id']; ?>" class="faculty-card">
                            <?php if (!empty($member['photo_path'])): ?>
                            <img src="<?php echo htmlspecialchars($member['photo_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($member['name']); ?>"
                                 class="faculty-photo">
                            <?php else: ?>
                            <div class="faculty-photo" style="display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-user fa-5x" style="color: rgba(255,255,255,0.3);"></i>
                            </div>
                            <?php endif; ?>
                            
                            <div class="faculty-info">
                                <div class="faculty-name"><?php echo htmlspecialchars($member['name'] ?? ''); ?></div>
                                <div class="faculty-designation"><?php echo htmlspecialchars($member['designation'] ?? ''); ?></div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="dept-info">
                        <div class="no-data">
                            <i class="fas fa-users"></i>
                            <p>Faculty information will be updated soon.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Courses Tab -->
                <div id="tab-courses" class="tab-content-item">
                    <div class="courses-table-container">
                        <?php if (count($courses) > 0): ?>
                        <table class="courses-table">
                            <thead>
                                <tr>
                                    <th>Course name</th>
                                    <th>Seat Capacity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($course['course_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($course['seat_capacity'] ?? 'N/A'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-book"></i>
                            <p>Course information will be updated soon.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Include your footer -->
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function switchTab(tabName) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.dept-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content-item').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }
    </script>
</body>
</html>