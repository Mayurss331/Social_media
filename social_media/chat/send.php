<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"]) || !isset($_POST["receiver_id"]) || !isset($_POST["message"])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION["user"];
$receiver_id = $_POST["receiver_id"];
$message = trim($_POST["message"]);

if ($message !== "") {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $receiver_id, $message]);
}

echo "Message sent!";
?>
