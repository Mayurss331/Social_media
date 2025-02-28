<?php
session_start();
try {
    if (file_exists(__DIR__ . '/../config/database.php')) {
        require_once __DIR__ . '/../config/database.php';
    } elseif (file_exists(__DIR__ . '/config/database.php')) {
        require_once __DIR__ . '/config/database.php';
    }
}

//catch exception
catch(Exception $e) {
    echo "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media</title>
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>
<nav class="navbar">
    <div class="nav-left">
        <a href="http://localhost/Paarsh/social_media/" class="logo">📌 SocialApp</a>
    </div>
    <div class="nav-center">
        <a href="http://localhost/Paarsh/social_media/">Home</a>
        <a href="http://localhost/Paarsh/social_media/user/profile.php">Profile</a>
        <a href="http://localhost/Paarsh/social_media/friends/followers.php">Friends</a>
        <a href="http://localhost/Paarsh/social_media/chat">Messages</a>
        <a href="http://localhost/Paarsh/social_media/notifications/list.php">Notifications</a>
    </div>
    <div class="nav-right">
        <form action="http://localhost/Paarsh/social_media/friends/search.php" method="GET" class="search-form">
            <input type="text" name="q" placeholder="Search...">
            <button type="submit">🔍</button>
        </form>
        <a href="http://localhost/Paarsh/social_media/auth  /logout.php" class="logout-btn">Logout</a>
    </div>
</nav>
