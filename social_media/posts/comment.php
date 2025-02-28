<?php 
require_once "../includes/functions.php"; // Include notification function

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert comment record
    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment_text) VALUES (?, ?, ?)");
    $stmt->execute([$_GET['post_id'], $_SESSION['user'], $_GET['comment_text']]);

    // Get post owner
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->execute([$_GET['post_id']]);
    $post_owner = $stmt->fetchColumn();

    // Send notification
    if ($post_owner != $_SESSION['user']) {
        sendNotification($conn, $post_owner, $user_id, 'comment', $post_id);
    }

    echo "Comment added!";
}
?>