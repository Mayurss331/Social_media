<?php
require_once "../config/database.php";

if (!isset($_GET["id"])) {
    die("Invalid profile!");
}

$user_id = $_GET["id"];
$stmt = $conn->prepare("SELECT username, bio, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($user['username']); ?>'s Profile</h2>
    <img src="../uploads/<?php echo $user['profile_picture']; ?>" width="150" height="150" alt="Profile Picture">
    <p>Bio: <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
    <a href="../index.php">Back to Home</a>
</body>
</html>
