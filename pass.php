<?php
$password = 'admin123'; // Change this to your desired password
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br><br>";
echo "Copy this hash to the database password_hash column";
?>