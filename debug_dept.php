<?php
/**
 * DEPARTMENT PAGE DEBUG SCRIPT
 * Place this in your website root as debug_department.php
 * Access: http://localhost/website/debug_department.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Department Debug</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #00ff00; }
        .section { background: #2d2d2d; border: 2px solid #00ff00; padding: 15px; margin: 15px 0; }
        .success { color: #00ff00; }
        .error { color: #ff0000; }
        .warning { color: #ffaa00; }
        h2 { color: #00aaff; border-bottom: 2px solid #00aaff; padding-bottom: 5px; }
        pre { background: #1a1a1a; padding: 10px; overflow-x: auto; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #444; padding: 8px; text-align: left; }
        th { background: #333; }
    </style>
</head>
<body>
    <h1>🔍 DEPARTMENT PAGE DIAGNOSTIC</h1>

    <?php
    // ==========================================
    // 1. CHECK FILE EXISTENCE
    // ==========================================
    echo '<div class="section">';
    echo '<h2>1. FILE CHECK</h2>';
    
    $files = [
        'department.php' => __DIR__ . '/department.php',
        'config/config.php' => __DIR__ . '/config/config.php',
        'includes/Department.php' => __DIR__ . '/includes/Department.php',
        'includes/Faculty.php' => __DIR__ . '/includes/Faculty.php',
        'includes/Database.php' => __DIR__ . '/includes/Database.php'
    ];
    
    foreach ($files as $name => $path) {
        if (file_exists($path)) {
            echo "<p class='success'>✓ $name - EXISTS (" . filesize($path) . " bytes)</p>";
        } else {
            echo "<p class='error'>✗ $name - MISSING at: $path</p>";
        }
    }
    echo '</div>';
    
    // ==========================================
    // 2. TEST DATABASE CONNECTION
    // ==========================================
    echo '<div class="section">';
    echo '<h2>2. DATABASE CONNECTION</h2>';
    
    try {
        require_once 'config/config.php';
        require_once 'includes/Database.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        echo "<p class='success'>✓ Database connected successfully</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
        die();
    }
    echo '</div>';
    
    // ==========================================
    // 3. CHECK DEPARTMENT TABLE STRUCTURE
    // ==========================================
    echo '<div class="section">';
    echo '<h2>3. DEPARTMENT TABLE STRUCTURE</h2>';
    
    try {
        $stmt = $db->query("DESCRIBE departments");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
        
        $hasSlug = false;
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . $col['Field'] . "</td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
            
            if ($col['Field'] === 'slug') {
                $hasSlug = true;
            }
        }
        echo "</table>";
        
        if ($hasSlug) {
            echo "<p class='success'>✓ 'slug' column exists</p>";
        } else {
            echo "<p class='error'>✗ 'slug' column is MISSING!</p>";
            echo "<p class='warning'>Fix: Run this SQL:</p>";
            echo "<pre>ALTER TABLE departments ADD COLUMN slug VARCHAR(100) DEFAULT NULL AFTER code;</pre>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    }
    echo '</div>';
    
    // ==========================================
    // 4. CHECK DEPARTMENT DATA
    // ==========================================
    echo '<div class="section">';
    echo '<h2>4. DEPARTMENT DATA</h2>';
    
    try {
        $stmt = $db->query("SELECT id, name, code, slug FROM departments LIMIT 10");
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Code</th><th>Slug</th></tr>";
        
        foreach ($departments as $dept) {
            echo "<tr>";
            echo "<td>" . $dept['id'] . "</td>";
            echo "<td>" . htmlspecialchars($dept['name']) . "</td>";
            echo "<td>" . htmlspecialchars($dept['code']) . "</td>";
            
            if (empty($dept['slug'])) {
                echo "<td class='error'>EMPTY/NULL</td>";
            } else {
                echo "<td class='success'>" . htmlspecialchars($dept['slug']) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Check for Economics specifically
        $econ = $db->query("SELECT * FROM departments WHERE code = 'ECON'")->fetch(PDO::FETCH_ASSOC);
        
        if ($econ) {
            echo "<h3>Economics Department:</h3>";
            echo "<pre>" . print_r($econ, true) . "</pre>";
            
            if (empty($econ['slug'])) {
                echo "<p class='error'>✗ Economics has NO SLUG!</p>";
                echo "<p class='warning'>Fix: Run this SQL:</p>";
                echo "<pre>UPDATE departments SET slug = 'economics' WHERE code = 'ECON';</pre>";
            } else {
                echo "<p class='success'>✓ Economics slug: " . $econ['slug'] . "</p>";
            }
        } else {
            echo "<p class='error'>✗ Economics department not found!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    }
    echo '</div>';
    
    // ==========================================
    // 5. TEST DEPARTMENT CLASS
    // ==========================================
    echo '<div class="section">';
    echo '<h2>5. DEPARTMENT CLASS TEST</h2>';
    
    try {
        require_once 'includes/Department.php';
        
        $deptModel = new Department();
        
        // Check if getBySlug method exists
        if (method_exists($deptModel, 'getBySlug')) {
            echo "<p class='success'>✓ Department::getBySlug() method EXISTS</p>";
            
            // Try to get Economics by slug
            echo "<h3>Testing getBySlug('economics'):</h3>";
            $result = $deptModel->getBySlug('economics');
            
            if ($result) {
                echo "<p class='success'>✓ SUCCESS! Department found:</p>";
                echo "<pre>" . print_r($result, true) . "</pre>";
            } else {
                echo "<p class='error'>✗ getBySlug('economics') returned NULL</p>";
                echo "<p class='warning'>Possible issues:</p>";
                echo "<ul>";
                echo "<li>Slug column is empty</li>";
                echo "<li>Slug value doesn't match 'economics'</li>";
                echo "<li>Query syntax error</li>";
                echo "</ul>";
            }
            
        } else {
            echo "<p class='error'>✗ Department::getBySlug() method DOES NOT EXIST!</p>";
            echo "<p class='warning'>Your Department.php is missing the getBySlug() method.</p>";
            echo "<p>You need to add this method to includes/Department.php</p>";
        }
        
        // Test getByCode as comparison
        echo "<h3>Testing getByCode('ECON') for comparison:</h3>";
        $resultByCode = $deptModel->getByCode('ECON');
        if ($resultByCode) {
            echo "<p class='success'>✓ getByCode works:</p>";
            echo "<pre>" . print_r($resultByCode, true) . "</pre>";
        } else {
            echo "<p class='error'>✗ Even getByCode failed!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error loading Department class: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    echo '</div>';
    
    // ==========================================
    // 6. TEST DIRECT URL ACCESS
    // ==========================================
    echo '<div class="section">';
    echo '<h2>6. URL ACCESS TEST</h2>';
    
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    
    echo "<p>Try these URLs:</p>";
    echo "<ul>";
    echo "<li><a href='department.php?code=ECON' target='_blank'>department.php?code=ECON</a></li>";
    echo "<li><a href='department.php?slug=economics' target='_blank'>department.php?slug=economics</a></li>";
    echo "<li><a href='department.php?id=1' target='_blank'>department.php?id=1</a></li>";
    echo "</ul>";
    
    echo '</div>';
    
    // ==========================================
    // 7. CHECK .HTACCESS / REWRITE RULES
    // ==========================================
    echo '<div class="section">';
    echo '<h2>7. SERVER CONFIGURATION</h2>';
    
    if (file_exists('.htaccess')) {
        echo "<p class='success'>✓ .htaccess file exists</p>";
        $htaccess = file_get_contents('.htaccess');
        echo "<pre>" . htmlspecialchars($htaccess) . "</pre>";
    } else {
        echo "<p class='warning'>⚠ No .htaccess file found (this is usually OK)</p>";
    }
    
    echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
    echo "<p>PHP Version: " . phpversion() . "</p>";
    
    echo '</div>';
    
    // ==========================================
    // 8. RECOMMENDED FIXES
    // ==========================================
    echo '<div class="section">';
    echo '<h2>8. RECOMMENDED FIXES</h2>';
    
    echo "<h3>Step 1: Add slug column (if missing)</h3>";
    echo "<pre>ALTER TABLE departments ADD COLUMN slug VARCHAR(100) DEFAULT NULL AFTER code;</pre>";
    
    echo "<h3>Step 2: Set slugs for all departments</h3>";
    echo "<pre>UPDATE departments SET slug = LOWER(code) WHERE slug IS NULL OR slug = '';
UPDATE departments SET slug = 'economics' WHERE code = 'ECON';</pre>";
    
    echo "<h3>Step 3: Verify Department.php has getBySlug()</h3>";
    echo "<p>Make sure includes/Department.php contains:</p>";
    echo "<pre>public function getBySlug(\$slug) {
    \$query = \"SELECT d.*, 
              IFNULL(hod.name, NULL) as hod_name,
              IFNULL(hod.designation, NULL) as hod_designation
              FROM departments d 
              LEFT JOIN faculty hod ON d.hod_faculty_id = hod.id
              WHERE d.slug = ?\";
    return \$this->database->fetchOne(\$query, [\$slug]);
}</pre>";
    
    echo "<h3>Step 4: Test again</h3>";
    echo "<p>After fixes, try: <a href='department.php?slug=economics'>department.php?slug=economics</a></p>";
    
    echo '</div>';
    ?>
    
    <div class="section">
        <h2>9. QUICK SQL FIX (Copy & Run)</h2>
        <p>Run this in phpMyAdmin if you see errors above:</p>
        <pre>-- Ensure slug column exists and is nullable
ALTER TABLE departments MODIFY COLUMN slug VARCHAR(100) DEFAULT NULL;

-- Generate slugs from codes
UPDATE departments SET slug = LOWER(code) WHERE slug IS NULL OR slug = '';

-- Set specific slugs
UPDATE departments SET slug = 'economics' WHERE code = 'ECON';
UPDATE departments SET slug = 'english' WHERE code = 'ENG';
UPDATE departments SET slug = 'mathematics' WHERE code = 'MATH';

-- Verify
SELECT id, name, code, slug FROM departments;</pre>
    </div>
    
    <hr>
    <p style="text-align: center;">
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="color: #00ff00;">🔄 Refresh Diagnostic</a>
    </p>
</body>
</html>