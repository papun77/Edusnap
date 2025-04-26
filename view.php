<?php
include 'db.php';
session_start();

if (!isset($_GET['post_id'])) {
    echo "No post ID specified.";
    exit;
}

$post_id = intval($_GET['post_id']);

// Fetch post details from the database
$stmt = $conn->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Post not found.";
    exit;
}

$post = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Post</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 p-6">

    <div class="max-w-xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4">
            <h2 class="text-xl font-bold mb-2">@<?php echo htmlspecialchars($post['username']); ?></h2>
            <p class="mb-4"><?php echo htmlspecialchars($post['caption']); ?></p>

            <video controls class="w-full rounded">
                <source src="uploads/<?php echo htmlspecialchars($post['video']); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>

</body>
</html>