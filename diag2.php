<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Diagnostic</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #00ff00;
            padding: 20px;
            line-height: 1.6;
        }
        .section {
            background: #2d2d2d;
            border: 2px solid #00ff00;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        .info { color: #00aaff; }
        h2 {
            color: #00ff00;
            border-bottom: 2px solid #00ff00;
            padding-bottom: 5px;
        }
        h3 {
            color: #00aaff;
            margin-top: 15px;
        }
        .code {
            background: #1a1a1a;
            padding: 10px;
            border-left: 3px solid #00ff00;
            margin: 10px 0;
            overflow-x: auto;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li:before {
            content: "▸ ";
            color: #00ff00;
        }
        .pass:before {
            content: "✓ ";
            color: #00ff00;
        }
        .fail:before {
            content: "✗ ";
            color: #ff0000;
        }
    </style>
</head>
<body>
    <h1>🔍 WEBSITE DIAGNOSTIC REPORT</h1>
    <p class="info">Generated: <?php echo date('Y-m-d H:i:s'); ?></p>

    <!-- ==================== FILE STRUCTURE CHECK ==================== -->
    <div class="section">
        <h2>1. FILE STRUCTURE CHECK</h2>
        <?php
        $requiredFiles = [
            'index.php' => __DIR__ . '/index.php',
            'css/style.css' => __DIR__ . '/css/style.css',
            'js/components.js' => __DIR__ . '/js/components.js',
            'config/config.php' => __DIR__ . '/config/config.php',
            'includes/Database.php' => __DIR__ . '/includes/Database.php',
            'includes/Auth.php' => __DIR__ . '/includes/Auth.php',
            'includes/HeroSlider.php' => __DIR__ . '/includes/HeroSlider.php',
            'includes/HomepageCategory.php' => __DIR__ . '/includes/HomepageCategory.php',
            'includes/Principal.php' => __DIR__ . '/includes/Principal.php',
            'components/header.php' => __DIR__ . '/components/header.php',
            'components/navigation.php' => __DIR__ . '/components/navigation.php',
            'components/hero-slider.php' => __DIR__ . '/components/hero-slider.php',
            'components/updates-ticker.php' => __DIR__ . '/components/updates-ticker.php',
            'components/three-column-section.php' => __DIR__ . '/components/three-column-section.php',
            'components/category-cards.php' => __DIR__ . '/components/category-cards.php',
            'components/video-gallery.php' => __DIR__ . '/components/video-gallery.php',
            'components/important-links.php' => __DIR__ . '/components/important-links.php',
            'components/footer.php' => __DIR__ . '/components/footer.php',
        ];

        echo "<ul>";
        foreach ($requiredFiles as $name => $path) {
            if (file_exists($path)) {
                echo "<li class='pass success'>$name - EXISTS (" . number_format(filesize($path)) . " bytes)</li>";
            } else {
                echo "<li class='fail error'>$name - MISSING</li>";
            }
        }
        echo "</ul>";
        ?>
    </div>

    <!-- ==================== DATABASE CHECK ==================== -->
    <div class="section">
        <h2>2. DATABASE CONNECTION CHECK</h2>
        <?php
        if (file_exists(__DIR__ . '/config/config.php')) {
            require_once __DIR__ . '/config/config.php';
            
            echo "<h3>Database Configuration:</h3>";
            echo "<div class='code'>";
            echo "Host: " . (defined('DB_HOST') ? DB_HOST : '<span class="error">NOT DEFINED</span>') . "<br>";
            echo "Database: " . (defined('DB_NAME') ? DB_NAME : '<span class="error">NOT DEFINED</span>') . "<br>";
            echo "Username: " . (defined('DB_USER') ? DB_USER : '<span class="error">NOT DEFINED</span>') . "<br>";
            echo "</div>";

            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                echo "<p class='success'>✓ Database connection successful!</p>";
                
                // Check tables
                echo "<h3>Database Tables:</h3>";
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                echo "<ul>";
                foreach ($tables as $table) {
                    $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                    echo "<li class='pass success'>$table ($count rows)</li>";
                }
                echo "</ul>";
                
            } catch (PDOException $e) {
                echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            echo "<p class='error'>✗ config.php file not found!</p>";
        }
        ?>
    </div>

    <!-- ==================== COMPONENT TEST ==================== -->
    <div class="section">
        <h2>3. COMPONENT LOADING TEST</h2>
        <?php
        $components = [
            'header' => __DIR__ . '/components/header.php',
            'navigation' => __DIR__ . '/components/navigation.php',
            'hero-slider' => __DIR__ . '/components/hero-slider.php',
            'updates-ticker' => __DIR__ . '/components/updates-ticker.php',
            'three-column-section' => __DIR__ . '/components/three-column-section.php',
            'category-cards' => __DIR__ . '/components/category-cards.php',
        ];

        foreach ($components as $name => $path) {
            echo "<h3>Testing: $name</h3>";
            if (file_exists($path)) {
                echo "<div class='code'>";
                ob_start();
                try {
                    include $path;
                    $output = ob_get_clean();
                    if (!empty(trim($output))) {
                        echo "<span class='success'>✓ Component loads and produces output (" . strlen($output) . " chars)</span>";
                    } else {
                        echo "<span class='warning'>⚠ Component loads but produces no output</span>";
                    }
                } catch (Exception $e) {
                    ob_end_clean();
                    echo "<span class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</span>";
                }
                echo "</div>";
            } else {
                echo "<p class='error'>✗ File not found</p>";
            }
        }
        ?>
    </div>

    <!-- ==================== PHP CONFIGURATION ==================== -->
    <div class="section">
        <h2>4. PHP CONFIGURATION</h2>
        <div class="code">
            <?php
            echo "PHP Version: " . phpversion() . "<br>";
            echo "Display Errors: " . ini_get('display_errors') . "<br>";
            echo "Error Reporting: " . error_reporting() . "<br>";
            echo "Max Execution Time: " . ini_get('max_execution_time') . "s<br>";
            echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
            echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";
            echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
            ?>
        </div>
    </div>

    <!-- ==================== PERMISSIONS CHECK ==================== -->
    <div class="section">
        <h2>5. DIRECTORY PERMISSIONS</h2>
        <?php
        $directories = [
            'uploads' => __DIR__ . '/uploads',
            'uploads/sliders' => __DIR__ . '/uploads/sliders',
            'assets' => __DIR__ . '/assets',
            'assets/images' => __DIR__ . '/assets/images',
        ];

        echo "<ul>";
        foreach ($directories as $name => $path) {
            if (file_exists($path)) {
                $writable = is_writable($path);
                $class = $writable ? 'success' : 'error';
                $status = $writable ? 'WRITABLE' : 'NOT WRITABLE';
                echo "<li class='$class'>$name - $status</li>";
            } else {
                echo "<li class='warning'>$name - DOES NOT EXIST</li>";
            }
        }
        echo "</ul>";
        ?>
    </div>

    <!-- ==================== JAVASCRIPT CHECK ==================== -->
    <div class="section">
        <h2>6. JAVASCRIPT LOADING TEST</h2>
        <p id="js-test" class="error">✗ JavaScript NOT loaded</p>
        <script>
            document.getElementById('js-test').innerHTML = '<span class="success">✓ JavaScript is working</span>';
            
            // Test component loader
            fetch('components/header.php')
                .then(response => {
                    if (response.ok) {
                        document.getElementById('js-test').innerHTML += '<br><span class="success">✓ AJAX fetch is working</span>';
                    } else {
                        document.getElementById('js-test').innerHTML += '<br><span class="error">✗ AJAX fetch failed (status: ' + response.status + ')</span>';
                    }
                })
                .catch(error => {
                    document.getElementById('js-test').innerHTML += '<br><span class="error">✗ AJAX error: ' + error.message + '</span>';
                });
        </script>
    </div>

    <!-- ==================== RECOMMENDATIONS ==================== -->
    <div class="section">
        <h2>7. RECOMMENDATIONS</h2>
        <div class="code">
            <?php
            $issues = [];
            
            // Check for missing files
            foreach ($requiredFiles as $name => $path) {
                if (!file_exists($path)) {
                    $issues[] = "Create missing file: $name";
                }
            }
            
            // Check database
            if (!defined('DB_HOST')) {
                $issues[] = "Configure database settings in config/config.php";
            }
            
            // Check uploads directory
            if (!file_exists(__DIR__ . '/uploads') || !is_writable(__DIR__ . '/uploads')) {
                $issues[] = "Create uploads directory and make it writable";
            }
            
            if (empty($issues)) {
                echo "<p class='success'>✓ No critical issues found!</p>";
            } else {
                echo "<p class='warning'>Issues found:</p><ul>";
                foreach ($issues as $issue) {
                    echo "<li class='warning'>$issue</li>";
                }
                echo "</ul>";
            }
            ?>
        </div>
    </div>

    <!-- ==================== QUICK ACTIONS ==================== -->
    <div class="section">
        <h2>8. QUICK ACTIONS</h2>
        <p><a href="index.php" style="color: #00ff00;">→ Go to Homepage</a></p>
        <p><a href="admin/login.php" style="color: #00ff00;">→ Go to Admin Login</a></p>
        <p><a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="color: #00ff00;">→ Refresh Diagnostic</a></p>
    </div>

    <div class="section">
        <h2>9. BROWSER CONSOLE CHECK</h2>
        <p class="info">Press F12 and check the Console tab for JavaScript errors</p>
        <script>
            console.log('%c✓ Diagnostic script loaded successfully', 'color: #00ff00; font-size: 14px;');
            console.log('Checking component paths...');
            
            const components = [
                'components/header.php',
                'components/navigation.php',
                'components/hero-slider.php'
            ];
            
            components.forEach(path => {
                fetch(path)
                    .then(response => {
                        if (response.ok) {
                            console.log('%c✓ ' + path + ' - OK', 'color: #00ff00');
                        } else {
                            console.error('✗ ' + path + ' - Failed (status: ' + response.status + ')');
                        }
                    })
                    .catch(error => {
                        console.error('✗ ' + path + ' - Error: ' + error.message);
                    });
            });
        </script>
    </div>
</body>
</html>