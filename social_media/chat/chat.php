<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"]) || !isset($_GET["user"])) {
    die("Unauthorized access!");
}

$user_id = $_SESSION["user"];
$receiver_id = $_GET["user"];

// Fetch receiver details
$stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$receiver_id]);
$receiver = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch messages
$stmt = $conn->prepare("
    SELECT sender_id, message, created_at 
    FROM messages 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
    ORDER BY created_at ASC
");
$stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat with <?php echo htmlspecialchars($receiver['username']); ?></title>
    <link rel="stylesheet" href="../assets/css/chat.css">
    <script src="chat.js" defer></script>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        <img src="../uploads/<?php echo $receiver['profile_picture'] ?: 'default_profile.jpg'; ?>" alt="Profile">
        <h2><?php echo htmlspecialchars($receiver['username']); ?></h2>
    </div>

    <div id="chat-box">
        <?php foreach ($messages as $message): ?>
            <div class="chat-message <?php echo ($message['sender_id'] == $user_id) ? 'sent' : 'received'; ?>">
                <p><?php echo htmlspecialchars($message['message']); ?></p>
                <span class="chat-time"><?php echo date("h:i A", strtotime($message['created_at'])); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <form id="chat-form">
        <input type="hidden" id="receiver_id" value="<?php echo $receiver_id; ?>">
        <input type="text" id="message" placeholder="Type a message..." autocomplete="off">
        <button type="submit">Send</button>
    </form>
</div>
<a class="back-button" href="http://localhost/Paarsh/social_media/chat/"></a>



</body>
</html>
