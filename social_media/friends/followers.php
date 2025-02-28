<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION["user"];

// Fetch friends where the logged-in user is either sender or receiver
$stmt = $conn->prepare("
    SELECT users.id, users.username, users.profile_picture 
    FROM friends 
    JOIN users ON (friends.sender_id = users.id OR friends.receiver_id = users.id) 
    WHERE (friends.sender_id = ? OR friends.receiver_id = ?) AND friends.status = 'accepted' AND users.id != ?
");
$stmt->execute([$user_id, $user_id, $user_id]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends List</title>
    <link rel="stylesheet" href="../assets/css/friends.css">
</head>
<body>

<div class="friends-container">
    <h2>Your Friends</h2>
    <?php if (count($friends) > 0): ?>
        <div class="friends-list">
            <?php foreach ($friends as $friend): ?>
                <div class="friend">
                    <img src="../uploads/<?php echo $friend['profile_picture'] ?: 'default_profile.jpg'; ?>" alt="Friend">
                    <p><?php echo htmlspecialchars($friend['username']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No friends yet. Start connecting!</p>
    <?php endif; ?>
</div>

</body>
</html>
