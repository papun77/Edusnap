<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin']; // assuming you store this in session
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

// Fetch the post to verify ownership
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $message = "Post not found.";
} else {
    $post = $result->fetch_assoc();
    
    if ($post['user_id'] == $user_id || $is_admin) {
        // Delete video file
        $video_path = "uploads/" . $post['video_path'];
        if (file_exists($video_path)) {
            unlink($video_path);
        }

        // Delete thumbnail if exists
        $thumb = "thumbnails/" . pathinfo($post['video_path'], PATHINFO_FILENAME) . ".jpg";
        if (file_exists($thumb)) {
            unlink($thumb);
        }

        // Delete from database
        $deleteStmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $deleteStmt->bind_param("i", $post_id);
        $deleteStmt->execute();

        $message = "Post deleted successfully.";
    } else {
        $message = "You do not have permission to delete this post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Post - Edusnap</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta http-equiv="refresh" content="2;url=index.php" />
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white shadow-lg rounded-lg p-6 max-w-md w-full text-center">
    <h2 class="text-2xl font-semibold text-red-600 mb-2">Delete Post</h2>
    <p class="text-gray-700 text-lg"><?php echo $message; ?></p>
    <p class="text-sm text-gray-500 mt-2">Redirecting to homepage...</p>
  </div>
</body>
</html>
