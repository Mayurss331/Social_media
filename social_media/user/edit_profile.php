<?php

require_once "../includes/header.php";

if (!isset($_SESSION["user"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user"];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = trim($_POST["bio"]);

    // Handle profile picture upload
    if (!empty($_FILES["profile_picture"]["name"])) {
        $stmt = $conn->prepare("SELECT username  FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $target_dir = "../uploads/";
        $target_file = $target_dir . $user["username"].$user_id.".jpg";
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        $stmt = $conn->prepare("UPDATE users SET bio = ?, profile_picture = ? WHERE id = ?");
        $stmt->execute([$bio, $target_file, $user_id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->execute([$bio, $user_id]);
    }

    header("Location: profile.php");
}

$stmt = $conn->prepare("SELECT bio, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>


    <div class="profile-container">
    <h2>Edit Profile</h2>
    <img src="../uploads/<?php echo $user['profile_picture']; ?>" width="150" height="150" alt="Profile Picture">
    <form method="POST" enctype="multipart/form-data">
        <textarea name="bio" placeholder="Write your bio here..."><?php echo htmlspecialchars($user['bio']); ?></textarea><br>
        <label>Profile Picture:</label>
        <input type="file" name="profile_picture"><br>
        <button type="submit" class="btn">Save Changes</button>
    </form>
    <a href="profile.php">Back to Profile</a>
    </div>
    <link rel="stylesheet" href="../assets/css/profile.css">
</body>
</html>
