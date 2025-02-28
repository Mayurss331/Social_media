<?php
require_once "../includes/functions.php"; // Include notification function
require_once "../includes/header.php"; // Include notification function

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $stmt = $conn->prepare("Select * From likes WHERE  post_id=? and user_id = ? ");
    $stmt->execute([$_GET["post_id"], $_SESSION['user']]);
    $cond= $stmt->fetchColumn();

    if($cond!=NULL){
        $stmt = $conn->prepare("DELETE FROM likes WHERE  `post_id`=? and `user_id` = ? ");
        $stmt->execute([$_GET["post_id"], $_SESSION['user']]);
        echo "Disliked!";

    }
    else{

        // Insert like record
        $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
        $stmt->execute([$_GET["post_id"], $_SESSION['user']]);
        // Get post owner
        $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$_GET["post_id"]]);
        $post_owner = $stmt->fetchColumn();

        // Send notification
        if ($post_owner != $_SESSION['user']) {
            sendNotification($conn, $post_owner, $_SESSION['user'], 'like', $post_id);
        }

        echo "Liked!";
    }
    header("Location: ../");


}
?>