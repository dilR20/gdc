<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

$hosts = ['localhost', '127.0.0.1', '::1'];
$db = 'college_db';
$user = 'root';
$pass = '';

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Host</th><th>Status</th><th>Message</th></tr>";

foreach ($hosts as $host) {
    echo "<tr>";
    echo "<td><strong>$host</strong></td>";
    
    try {
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        
        echo "<td style='background: #d4edda; color: #155724;'><strong>✅ SUCCESS</strong></td>";
        echo "<td>Connection established!</td>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM homepage_categories");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "</tr>";
        echo "<tr><td colspan='3'>Categories found: {$result['count']}</td></tr>";
        
    } catch (PDOException $e) {
        echo "<td style='background: #f8d7da; color: #721c24;'><strong>❌ FAILED</strong></td>";
        echo "<td>" . htmlspecialchars($e->getMessage()) . "</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<hr>";
echo "<h2>Recommendation:</h2>";
echo "<p>Use the host that shows <strong>✅ SUCCESS</strong> in your config.php file.</p>";
echo "<p>Update this line in <code>config/config.php</code>:</p>";
echo "<pre>define('DB_HOST', 'SUCCESSFUL_HOST_HERE');</pre>";
?>