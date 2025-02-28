<?php
session_start();
require_once "../config/database.php";
require_once "../includes/functions.php"; // Include notification function


if (!isset($_SESSION["user"]) || !isset($_POST["receiver_id"])) {
    die("Unauthorized access!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user'];
    $receiver_id = $_POST['receiver_id'];
    if ($sender_id == $receiver_id) {
        die("You cannot send a friend request to yourself!");
    }

    // Check if the friend request already exists
    $stmt = $conn->prepare("SELECT * FROM friends WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$sender_id, $receiver_id]);
    
    if ($stmt->fetch()) {
        echo "Friend request already sent!";
        exit;
    }

    // Insert new friend request
    $stmt = $conn->prepare("INSERT INTO friends (sender_id, receiver_id, status, created_at) VALUES (?, ?, 'pending', NOW())");
    if ($stmt->execute([$sender_id, $receiver_id])) {
        sendNotification($conn, $receiver_id, $sender_id, 'friend_request');
        echo "Friend request sent successfully!";
    } else {
        echo "Error sending friend request.";
    }
}








?>