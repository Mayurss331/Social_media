<?php
require_once "../config/database.php";

if (!isset($_GET["id"])) {
    die("User ID missing!");
}

$stmt = $conn->prepare("UPDATE users SET banned = 1 WHERE id = ?");
$stmt->execute([$_GET["id"]]);

header("Location: dashboard.php");
?>
