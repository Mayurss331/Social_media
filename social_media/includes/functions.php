
<?php
function sendNotification($conn, $user_id, $sender_id, $type, $post_id = NULL) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, sender_id, type, post_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $sender_id, $type, $post_id]);
}
?>