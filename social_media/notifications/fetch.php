session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user"])) {
    die("0");
}

$user_id = $_SESSION["user"];

$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = FALSE");
$stmt->execute([$user_id]);
$unread_count = $stmt->fetchColumn();

echo $unread_count;
?>
