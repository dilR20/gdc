<?php
/**
 * Admin User Checker & Creator
 * This will check if admin user exists and create/fix it
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';

echo "<h1>Admin User Checker & Fixer</h1>";
echo "<hr>";

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>✅ Database Connection: Success</h2>";
    echo "Database: " . DB_NAME . "<br><br>";
    
    // Check if admin_users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    if ($stmt->rowCount() == 0) {
        echo "<h2>❌ Table 'admin_users' does not exist!</h2>";
        echo "<p>You need to import the database.sql file.</p>";
        exit;
    }
    
    echo "<h2>✅ Table 'admin_users': Exists</h2><br>";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<h2>✅ Admin User: Found</h2>";
        echo "<strong>Username:</strong> " . htmlspecialchars($admin['username']) . "<br>";
        echo "<strong>Full Name:</strong> " . htmlspecialchars($admin['full_name']) . "<br>";
        echo "<strong>Email:</strong> " . htmlspecialchars($admin['email']) . "<br>";
        echo "<strong>Status:</strong> " . ($admin['is_active'] ? 'Active' : 'Inactive') . "<br>";
        echo "<strong>Last Login:</strong> " . ($admin['last_login'] ?? 'Never') . "<br>";
        echo "<br>";
        
        // Check password hash
        echo "<h3>Password Hash Check:</h3>";
        echo "<strong>Current Hash:</strong> " . substr($admin['password_hash'], 0, 50) . "...<br><br>";
        
        // Test the password
        $testPassword = 'admin123';
        if (password_verify($testPassword, $admin['password_hash'])) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
            echo "<h3 style='color: #155724; margin: 0;'>✅ Password is CORRECT!</h3>";
            echo "<p style='margin: 10px 0 0 0;'>The password 'admin123' should work!</p>";
            echo "</div>";
            echo "<br>";
            echo "<p><strong>If you're still getting 'Invalid password', try:</strong></p>";
            echo "<ol>";
            echo "<li>Clear your browser cache</li>";
            echo "<li>Try in incognito/private mode</li>";
            echo "<li>Check if you're typing the password correctly (no extra spaces)</li>";
            echo "</ol>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
            echo "<h3 style='color: #721c24; margin: 0;'>❌ Password Hash is WRONG!</h3>";
            echo "<p style='margin: 10px 0 0 0;'>The current password does not match 'admin123'</p>";
            echo "</div>";
            echo "<br>";
            echo "<h3>🔧 Click button below to fix the password:</h3>";
            echo "<form method='POST'>";
            echo "<button type='submit' name='fix_password' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Fix Password (Reset to admin123)</button>";
            echo "</form>";
        }
        
    } else {
        echo "<div style='background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px;'>";
        echo "<h2 style='color: #856404; margin: 0;'>⚠️ Admin User: NOT FOUND</h2>";
        echo "<p style='margin: 10px 0 0 0;'>The admin user does not exist in the database.</p>";
        echo "</div>";
        echo "<br>";
        echo "<h3>🔧 Click button below to create admin user:</h3>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='create_admin' style='background: #007bff; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>Create Admin User</button>";
        echo "</form>";
    }
    
    // Handle password fix
    if (isset($_POST['fix_password'])) {
        $newHash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
        $stmt->execute([$newHash, 'admin']);
        
        echo "<br><div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
        echo "<h3 style='color: #155724;'>✅ Password Updated!</h3>";
        echo "<p>The password has been reset to: <strong>admin123</strong></p>";
        echo "<p><a href='login.php' style='color: #155724;'><strong>→ Go to Login Page</strong></a></p>";
        echo "</div>";
    }
    
    // Handle admin creation
    if (isset($_POST['create_admin'])) {
        $newHash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@college.edu', $newHash, 'System Administrator', 1]);
        
        echo "<br><div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
        echo "<h3 style='color: #155724;'>✅ Admin User Created!</h3>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        echo "<p><a href='login.php' style='color: #155724;'><strong>→ Go to Login Page</strong></a></p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
    echo "<h2 style='color: #721c24;'>❌ Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Need to start fresh?</h3>";
echo "<p>If nothing works, you may need to re-import the database.sql file in phpMyAdmin.</p>";
?>
