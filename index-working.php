<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gyanpeeth Degree College</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/homepage-sections.css">
</head>
<body>
    <!-- Top Header -->
    <?php include 'components/header.php'; ?>
    
    <!-- Navigation -->
    <?php include 'components/navigation.php'; ?>
    
    <!-- Hero Slider -->
    <?php include 'components/hero-slider.php'; ?>
    
    <!-- Latest Updates Ticker -->
    <?php include 'components/updates-ticker.php'; ?>
    
    <!-- Homepage Category Sections -->
    <?php include 'components/homepage-categories.php'; ?>
    
    <!-- Main Content -->
    <main class="container-fluid mt-4">
        <div class="row">
            <!-- Left Sidebar -->
            <aside class="col-lg-2 col-md-3">
                <?php include 'components/left-sidebar.php'; ?>
            </aside>
            
            <!-- Center Content -->
            <div class="col-lg-7 col-md-6">
                <!-- Principal's Desk -->
                <?php include 'components/principal-desk.php'; ?>
                
                <!-- Glimpses Section -->
                <?php include 'components/glimpses.php'; ?>
                
                <!-- Video Gallery -->
                <?php include 'components/video-gallery.php'; ?>
                
                <!-- Important Links -->
                <?php include 'components/important-links.php'; ?>
            </div>
            
            <!-- Right Sidebar -->
            <aside class="col-lg-3 col-md-3">
                <?php include 'components/right-sidebar.php'; ?>
            </aside>
        </div>
    </main>
    
    <!-- Footer -->
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>