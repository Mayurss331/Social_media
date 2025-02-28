<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

// Fetch users
$users = $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

// Fetch posts
$posts = $conn->query("SELECT * FROM posts")->fetchAll(PDO::FETCH_ASSOC);

// Fetch reports
$reports = $conn->query("SELECT * FROM reports")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <h2>Admin Dashboard</h2>
    <div style="text-align:center;">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="dashboard-section">
        <h3>Users</h3>
        <div class="table-container">
            <table>
                <tr><th>ID</th><th>Username</th><th>Email</th><th>Actions</th></tr>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <a href="ban_user.php?id=<?php echo $user['id']; ?>">Ban</a> |
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="dashboard-section">
        <h3>Posts</h3>
        <div class="table-container">
            <table>
                <tr><th>ID</th><th>User ID</th><th>Content</th><th>Actions</th></tr>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo $post['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($post['content']); ?></td>
                        <td><a href="delete_post.php?id=<?php echo $post['id']; ?>">Delete</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <div class="dashboard-section">
        <h3>Reports</h3>
        <div class="table-container">
            <table>
                <tr><th>ID</th><th>Reported By</th><th>Content ID</th><th>Reason</th><th>Actions</th></tr>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo $report['id']; ?></td>
                        <td><?php echo $report['user_id']; ?></td>
                        <td><?php echo $report['content_id']; ?></td>
                        <td><?php echo htmlspecialchars($report['reason']); ?></td>
                        <td>
                            <a href="delete_post.php?id=<?php echo $report['content_id']; ?>">Delete Post</a> |
                            <a href="delete_user.php?id=<?php echo $report['user_id']; ?>">Ban User</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
