<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Page - Direct Component Loading</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/homepage-sections.css">
</head>
<body>
    <h1 class="text-center bg-primary text-white py-3">Component Test Page</h1>
    
    <div class="container-fluid">
        <div class="alert alert-info">
            <strong>Testing Direct PHP Include (No JavaScript)</strong>
        </div>
        
        <!-- Test Header -->
        <section class="mb-4">
            <h2>1. Header Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/header.php';
                    echo '<div class="alert alert-success mt-2">✅ Header loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Header error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Navigation -->
        <section class="mb-4">
            <h2>2. Navigation Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/navigation.php';
                    echo '<div class="alert alert-success mt-2">✅ Navigation loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Navigation error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Updates Ticker -->
        <section class="mb-4">
            <h2>3. Updates Ticker Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/updates-ticker.php';
                    echo '<div class="alert alert-success mt-2">✅ Updates Ticker loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Updates Ticker error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Homepage Categories -->
        <section class="mb-4">
            <h2>4. Homepage Categories Component (THE MAIN ONE)</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/homepage-categories.php';
                    echo '<div class="alert alert-success mt-2">✅ Homepage Categories loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Homepage Categories error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Principal Desk -->
        <section class="mb-4">
            <h2>5. Principal Desk Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/principal-desk.php';
                    echo '<div class="alert alert-success mt-2">✅ Principal Desk loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Principal Desk error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Right Sidebar -->
        <section class="mb-4">
            <h2>6. Right Sidebar Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/right-sidebar.php';
                    echo '<div class="alert alert-success mt-2">✅ Right Sidebar loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Right Sidebar error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
        
        <!-- Test Footer -->
        <section class="mb-4">
            <h2>7. Footer Component</h2>
            <div class="border p-3">
                <?php 
                try {
                    include 'components/footer.php';
                    echo '<div class="alert alert-success mt-2">✅ Footer loaded successfully</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-2">❌ Footer error: ' . $e->getMessage() . '</div>';
                }
                ?>
            </div>
        </section>
    </div>
    
    <div class="text-center my-5">
        <a href="index.php" class="btn btn-primary btn-lg">Go Back to Index</a>
        <a href="diagnostic.php" class="btn btn-secondary btn-lg">Run Diagnostic</a>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>