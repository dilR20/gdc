<?php
/**
 * System Diagnostic Tool
 * 
 * Place this file in your website root and access it via browser
 * It will check all requirements and show you what's wrong
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Diagnostic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .check {
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 5px solid #ccc;
        }
        .check.success {
            border-color: #27ae60;
        }
        .check.error {
            border-color: #e74c3c;
        }
        .check.warning {
            border-color: #f39c12;
        }
        h1 {
            color: #2c3e50;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        .icon {
            font-size: 20px;
            margin-right: 10px;
        }
        code {
            background: #f8f9fa;
            padding: 2px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .fix-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>🔍 System Diagnostic Tool</h1>
    <p>This tool will check if your system is properly configured.</p>

    <?php
    $errors = 0;
    $warnings = 0;
    
    // Check 1: PHP Version
    echo "<h2>1. PHP Configuration</h2>";
    
    $phpVersion = phpversion();
    if (version_compare($phpVersion, '7.4.0', '>=')) {
        echo "<div class='check success'>";
        echo "<span class='icon'>✅</span>";
        echo "<strong>PHP Version:</strong> $phpVersion (OK)";
        echo "</div>";
    } else {
        echo "<div class='check error'>";
        echo "<span class='icon'>❌</span>";
        echo "<strong>PHP Version:</strong> $phpVersion (Too old, need 7.4+)";
        echo "</div>";
        $errors++;
    }
    
    // Check 2: Required Extensions
    $required_extensions = ['pdo', 'pdo_mysql', 'mysqli', 'mbstring', 'gd'];
    
    foreach ($required_extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<div class='check success'>";
            echo "<span class='icon'>✅</span>";
            echo "<strong>Extension $ext:</strong> Loaded";
            echo "</div>";
        } else {
            echo "<div class='check error'>";
            echo "<span class='icon'>❌</span>";
            echo "<strong>Extension $ext:</strong> Not loaded";
            echo "<div class='fix-box'>";
            echo "<strong>Fix:</strong> Enable <code>extension=$ext</code> in your php.ini file";
            echo "</div>";
            echo "</div>";
            $errors++;
        }
    }
    
    // Check 3: File Structure
    echo "<h2>2. File Structure</h2>";
    
    $required_files = [
        'config/config.php' => 'Configuration file',
        'includes/Database.php' => 'Database class',
        'includes/Auth.php' => 'Authentication class',
        'includes/Faculty.php' => 'Faculty model',
        'admin/login.php' => 'Admin login page',
        'admin/index.php' => 'Admin dashboard'
    ];
    
    foreach ($required_files as $file => $description) {
        if (file_exists($file)) {
            echo "<div class='check success'>";
            echo "<span class='icon'>✅</span>";
            echo "<strong>$file:</strong> Found";
            echo "</div>";
        } else {
            echo "<div class='check error'>";
            echo "<span class='icon'>❌</span>";
            echo "<strong>$file:</strong> Missing ($description)";
            echo "<div class='fix-box'>";
            echo "<strong>Fix:</strong> Upload this file to the correct location";
            echo "</div>";
            echo "</div>";
            $errors++;
        }
    }
    
    // Check 4: Directories
    echo "<h2>3. Directory Permissions</h2>";
    
    $required_dirs = [
        'uploads/faculty' => 'Faculty photos'
    ];
    
    foreach ($required_dirs as $dir => $description) {
        if (is_dir($dir)) {
            if (is_writable($dir)) {
                echo "<div class='check success'>";
                echo "<span class='icon'>✅</span>";
                echo "<strong>$dir:</strong> Exists and writable";
                echo "</div>";
            } else {
                echo "<div class='check warning'>";
                echo "<span class='icon'>⚠️</span>";
                echo "<strong>$dir:</strong> Exists but not writable";
                echo "<div class='fix-box'>";
                echo "<strong>Fix:</strong> Run <code>chmod 777 $dir</code> (Linux) or check folder properties (Windows)";
                echo "</div>";
                echo "</div>";
                $warnings++;
            }
        } else {
            echo "<div class='check error'>";
            echo "<span class='icon'>❌</span>";
            echo "<strong>$dir:</strong> Directory doesn't exist ($description)";
            echo "<div class='fix-box'>";
            echo "<strong>Fix:</strong> Create this directory: <code>mkdir -p $dir</code>";
            echo "</div>";
            echo "</div>";
            $errors++;
        }
    }
    
    // Check 5: Config File
    echo "<h2>4. Configuration</h2>";
    
    if (file_exists('config/config.php')) {
        include_once 'config/config.php';
        
        // Check if constants are defined
        $required_constants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'SITE_URL'];
        
        foreach ($required_constants as $const) {
            if (defined($const)) {
                $value = constant($const);
                echo "<div class='check success'>";
                echo "<span class='icon'>✅</span>";
                echo "<strong>$const:</strong> $value";
                echo "</div>";
            } else {
                echo "<div class='check error'>";
                echo "<span class='icon'>❌</span>";
                echo "<strong>$const:</strong> Not defined in config.php";
                echo "</div>";
                $errors++;
            }
        }
    }
    
    // Check 6: Database Connection
    echo "<h2>5. Database Connection</h2>";
    
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER')) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                defined('DB_PASS') ? DB_PASS : ''
            );
            
            echo "<div class='check success'>";
            echo "<span class='icon'>✅</span>";
            echo "<strong>Database Connection:</strong> Successful";
            echo "</div>";
            
            // Check tables
            $tables = ['departments', 'faculty', 'admin_users', 'courses'];
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    echo "<div class='check success'>";
                    echo "<span class='icon'>✅</span>";
                    echo "<strong>Table '$table':</strong> Exists";
                    echo "</div>";
                } else {
                    echo "<div class='check error'>";
                    echo "<span class='icon'>❌</span>";
                    echo "<strong>Table '$table':</strong> Missing";
                    echo "<div class='fix-box'>";
                    echo "<strong>Fix:</strong> Import the database.sql file";
                    echo "</div>";
                    echo "</div>";
                    $errors++;
                }
            }
            
        } catch (PDOException $e) {
            echo "<div class='check error'>";
            echo "<span class='icon'>❌</span>";
            echo "<strong>Database Connection:</strong> Failed";
            echo "<div class='fix-box'>";
            echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
            echo "<strong>Fix:</strong> Check your database credentials in config/config.php<br>";
            echo "Make sure MySQL is running and the database exists.";
            echo "</div>";
            echo "</div>";
            $errors++;
        }
    }
    
    // Check 7: Include Path Test
    echo "<h2>6. Include Paths</h2>";
    
    if (file_exists('includes/Database.php')) {
        echo "<div class='check success'>";
        echo "<span class='icon'>✅</span>";
        echo "<strong>Include path test:</strong> Database.php is accessible";
        echo "</div>";
        
        // Try to include it
        try {
            require_once 'includes/Database.php';
            echo "<div class='check success'>";
            echo "<span class='icon'>✅</span>";
            echo "<strong>Database class:</strong> Loaded successfully";
            echo "</div>";
        } catch (Exception $e) {
            echo "<div class='check error'>";
            echo "<span class='icon'>❌</span>";
            echo "<strong>Database class:</strong> Failed to load";
            echo "<div class='fix-box'>";
            echo "<strong>Error:</strong> " . $e->getMessage();
            echo "</div>";
            echo "</div>";
            $errors++;
        }
    }
    
    // Summary
    echo "<h2>📊 Summary</h2>";
    
    if ($errors == 0 && $warnings == 0) {
        echo "<div class='check success' style='font-size: 18px;'>";
        echo "<span class='icon'>🎉</span>";
        echo "<strong>All checks passed!</strong> Your system is properly configured.";
        echo "<br><br>";
        echo "You can now access the admin panel at: <code>admin/login.php</code>";
        echo "</div>";
    } else {
        echo "<div class='check error' style='font-size: 18px;'>";
        echo "<span class='icon'>⚠️</span>";
        echo "<strong>Issues found:</strong> $errors errors, $warnings warnings";
        echo "<br><br>";
        echo "Please fix the issues above before using the system.";
        echo "</div>";
    }
    
    // Next Steps
    echo "<h2>📝 Next Steps</h2>";
    echo "<div class='check' style='border-color: #3498db;'>";
    echo "<ol>";
    echo "<li>Fix any errors shown above</li>";
    echo "<li>Check the TROUBLESHOOTING.md file for detailed solutions</li>";
    echo "<li>If database tables are missing, import database.sql</li>";
    echo "<li>Once all checks pass, delete this diagnostic.php file</li>";
    echo "<li>Access admin panel at: <code>admin/login.php</code></li>";
    echo "</ol>";
    echo "</div>";
    ?>
    
    <hr>
    <p style="text-align: center; color: #7f8c8d;">
        <small>System Diagnostic Tool v1.0 | Delete this file after troubleshooting</small>
    </p>
</body>
</html>
