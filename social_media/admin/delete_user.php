<?php
require_once "../config/database.php";

if (!isset($_GET["id"])) {
    die("User ID missing!");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$_GET["id"]]);

header("Location: dashboard.php");
?>
