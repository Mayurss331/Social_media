<?php
require_once "../config/database.php";

$username = "admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $password]);

echo "Admin user created!";
?>