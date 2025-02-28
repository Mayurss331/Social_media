<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"]) || !isset($_POST["request_id"]) || !isset($_POST["action"])) {
    die("Unauthorized access!");
}

$request_id = $_POST["request_id"];
$action = $_POST["action"];

if ($action !== "accepted" && $action !== "rejected") {
    die("Invalid action!");
}

// Update friend request status
$stmt = $conn->prepare("UPDATE friends SET status = ? WHERE id = ?");
$stmt->execute([$action, $request_id]);

echo "Friend request $action!";
?>
