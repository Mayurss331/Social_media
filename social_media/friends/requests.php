<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION["user"];

$stmt = $conn->prepare("
    SELECT friends.id, users.username 
    FROM friends 
    JOIN users ON friends.sender_id = users.id 
    WHERE friends.receiver_id = ? AND friends.status = 'pending'
");
$stmt->execute([$user_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/requests.css">
</head>
<body>

<div class="requests-container">
    <h2>Friend Requests</h2>
    
    <?php if (count($requests) > 0): ?>
        <?php foreach ($requests as $request): ?>
            <div class="friend-request">
                <i class="fas fa-user"></i>
                <p><?php echo htmlspecialchars($request["username"]); ?> sent you a friend request.</p>
                <form method="POST" action="manage_requests.php">
                    <input type="hidden" name="request_id" value="<?php echo $request["id"]; ?>">
                    <button type="submit" name="action" value="accepted" class="accept-btn">Accept</button>
                    <button type="submit" name="action" value="rejected" class="reject-btn">Reject</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-requests">No pending friend requests.</p>
    <?php endif; ?>

</div>

</body>
</html>

