<?php
include '../includes/header.php'; // Database connection

if (!isset($_GET['q']) || empty($_GET['q'])) {
    echo "<p>Please enter a username to search.</p>";
    exit;
}

$searchTerm = '%' . $_GET['q'] . '%'; // For partial match search
$currentUserId = $_SESSION['user'];

$stmt = $conn->prepare("SELECT id, username, profile_picture FROM users WHERE username LIKE ? AND id != ?");
$stmt->execute([$searchTerm, $currentUserId]);
$users = $stmt->fetchAll();

if (!$users) {
    echo "<p>No users found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="../assets/css/search.css">
</head>
<body>
    <div class="search-results">
        <h2>Search Results</h2>
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <img src="../uploads/<?php echo $user['profile_picture']; ?>" alt="Profile Picture">
                <p><?php echo htmlspecialchars($user['username']); ?></p>
                <?php
                // Check if friend request is already sent
                $stmt = $conn->prepare("SELECT * FROM friends WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)");
                $stmt->execute([$currentUserId, $user['id'], $user['id'], $currentUserId]);
                $friendship = $stmt->fetch();

                if (!$friendship): ?>
                    <form action="send_request.php" method="POST">
                        <input type="hidden" name="receiver_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" class="friend-request-btn">Send Friend Request</button>
                    </form>
                <?php else: ?>
                    <button class="friend-request-btn disabled">Request Sent</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
