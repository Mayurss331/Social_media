<?php
require_once "../includes/header.php";

if (!isset($_SESSION["user"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user"];
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch accepted friends
$friends_stmt = $conn->prepare("SELECT users.id, users.username, users.profile_picture FROM friends 
    JOIN users ON (friends.sender_id = users.id OR friends.receiver_id = users.id) 
    WHERE (friends.sender_id = ? OR friends.receiver_id = ?) AND friends.status = 'accepted' AND users.id != ?");
$friends_stmt->execute([$user_id, $user_id, $user_id]);
$friends = $friends_stmt->fetchAll();

// Fetch user posts
$posts_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$posts_stmt->execute([$user_id]);
$posts = $posts_stmt->fetchAll();
?>

<div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img class="cover-photo" src="../uploads/default_banner.jpg" alt="Cover Photo">
            <!-- <img class="cover-photo" src="../uploads/<?php #echo $user['cover_photo'] ?: 'default_banner.jpg'; ?>" alt="Cover Photo"> -->
            <div class="profile-details">
                <img class="profile-picture" src="../uploads/<?php echo $user['profile_picture'] ?: 'default_profile.jpg'; ?>" alt="Profile Picture">
                <div class="details">
                <h2>UserName:   &nbsp<?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="bio">BIO: &nbsp  <?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
                </div>
                <div class="redirect-btn">
                <a href="edit_profile.php" class="btn">Edit Profile</a>
                </div>
            </div>
        </div>

        
    <div class="inner-container">
        <div class="posts-container">
    <h3>Your Posts</h3>
    <div class="posts-grid">
        <?php
        $posts_stmt = $conn->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
        $posts_stmt->execute([$user_id]);
        $posts = $posts_stmt->fetchAll();

        if (count($posts) > 0):
            foreach ($posts as $post):
        ?>
                <div class="post">
                    <?php if ($post['media']): ?>
                        <img src="../uploads/<?php echo $post['media']; ?>" alt="Post Image">
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                </div>
        <?php
            endforeach;
        else:
            echo "<p>No posts yet.</p>";
        endif;
        ?>
        </div>
        </div>
        <!-- Friends List -->
        <div class="friends-list">
                <h3>Friends</h3>
                <div class="friends">
                    <?php foreach ($friends as $friend): ?>
                        <div class="friend">
                            <img src="../uploads/<?php echo $friend['profile_picture'] ?: 'default_profile.jpg'; ?>" alt="Friend">
                            <p><?php echo htmlspecialchars($friend['username']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- View All Button -->
            <div class="view-all-container">
                <a href="http://localhost/Paarsh/social_media/friends/followers.php" class="view-all-btn">View All</a>
            </div>
        </div>


    
    
    <link rel="stylesheet" href="../assets/css/profile.css">
</body>

</html>
