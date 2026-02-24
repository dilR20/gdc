<?php
/**
 * Homepage Diagnostic Script
 * Run this to identify why homepage is not loading
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Diagnostic Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e3c72;
            border-bottom: 3px solid #1e3c72;
            padding-bottom: 10px;
        }
        h2 {
            color: #2a5298;
            margin-top: 30px;
            border-left: 4px solid #2a5298;
            padding-left: 10px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: bold;
            margin-left: 10px;
        }
        .success {
            background: #28a745;
            color: white;
        }
        .error {
            background: #dc3545;
            color: white;
        }
        .warning {
            background: #ffc107;
            color: #000;
        }
        .info {
            background: #17a2b8;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #1e3c72;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .code {
            background: #f4f4f4;
            padding: 10px;
            border-left: 3px solid #dc3545;
            margin: 10px 0;
            font-family: monospace;
        }
        .fix-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Homepage Diagnostic Report</h1>
        <p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        
        <?php
        $errors = [];
        $warnings = [];
        $success = [];
        
        // ==========================================
        // 1. CHECK DATABASE CONNECTION
        // ==========================================
        echo "<h2>1. Database Connection</h2>";
        
        try {
            require_once 'config/config.php';
            require_once 'includes/Database.php';
            
            $database = new Database();
            $conn = $database->getConnection();
            
            if ($conn) {
                echo "<p>✅ Database connection: <span class='status success'>SUCCESS</span></p>";
                $success[] = "Database connected successfully";
            }
        } catch (Exception $e) {
            echo "<p>❌ Database connection: <span class='status error'>FAILED</span></p>";
            echo "<div class='code'>Error: " . $e->getMessage() . "</div>";
            $errors[] = "Database connection failed: " . $e->getMessage();
        }
        
        // ==========================================
        // 2. CHECK DATABASE TABLES
        // ==========================================
        echo "<h2>2. Database Tables</h2>";
        
        $requiredTables = [
            'homepage_categories',
            'homepage_items',
            'latest_updates',
            'notifications',
            'principal',
            'faculty'
        ];
        
        echo "<table>";
        echo "<tr><th>Table Name</th><th>Status</th><th>Row Count</th></tr>";
        
        foreach ($requiredTables as $table) {
            try {
                $query = "SELECT COUNT(*) as count FROM `$table`";
                $stmt = $conn->query($query);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $count = $result['count'];
                
                echo "<tr>";
                echo "<td><strong>$table</strong></td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>$count rows</td>";
                echo "</tr>";
                
                if ($count == 0 && in_array($table, ['homepage_categories', 'homepage_items'])) {
                    $warnings[] = "Table '$table' exists but has no data";
                }
            } catch (Exception $e) {
                echo "<tr>";
                echo "<td><strong>$table</strong></td>";
                echo "<td><span class='status error'>❌ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $errors[] = "Table '$table' does not exist";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 3. CHECK REQUIRED PHP FILES
        // ==========================================
        echo "<h2>3. Required PHP Files</h2>";
        
        $requiredFiles = [
            'includes/Database.php',
            'includes/HomepageCategory.php',
            'includes/Announcement.php',
            'includes/Principal.php',
            'includes/FileUpload.php',
            'includes/Auth.php',
            'config/config.php',
            'index.php'
        ];
        
        echo "<table>";
        echo "<tr><th>File Path</th><th>Status</th><th>Size</th></tr>";
        
        foreach ($requiredFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>" . number_format($size) . " bytes</td>";
                echo "</tr>";
                
                if ($size < 100) {
                    $warnings[] = "File '$file' exists but seems too small ($size bytes)";
                }
            } else {
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status error'>❌ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $errors[] = "Required file '$file' is missing";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 4. CHECK COMPONENT FILES
        // ==========================================
        echo "<h2>4. Component Files</h2>";
        
        $componentFiles = [
            'components/header.php',
            'components/navigation.php',
            'components/hero-slider.php',
            'components/updates-ticker.php',
            'components/homepage-categories.php',
            'components/left-sidebar.php',
            'components/principal-desk.php',
            'components/glimpses.php',
            'components/video-gallery.php',
            'components/important-links.php',
            'components/right-sidebar.php',
            'components/footer.php'
        ];
        
        echo "<table>";
        echo "<tr><th>Component File</th><th>Status</th><th>Size</th></tr>";
        
        $missingComponents = [];
        foreach ($componentFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>" . number_format($size) . " bytes</td>";
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status error'>❌ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $missingComponents[] = $file;
                $errors[] = "Component '$file' is missing";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 5. CHECK CSS FILES
        // ==========================================
        echo "<h2>5. CSS Files</h2>";
        
        $cssFiles = [
            'css/style.css',
            'css/modal.css',
            'css/homepage-sections.css'
        ];
        
        echo "<table>";
        echo "<tr><th>CSS File</th><th>Status</th><th>Size</th></tr>";
        
        foreach ($cssFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>" . number_format($size) . " bytes</td>";
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status error'>❌ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $errors[] = "CSS file '$file' is missing";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 6. CHECK JAVASCRIPT FILES
        // ==========================================
        echo "<h2>6. JavaScript Files</h2>";
        
        $jsFiles = [
            'js/jquery-3.6.0.min.js',
            'js/bootstrap.bundle.min.js',
            'js/components.js',
            'js/main.js',
            'js/modal.js'
        ];
        
        echo "<table>";
        echo "<tr><th>JS File</th><th>Status</th><th>Size</th></tr>";
        
        foreach ($jsFiles as $file) {
            $fullPath = __DIR__ . '/' . $file;
            if (file_exists($fullPath)) {
                $size = filesize($fullPath);
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>" . number_format($size) . " bytes</td>";
                echo "</tr>";
            } else {
                echo "<tr>";
                echo "<td>$file</td>";
                echo "<td><span class='status error'>❌ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $errors[] = "JavaScript file '$file' is missing";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 7. CHECK UPLOAD DIRECTORIES
        // ==========================================
        echo "<h2>7. Upload Directories</h2>";
        
        $uploadDirs = [
            'uploads/',
            'uploads/homepage/',
            'uploads/faculty/',
            'uploads/principal/',
            'uploads/notices/'
        ];
        
        echo "<table>";
        echo "<tr><th>Directory</th><th>Status</th><th>Writable</th></tr>";
        
        foreach ($uploadDirs as $dir) {
            $fullPath = __DIR__ . '/' . $dir;
            if (is_dir($fullPath)) {
                $writable = is_writable($fullPath);
                echo "<tr>";
                echo "<td>$dir</td>";
                echo "<td><span class='status success'>✅ EXISTS</span></td>";
                echo "<td>" . ($writable ? "<span class='status success'>YES</span>" : "<span class='status error'>NO</span>") . "</td>";
                echo "</tr>";
                
                if (!$writable) {
                    $warnings[] = "Directory '$dir' is not writable";
                }
            } else {
                echo "<tr>";
                echo "<td>$dir</td>";
                echo "<td><span class='status warning'>⚠️ MISSING</span></td>";
                echo "<td>-</td>";
                echo "</tr>";
                $warnings[] = "Upload directory '$dir' does not exist";
            }
        }
        echo "</table>";
        
        // ==========================================
        // 8. TEST COMPONENT LOADING
        // ==========================================
        echo "<h2>8. Component Loading Test</h2>";
        
        if (empty($missingComponents)) {
            echo "<p>Testing if components can be loaded...</p>";
            
            $testComponents = [
                'components/homepage-categories.php',
                'components/updates-ticker.php',
                'components/principal-desk.php'
            ];
            
            echo "<table>";
            echo "<tr><th>Component</th><th>Load Test</th><th>Output Size</th></tr>";
            
            foreach ($testComponents as $comp) {
                ob_start();
                try {
                    include __DIR__ . '/' . $comp;
                    $output = ob_get_clean();
                    $outputSize = strlen($output);
                    
                    echo "<tr>";
                    echo "<td>$comp</td>";
                    if ($outputSize > 0) {
                        echo "<td><span class='status success'>✅ LOADS</span></td>";
                        echo "<td>" . number_format($outputSize) . " bytes</td>";
                    } else {
                        echo "<td><span class='status warning'>⚠️ EMPTY</span></td>";
                        echo "<td>0 bytes (no output)</td>";
                        $warnings[] = "Component '$comp' loads but produces no output";
                    }
                    echo "</tr>";
                } catch (Exception $e) {
                    ob_end_clean();
                    echo "<tr>";
                    echo "<td>$comp</td>";
                    echo "<td><span class='status error'>❌ ERROR</span></td>";
                    echo "<td>" . $e->getMessage() . "</td>";
                    echo "</tr>";
                    $errors[] = "Component '$comp' failed to load: " . $e->getMessage();
                }
            }
            echo "</table>";
        } else {
            echo "<p class='code'>⚠️ Cannot test component loading - some components are missing</p>";
        }
        
        // ==========================================
        // 9. CHECK SAMPLE DATA
        // ==========================================
        echo "<h2>9. Sample Data Check</h2>";
        
        try {
            // Check homepage categories
            $stmt = $conn->query("SELECT COUNT(*) as count FROM homepage_categories");
            $catCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Check homepage items
            $stmt = $conn->query("SELECT COUNT(*) as count FROM homepage_items");
            $itemCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            // Check latest updates
            $stmt = $conn->query("SELECT COUNT(*) as count FROM latest_updates");
            $updateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            
            echo "<table>";
            echo "<tr><th>Data Type</th><th>Count</th><th>Status</th></tr>";
            
            echo "<tr>";
            echo "<td>Homepage Categories</td>";
            echo "<td>$catCount</td>";
            echo "<td>" . ($catCount >= 4 ? "<span class='status success'>✅ OK</span>" : "<span class='status warning'>⚠️ LOW</span>") . "</td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td>Homepage Items</td>";
            echo "<td>$itemCount</td>";
            echo "<td>" . ($itemCount > 0 ? "<span class='status success'>✅ OK</span>" : "<span class='status error'>❌ EMPTY</span>") . "</td>";
            echo "</tr>";
            
            echo "<tr>";
            echo "<td>Latest Updates</td>";
            echo "<td>$updateCount</td>";
            echo "<td>" . ($updateCount > 0 ? "<span class='status success'>✅ OK</span>" : "<span class='status warning'>⚠️ EMPTY</span>") . "</td>";
            echo "</tr>";
            
            echo "</table>";
            
            if ($catCount < 4) {
                $warnings[] = "Only $catCount homepage categories found (expected 4)";
            }
            if ($itemCount == 0) {
                $errors[] = "No homepage items in database";
            }
        } catch (Exception $e) {
            echo "<p class='code'>❌ Error checking sample data: " . $e->getMessage() . "</p>";
        }
        
        // ==========================================
        // SUMMARY AND RECOMMENDATIONS
        // ==========================================
        echo "<h2>📊 Summary</h2>";
        
        $totalIssues = count($errors) + count($warnings);
        
        if ($totalIssues == 0) {
            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #28a745; margin-top: 0;'>✅ All Checks Passed!</h3>";
            echo "<p>Your homepage should be working properly. If you're still seeing issues:</p>";
            echo "<ul>";
            echo "<li>Clear your browser cache (Ctrl+Shift+Delete)</li>";
            echo "<li>Check browser console for JavaScript errors (F12)</li>";
            echo "<li>Make sure you're accessing the correct URL</li>";
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 8px;'>";
            echo "<h3 style='color: #dc3545; margin-top: 0;'>❌ Issues Found: $totalIssues</h3>";
            
            if (!empty($errors)) {
                echo "<h4 style='color: #dc3545;'>Critical Errors (" . count($errors) . "):</h4>";
                echo "<ul>";
                foreach ($errors as $error) {
                    echo "<li><strong>$error</strong></li>";
                }
                echo "</ul>";
            }
            
            if (!empty($warnings)) {
                echo "<h4 style='color: #ffc107;'>Warnings (" . count($warnings) . "):</h4>";
                echo "<ul>";
                foreach ($warnings as $warning) {
                    echo "<li>$warning</li>";
                }
                echo "</ul>";
            }
            echo "</div>";
        }
        
        // ==========================================
        // FIX RECOMMENDATIONS
        // ==========================================
        if ($totalIssues > 0) {
            echo "<h2>🔧 Recommended Fixes</h2>";
            
            // Missing tables
            if (in_array('homepage_categories', array_map(function($e) { 
                return strpos($e, 'homepage_categories') !== false; 
            }, $errors))) {
                echo "<div class='fix-section'>";
                echo "<h3>Fix Missing Database Tables:</h3>";
                echo "<p>Run this SQL in phpMyAdmin:</p>";
                echo "<div class='code'>Database SQL file: database.sql</div>";
                echo "<p>Or create manually: <a href='diagnostic.php?action=create_tables'>Create Tables Automatically</a></p>";
                echo "</div>";
            }
            
            // Missing components
            if (!empty($missingComponents)) {
                echo "<div class='fix-section'>";
                echo "<h3>Fix Missing Components:</h3>";
                echo "<p>Create these component files in the <code>components/</code> folder:</p>";
                echo "<ul>";
                foreach ($missingComponents as $comp) {
                    echo "<li>$comp</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
            
            // Missing upload directories
            foreach ($uploadDirs as $dir) {
                if (!is_dir(__DIR__ . '/' . $dir)) {
                    echo "<div class='fix-section'>";
                    echo "<h3>Create Upload Directory:</h3>";
                    echo "<div class='code'>mkdir " . __DIR__ . "/" . $dir . "</div>";
                    echo "<p>Or: <a href='diagnostic.php?action=create_dir&dir=" . urlencode($dir) . "'>Create Directory</a></p>";
                    echo "</div>";
                }
            }
        }
        
        // ==========================================
        // QUICK ACTIONS
        // ==========================================
        if (isset($_GET['action'])) {
            echo "<h2>⚡ Quick Action Results</h2>";
            
            if ($_GET['action'] == 'create_dir' && isset($_GET['dir'])) {
                $dir = $_GET['dir'];
                $fullPath = __DIR__ . '/' . $dir;
                if (!is_dir($fullPath)) {
                    if (mkdir($fullPath, 0777, true)) {
                        echo "<p class='code' style='border-color: #28a745;'>✅ Directory '$dir' created successfully!</p>";
                    } else {
                        echo "<p class='code'>❌ Failed to create directory '$dir'</p>";
                    }
                } else {
                    echo "<p class='code' style='border-color: #ffc107;'>⚠️ Directory '$dir' already exists</p>";
                }
            }
        }
        
        ?>
        
        <hr style="margin: 40px 0;">
        <p style="text-align: center;">
            <a href="index.php" style="background: #1e3c72; color: white; padding: 10px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                🏠 Go to Homepage
            </a>
            &nbsp;&nbsp;
            <a href="diagnostic.php" style="background: #28a745; color: white; padding: 10px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                🔄 Refresh Diagnostic
            </a>
        </p>
    </div>
</body>
</html>