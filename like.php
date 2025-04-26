<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

$liked = false;

// Check if the like already exists
$check = $conn->prepare("SELECT * FROM likes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    // Unlike
    $delete = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $delete->bind_param("ii", $user_id, $post_id);
    $delete->execute();
    $message = "You unliked the post.";
} else {
    // Like
    $insert = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $insert->bind_param("ii", $user_id, $post_id);
    $insert->execute();
    $liked = true;
    $message = "You liked the post!";
}

// Optional: Show success UI for 1.5 seconds then redirect
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edusnap - Like</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta http-equiv="refresh" content="1.5;url=index.php" />
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded-lg shadow-lg text-center max-w-sm w-full">
    <h2 class="text-2xl font-bold mb-4 text-blue-700">Edusnap</h2>
    <p class="text-lg text-gray-800 mb-2">
      <?php echo $message; ?>
    </p>
    <p class="text-sm text-gray-500">Redirecting to feed...</p>
  </div>
</body>
</html>
