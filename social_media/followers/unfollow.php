<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"]) || !isset($_POST["user_id"])) {
    die("Unauthorized access!");
}

$follower_id = $_SESSION["user"];
$user_id = $_POST["user_id"];

$stmt = $conn->prepare("DELETE FROM followers WHERE user_id = ? AND follower_id = ?");
$stmt->execute([$user_id, $follower_id]);

echo "Unfollowed!";
?>
