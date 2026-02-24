<?php
require_once 'config/config.php';
require_once 'includes/HomepageCategory.php';

$homepageCategory = new HomepageCategory();

$slug = $_GET['slug'] ?? '';
$category = $homepageCategory->getCategoryBySlug($slug);

if (!$category) {
    header('Location: index.php');
    exit();
}

$items = $homepageCategory->getItemsByCategory($category['id'], 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> - College</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
        }
        
        .item-card {
            background: white;
            border-left: 4px solid #1e3c72;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .item-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }
        
        .new-tag {
            background: #ff0000;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            animation: blink 1s infinite;
        }
        
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.5; }
        }
        
        .download-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .download-btn:hover {
            background: #bb2d3b;
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="display-4">
                <i class="fas fa-<?php echo htmlspecialchars($category['icon']); ?>"></i>
                <?php echo htmlspecialchars($category['name']); ?>
            </h1>
            <p class="lead">View all <?php echo strtolower($category['name']); ?></p>
        </div>
    </section>
    
    <!-- Items List -->
    <section class="pb-5">
        <div class="container">
            <?php if (empty($items)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                <h4>No items available</h4>
                <p>There are currently no <?php echo strtolower($category['name']); ?> to display.</p>
            </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                <div class="item-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h5>
                                <i class="fas fa-circle" style="font-size: 8px; color: #1e3c72;"></i>
                                <?php echo htmlspecialchars($item['title']); ?>
                                <?php if ($item['show_new']): ?>
                                <span class="new-tag">NEW</span>
                                <?php endif; ?>
                            </h5>
                            
                            <?php if ($item['date']): ?>
                            <p class="text-muted mb-2">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('F d, Y', strtotime($item['date'])); ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if ($item['file_path']): ?>
                            <p class="mb-0">
                                <a href="<?php echo htmlspecialchars($item['file_path']); ?>" 
                                   class="download-btn" target="_blank">
                                    <i class="fas fa-download"></i> Download / View
                                </a>
                            </p>
                            <?php elseif ($item['link']): ?>
                            <p class="mb-0">
                                <a href="<?php echo htmlspecialchars($item['link']); ?>" 
                                   class="download-btn" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Click Here
                                </a>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-home"></i> Back to Homepage
                </a>
            </div>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>