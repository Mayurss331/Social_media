require_once "../includes/functions.php"; // Include notification function

// Insert follow record
$stmt = $conn->prepare("INSERT INTO followers (user_id, follower_id) VALUES (?, ?)");
$stmt->execute([$user_id, $follower_id]);

// Send notification
sendNotification($conn, $user_id, $follower_id, 'follow');

echo "Now following!";
