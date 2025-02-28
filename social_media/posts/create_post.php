<?php
require_once "../includes/header.php";

// Check if the user is logged in
if (!isset($_SESSION["user"])) {
    die("Unauthorized access!");
}
function extractHashtags($text) {
    preg_match_all('/#(\w+)/', $text, $matches);
    return $matches[1];
}
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // CSRF Protection: Validate Token
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("CSRF validation failed!");
    }

    // Get the content and sanitize it to prevent XSS
    $content = htmlspecialchars($_POST["content"], ENT_QUOTES, 'UTF-8');

    // Handle file upload (for images/videos)
    $media = NULL;
    if (!empty($_FILES["media"]["name"])) {
        $target_dir = "../uploads/Posts/".$_SESSION["user"]."/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $media = $target_dir . basename($_FILES["media"]["name"]);
        
        // Validate file type (Allow only images/videos)
        $file_type = strtolower(pathinfo($media, PATHINFO_EXTENSION));
        $allowed_types = ["jpg", "jpeg", "png", "gif", "mp4", "mov", "avi"];

        if (!in_array($file_type, $allowed_types)) {
            die("Invalid file type!");
        }

        // Move the uploaded file
        if (!move_uploaded_file($_FILES["media"]["tmp_name"], $media)) {
            die("File upload failed!");
        }
    }

    // Insert post into database using prepared statements
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, media, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$_SESSION["user"], $content, $media]);
    $post_id = $conn->lastInsertId();
    $hashtags = extractHashtags($content);
     // Store hashtags
     foreach ($hashtags as $tag) {
        $stmt = $conn->prepare("INSERT INTO hashtags (tag, post_id) VALUES (?, ?)");
        $stmt->execute([$tag, $post_id]);
    }



    // Redirect to homepage after posting
    header("Location: ../");
    exit();
}
?>
<div class="post-card">
    <h2>Create a Post</h2>
    <form method="POST" action="create_post.php" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <textarea name="content" placeholder="What's on your mind?" required></textarea>

        <!-- File Upload -->
        <label for="media-upload" class="upload-btn">
            <i class="fas fa-image"></i> Add Photo/Video
        </label>
        <input type="file" id="media-upload" name="media" accept="image/*,video/*" hidden onchange="previewMedia(event)">

        <!-- Image/Video Preview -->
        <div class="preview-container" id="preview-container">
            <img id="image-preview" style="display: none;">
            <video id="video-preview" style="display: none;" controls></video>
        </div>

        <button type="submit" class="post-btn">Post</button>
    </form>
</div>
<script>
function previewMedia(event) {
    const file = event.target.files[0];
    const imagePreview = document.getElementById("image-preview");
    const videoPreview = document.getElementById("video-preview");
    const previewContainer = document.getElementById("preview-container");

    if (!file) return;

    const fileType = file.type.split('/')[0];

    if (fileType === "image") {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            imagePreview.style.display = "flex";
            videoPreview.style.display = "none";
        };
        reader.readAsDataURL(file);
    } else if (fileType === "video") {
        const reader = new FileReader();
        reader.onload = function(e) {
            videoPreview.src = e.target.result;
            videoPreview.style.display = "flex";
            imagePreview.style.display = "none";
        };
        reader.readAsDataURL(file);
    }

    previewContainer.style.display = "flex";
}

</script>
<link rel="stylesheet" href="../assets/css/createpost.css">


