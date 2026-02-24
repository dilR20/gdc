<?php
/**
 * Redirect Diagnostic Tool
 * This will show you what's causing the double /admin/admin/ redirect
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Redirect Diagnostic Tool</h1>";
echo "<hr>";

// Test 1: Show current URL info
echo "<h2>Test 1: Current URL Information</h2>";
echo "<strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "<strong>Current Dir:</strong> " . __DIR__ . "<br>";
echo "<br>";

// Test 2: Check config.php settings
echo "<h2>Test 2: Config.php Settings</h2>";
require_once '../config/config.php';

echo "<strong>SITE_URL:</strong> " . (defined('SITE_URL') ? SITE_URL : 'NOT DEFINED') . "<br>";
echo "<strong>DB_NAME:</strong> " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "<br>";
echo "<br>";

// Test 3: Check for .htaccess redirects
echo "<h2>Test 3: Check for .htaccess Files</h2>";

$locations = [
    __DIR__ => 'Current directory (admin/)',
    dirname(__DIR__) => 'Parent directory (website/)',
];

foreach ($locations as $dir => $name) {
    $htaccess = $dir . '/.htaccess';
    if (file_exists($htaccess)) {
        echo "<div style='background: #fff3cd; padding: 10px; margin: 10px 0; border-left: 4px solid #ffc107;'>";
        echo "<strong>⚠️ Found .htaccess in: $name</strong><br>";
        echo "<strong>Location:</strong> $htaccess<br>";
        echo "<strong>Contents:</strong><br>";
        echo "<pre style='background: #f8f9fa; padding: 10px; overflow-x: auto;'>";
        echo htmlspecialchars(file_get_contents($htaccess));
        echo "</pre>";
        echo "</div>";
    } else {
        echo "✅ No .htaccess in: $name<br>";
    }
}
echo "<br>";

// Test 4: Check session redirect
echo "<h2>Test 4: Session Information</h2>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo "<strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";
echo "<strong>Admin Logged In:</strong> " . (isset($_SESSION['admin_id']) ? 'Yes (ID: ' . $_SESSION['admin_id'] . ')' : 'No') . "<br>";
echo "<br>";

// Test 5: Check login.php for redirects
echo "<h2>Test 5: Checking login.php for Redirect Code</h2>";
$loginFile = __DIR__ . '/login.php';
if (file_exists($loginFile)) {
    $content = file_get_contents($loginFile);
    
    // Look for header redirects
    if (preg_match_all("/header\s*\(\s*['\"]Location:\s*([^'\"]+)['\"]/i", $content, $matches)) {
        echo "<strong>Found redirects in login.php:</strong><br>";
        echo "<ul>";
        foreach ($matches[1] as $redirect) {
            $style = (strpos($redirect, 'admin') !== false) ? 'color: red; font-weight: bold;' : 'color: green;';
            echo "<li style='$style'>Location: " . htmlspecialchars($redirect) . "</li>";
        }
        echo "</ul>";
        
        // Check for problematic redirects
        foreach ($matches[1] as $redirect) {
            if (strpos($redirect, 'admin/') !== false || strpos($redirect, '/admin') !== false) {
                echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 10px 0;'>";
                echo "<strong>❌ PROBLEM FOUND!</strong><br>";
                echo "Redirect contains 'admin' path: <code>" . htmlspecialchars($redirect) . "</code><br>";
                echo "<strong>This is causing the double /admin/admin/ issue!</strong><br>";
                echo "</div>";
            }
        }
    } else {
        echo "✅ No problematic redirects found in login.php<br>";
    }
} else {
    echo "❌ login.php not found!<br>";
}
echo "<br>";

// Solution
echo "<h2>📋 Solution</h2>";
echo "<div style='background: #d1ecf1; padding: 15px; border-left: 4px solid #0c5460;'>";
echo "<strong>To fix the redirect issue:</strong><br><br>";
echo "1. <strong>Download login-corrected.php</strong> (I've provided this)<br>";
echo "2. <strong>Rename it to login.php</strong><br>";
echo "3. <strong>Replace your current login.php</strong> with it<br>";
echo "4. <strong>Clear browser cache</strong> or use incognito mode<br>";
echo "5. <strong>Try again!</strong><br>";
echo "</div>";

echo "<hr>";
echo "<h3>Quick Test Links:</h3>";
echo "<ul>";
echo "<li><a href='login.php'>Go to login.php</a></li>";
echo "<li><a href='check-admin.php'>Go to check-admin.php</a></li>";
echo "<li><a href='../'>Go to website root</a></li>";
echo "</ul>";
?>
