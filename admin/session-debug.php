<?php
// DEBUG: Check session and authentication
session_start();

echo "<h1>Session Debug Info</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n\n";

echo "SESSION VARIABLES:\n";
print_r($_SESSION);

echo "\n\nCONFIG FILE EXISTS: ";
echo file_exists('../config/config.php') ? 'YES' : 'NO';

echo "\n\nAUTH FILE EXISTS: ";
echo file_exists('../includes/Auth.php') ? 'YES' : 'NO';

echo "\n\nDATABASE FILE EXISTS: ";
echo file_exists('../includes/Database.php') ? 'YES' : 'NO';

// Try loading Auth
require_once '../config/config.php';
require_once '../includes/Auth.php';

$auth = new Auth();

echo "\n\nAuth object created: YES";
echo "\nisLoggedIn(): " . ($auth->isLoggedIn() ? 'TRUE' : 'FALSE');
echo "\ngetAdminId(): " . ($auth->getAdminId() ?? 'NULL');

echo "</pre>";

echo "<hr>";
echo "<a href='login.php'>Go to Login</a> | ";
echo "<a href='index.php'>Go to Dashboard</a> | ";
echo "<a href='faculty-list.php'>Go to Faculty (Working)</a>";
?>