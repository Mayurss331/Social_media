<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION["user"];

// Fetch followers
$stmt = $conn->prepare("SELECT users.username FROM followers JOIN users ON followers.follower_id = users.id WHERE followers.user_id = ?");
$stmt->execute([$user_id]);
$followers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch following
$stmt = $conn->prepare("SELECT users.username FROM followers JOIN users ON followers.user_id = users.id WHERE followers.follower_id = ?");
$stmt->execute([$user_id]);
$following = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Followers & Following</title>
</head>
<body>
    <h2>Followers</h2>
    <?php foreach ($followers as $follower): ?>
        <p><?php echo htmlspecialchars($follower["username"]); ?></p>
    <?php endforeach; ?>

    <h2>Following</h2>
    <?php foreach ($following as $followed): ?>
        <p><?php echo htmlspecialchars($followed["username"]); ?></p>
    <?php endforeach; ?>
</body>
</html>
