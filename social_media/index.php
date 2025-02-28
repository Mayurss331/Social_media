<?php include 'includes/header.php'; 

// Check if user is logged in
if (!isset($_SESSION["user"])) {
    header("Location: auth/login.php");
    exit();
}

// Get logged-in user ID
$user_id = $_SESSION["user"];

// Fetch latest posts from self and friends
$stmt = $conn->prepare("
    SELECT posts.*, users.username, users.profile_picture 
    FROM posts
    JOIN users ON posts.user_id = users.id
    WHERE posts.user_id = :user_id 
       OR posts.user_id IN (
            SELECT CASE 
                     WHEN sender_id = :user_id THEN receiver_id 
                     ELSE sender_id 
                   END 
            FROM friends 
            WHERE (sender_id = :user_id OR receiver_id = :user_id) 
              AND status = 'accepted'
        )
    ORDER BY posts.created_at DESC
");
$stmt->execute(["user_id" => $user_id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch featured posts with post details
$stmt = $conn->prepare("SELECT 
                                fp.id AS featured_id, 
                                p.id AS post_id, 
                                p.user_id, 
                                u.username,  -- Fetch username from users table
                                p.content, 
                                p.media, 
                                p.created_at 
                            FROM featured_posts fp
                            JOIN posts p ON fp.post_id = p.id
                            JOIN users u ON p.user_id = u.id  -- Join users table
                            ORDER BY fp.created_at DESC 
                            LIMIT 5");
$stmt->execute();

    // Fetch all results properly
$featured_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Fetch top 5 trending hashtags
$stmt = $conn->prepare("SELECT tag, COUNT(*) AS count FROM hashtags GROUP BY tag ORDER BY count DESC LIMIT 5");
$stmt->execute();
$trending_tags = $stmt->fetchAll();



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Social Media</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Swiper.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Swiper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

</head>
<body>
<div class="main-container">
    <!-- Left: Swiper Slider -->
    <div class="slider-container">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <?php foreach ($featured_posts as $featured): ?>
                    <div class="swiper-slide">
                        <img src="uploads/<?= $featured['media'] ?>" alt="Featured Image">
                        <div class="slide-caption">
                            <h3><?= htmlspecialchars($featured['username']) ?></h3>
                            <p><?= htmlspecialchars($featured['content']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Swiper Pagination & Navigation -->
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
        
        <div class="trending-tags">
            <h3>Trending Hashtags</h3>
            <ul>
                <?php foreach ($trending_tags as $tag): ?>
                    <li><a href="search.php?tag=<?= urlencode($tag['tag']) ?>">#<?= htmlspecialchars($tag['tag']) ?></a> (<?= $tag['count'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Right: Latest Posts -->
    <div class="user-feed">
        <h2>Latest Posts</h2>

        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <img src="uploads/<?= $post['profile_picture'] ?>" alt="Profile" class="profile-pic">
                    <strong><?= htmlspecialchars($post['username']) ?></strong>
                    <small><?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></small>
                </div>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                <?php if ($post['media']): ?>
                    <div class="post-media">
                        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $post['media'])): ?>
                            <img src="uploads/<?= $post['media'] ?>" alt="Post Image">
                        <?php elseif (preg_match('/\.(mp4|mov|avi)$/i', $post['media'])): ?>
                            <video controls>
                                <source src="uploads/<?= $post['media'] ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="post-actions">
                    <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
                        $stmt->execute([$post['id']]);
                        $like_count = $stmt->fetchColumn();
                    ?>
                    <a href="posts/like.php?post_id=<?= $post['id'] ?>">
                        <i class="fas fa-thumbs-up"></i>&nbsp<?php echo $like_count;?>
                    </a>
                    <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
                        $stmt->execute([$post['id']]);
                        $comment_c = $stmt->fetchColumn();
                    ?>
                    <a href="#" class="comment-btn" data-post-id="<?= $post['id']; ?>">
                        <i class="fas fa-comment"></i> <?php echo  $comment_c; ?>
                    </a>
                </div>

                
            </div>
            

            <?php endforeach; ?>
        </div>
    </div>
    <a href="posts/create_post.php" class="floating-btn">
        <i class="fas fa-plus"></i>
    </a>

    <div id="commentModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
                <h2>Add a Comment</h2>
                <form id="commentForm">
                    <input type="hidden" name="post_id" id="post_id">
                    <textarea name="comment_text" id="comment_text" placeholder="Write your comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("commentModal");
    const closeBtn = document.querySelector(".close");
    const commentBtns = document.querySelectorAll(".comment-btn");
    const commentForm = document.getElementById("commentForm");
    const postIdInput = document.getElementById("post_id");

    commentBtns.forEach(btn => {
        btn.addEventListener("click", function (event) {
            event.preventDefault();
            let postId = this.getAttribute("data-post-id");
            postIdInput.value = postId;
            modal.style.display = "flex";
        });
    });

    closeBtn.addEventListener("click", function () {
        modal.style.display = "none";
    });

    window.addEventListener("click", function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });

    commentForm.addEventListener("submit", function (event) {
        event.preventDefault();
        let formData = new FormData(commentForm);

        fetch("posts/comment.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            alert("Comment added successfully!");
            modal.style.display = "none";
            commentForm.reset();
        })
        .catch(error => console.error("Error:", error));
    });
});
</script>

    <script>
document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".mySwiper", {
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
});
</script>


<?php include 'includes/footer.php'; ?>
</body>
</html>



