<?php
/**
 * Login Debug Script
 * This will show you exactly what's wrong
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Login Debug Test</h1>";
echo "<hr>";

// Test 1: PHP is working
echo "<h2>Test 1: PHP is working</h2>";
echo "✅ PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br><br>";

// Test 2: Can we find config.php?
echo "<h2>Test 2: Finding config.php</h2>";
$config_path = '../config/config.php';
if (file_exists($config_path)) {
    echo "✅ config.php found at: $config_path<br>";
    
    // Try to include it
    try {
        require_once $config_path;
        echo "✅ config.php loaded successfully<br>";
        
        // Check if constants are defined
        if (defined('DB_HOST')) {
            echo "✅ DB_HOST defined: " . DB_HOST . "<br>";
        }
        if (defined('DB_NAME')) {
            echo "✅ DB_NAME defined: " . DB_NAME . "<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error loading config.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ config.php NOT found at: $config_path<br>";
    echo "Current directory: " . __DIR__ . "<br>";
}
echo "<br>";

// Test 3: Can we find Auth.php?
echo "<h2>Test 3: Finding Auth.php</h2>";
$auth_path = '../includes/Auth.php';
if (file_exists($auth_path)) {
    echo "✅ Auth.php found at: $auth_path<br>";
    
    // Try to include it
    try {
        require_once $auth_path;
        echo "✅ Auth.php loaded successfully<br>";
        
        // Try to create Auth object
        $auth = new Auth();
        echo "✅ Auth object created successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error with Auth.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Auth.php NOT found at: $auth_path<br>";
}
echo "<br>";

// Test 4: Session test
echo "<h2>Test 4: Session Test</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✅ Session started<br>";
} else {
    echo "✅ Session already active<br>";
}
echo "Session ID: " . session_id() . "<br><br>";

// Test 5: Database connection
echo "<h2>Test 5: Database Connection</h2>";
if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER')) {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            defined('DB_PASS') ? DB_PASS : ''
        );
        echo "✅ Database connection successful<br>";
    } catch (PDOException $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "⚠️ Database constants not defined<br>";
}
echo "<br>";

// Test 6: List all files in admin directory
echo "<h2>Test 6: Files in admin directory</h2>";
$files = scandir(__DIR__);
echo "<pre>";
print_r($files);
echo "</pre>";

echo "<hr>";
echo "<h2>Conclusion</h2>";
echo "If all tests pass above, the issue is in login.php code itself.<br>";
echo "Check the errors above to see what's failing.<br>";
?>
