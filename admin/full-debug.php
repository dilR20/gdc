<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session BEFORE any output
session_start();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Full Session Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .error { border-color: #dc3545; background: #fff5f5; }
        .success { border-color: #28a745; background: #f0fff0; }
        .warning { border-color: #ffc107; background: #fffef0; }
        h2 { color: #333; margin-top: 20px; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; margin: 5px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Complete Session Diagnostic</h1>
    
    <?php
    // Test 1: Session Status
    echo "<div class='box'>";
    echo "<h2>1. Session Status</h2>";
    echo "Session ID: <strong>" . session_id() . "</strong><br>";
    echo "Session Status: <strong>" . session_status() . "</strong> ";
    echo "(2 = Active, 1 = Disabled, 0 = None)<br>";
    echo "Session Save Path: <strong>" . session_save_path() . "</strong><br>";
    echo "Session Cookie Params:<br>";
    echo "<pre>";
    print_r(session_get_cookie_params());
    echo "</pre>";
    echo "</div>";
    
    // Test 2: Session Variables
    echo "<div class='box " . (empty($_SESSION) ? "error" : "success") . "'>";
    echo "<h2>2. Session Variables</h2>";
    if (empty($_SESSION)) {
        echo "⚠️ <strong>SESSION IS EMPTY!</strong><br>";
        echo "This means you are NOT logged in.<br>";
    } else {
        echo "✅ Session has data:<br>";
    }
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";
    echo "</div>";
    
    // Test 3: Set a test variable
    $_SESSION['test_variable'] = 'Test at ' . date('H:i:s');
    echo "<div class='box'>";
    echo "<h2>3. Session Write Test</h2>";
    echo "Just set: \$_SESSION['test_variable'] = '" . $_SESSION['test_variable'] . "'<br>";
    echo "✅ Session write successful<br>";
    echo "</div>";
    
    // Test 4: Files
    echo "<div class='box'>";
    echo "<h2>4. File Structure</h2>";
    $files = [
        '../config/config.php' => 'Config',
        '../includes/Auth.php' => 'Auth Class',
        '../includes/Database.php' => 'Database Class',
        '../includes/Department.php' => 'Department Model',
        '../includes/Course.php' => 'Course Model',
        'faculty-list.php' => 'Faculty List (Working)',
        'departments-list.php' => 'Departments List',
        'courses-list.php' => 'Courses List'
    ];
    
    foreach ($files as $path => $name) {
        $exists = file_exists($path);
        echo ($exists ? "✅" : "❌") . " <strong>$name:</strong> ";
        echo $exists ? "EXISTS" : "MISSING";
        if ($exists) {
            echo " (" . number_format(filesize($path)) . " bytes)";
        }
        echo "<br>";
    }
    echo "</div>";
    
    // Test 5: Try Auth Class
    echo "<div class='box'>";
    echo "<h2>5. Authentication Test</h2>";
    
    try {
        require_once '../config/config.php';
        require_once '../includes/Auth.php';
        
        $auth = new Auth();
        
        echo "✅ Auth class loaded<br>";
        echo "isLoggedIn(): <strong>" . ($auth->isLoggedIn() ? "TRUE ✅" : "FALSE ❌") . "</strong><br>";
        echo "getAdminId(): <strong>" . ($auth->getAdminId() ?? "NULL") . "</strong><br>";
        
        if (!$auth->isLoggedIn()) {
            echo "<br>⚠️ <strong>YOU ARE NOT LOGGED IN!</strong><br>";
            echo "Please <a href='login.php' class='btn'>Login Here</a> first.<br>";
        } else {
            $adminInfo = $auth->getAdminInfo();
            echo "<br>✅ <strong>YOU ARE LOGGED IN!</strong><br>";
            echo "Admin: " . ($adminInfo['full_name'] ?? 'Unknown') . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error loading Auth: " . $e->getMessage() . "<br>";
    }
    echo "</div>";
    
    // Test 6: Current Page
    echo "<div class='box'>";
    echo "<h2>6. Current Page Info</h2>";
    echo "Current URL: <strong>" . $_SERVER['REQUEST_URI'] . "</strong><br>";
    echo "Current File: <strong>" . basename($_SERVER['PHP_SELF']) . "</strong><br>";
    echo "Document Root: <strong>" . $_SERVER['DOCUMENT_ROOT'] . "</strong><br>";
    echo "Current Directory: <strong>" . __DIR__ . "</strong><br>";
    echo "</div>";
    
    // Test 7: Database
    echo "<div class='box'>";
    echo "<h2>7. Database Test</h2>";
    
    try {
        require_once '../includes/Database.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        echo "✅ Database connected<br>";
        
        // Test departments table
        try {
            $stmt = $conn->query("SELECT COUNT(*) as count FROM departments");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ Departments table exists (" . $result['count'] . " records)<br>";
        } catch (Exception $e) {
            echo "❌ Departments table: " . $e->getMessage() . "<br>";
        }
        
        // Test courses table
        try {
            $stmt = $conn->query("SELECT COUNT(*) as count FROM courses");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ Courses table exists (" . $result['count'] . " records)<br>";
        } catch (Exception $e) {
            echo "❌ Courses table: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
    echo "</div>";
    
    // Test 8: Compare with Faculty
    echo "<div class='box'>";
    echo "<h2>8. Faculty Page Comparison</h2>";
    
    if (file_exists('faculty-list.php')) {
        $facultyContent = file_get_contents('faculty-list.php');
        
        echo "Faculty-list.php analysis:<br>";
        echo "Has session_start(): " . (strpos($facultyContent, 'session_start()') !== false ? "YES ⚠️" : "NO ✅") . "<br>";
        echo "Has Auth require: " . (strpos($facultyContent, "require_once '../includes/Auth.php'") !== false ? "YES ✅" : "NO ❌") . "<br>";
        echo "Has requireLogin: " . (strpos($facultyContent, '$auth->requireLogin()') !== false ? "YES ✅" : "NO ❌") . "<br>";
    }
    
    if (file_exists('departments-list.php')) {
        $deptContent = file_get_contents('departments-list.php');
        
        echo "<br>Departments-list.php analysis:<br>";
        echo "Has session_start(): " . (strpos($deptContent, 'session_start()') !== false ? "YES ⚠️" : "NO ✅") . "<br>";
        echo "Has Auth require: " . (strpos($deptContent, "require_once '../includes/Auth.php'") !== false ? "YES ✅" : "NO ❌") . "<br>";
        echo "Has requireLogin: " . (strpos($deptContent, '$auth->requireLogin()') !== false ? "YES ✅" : "NO ❌") . "<br>";
    }
    echo "</div>";
    ?>
    
    <div class="box warning">
        <h2>🎯 Next Steps</h2>
        <ol>
            <li>If session is empty: <a href="login.php" class="btn">Login First</a></li>
            <li>After login, <a href="?" class="btn">Refresh This Page</a></li>
            <li>If logged in, try: <a href="faculty-list.php" class="btn">Faculty (Working)</a></li>
            <li>Then try: <a href="departments-list.php" class="btn">Departments</a></li>
            <li>Then try: <a href="courses-list.php" class="btn">Courses</a></li>
        </ol>
    </div>
    
    <div class="box">
        <h2>📋 Quick Links</h2>
        <a href="login.php" class="btn">Login Page</a>
        <a href="index.php" class="btn">Dashboard</a>
        <a href="faculty-list.php" class="btn">Faculty List</a>
        <a href="departments-list.php" class="btn">Departments List</a>
        <a href="courses-list.php" class="btn">Courses List</a>
    </div>
</body>
</html>