<?php
/**
 * Simple Login - No Redirects
 * Use this to test login without any redirect issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
$success = false;

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $message = '❌ Please enter username and password';
    } else {
        // Connect to database
        try {
            require_once '../config/config.php';
            
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Check user
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                session_start();
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_name'] = $user['full_name'];
                $_SESSION['last_activity'] = time();
                
                $message = '✅ Login successful!';
                $success = true;
            } else {
                $message = '❌ Invalid username or password';
            }
            
        } catch (PDOException $e) {
            $message = '❌ Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Login Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .links {
            margin-top: 20px;
            text-align: center;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>🔧 Simple Login Test</h1>
        <p style="text-align: center; color: #666;">No redirects - just testing</p>
        
        <?php if ($message): ?>
        <div class="message <?php echo $success ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div style="text-align: center;">
            <p><strong>✅ Login worked!</strong></p>
            <p>Now you can try the regular login page:</p>
            <div class="links">
                <a href="login.php">→ Go to Regular Login</a><br>
                <a href="index.php">→ Go to Dashboard</a>
            </div>
        </div>
        <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div class="links">
            <p style="margin-top: 20px; font-size: 14px; color: #666;">
                Default credentials:<br>
                <strong>admin</strong> / <strong>admin123</strong>
            </p>
            <a href="check-admin.php">Check/Fix Admin User</a> |
            <a href="check-redirect.php">Check Redirects</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
