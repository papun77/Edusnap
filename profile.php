<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Edusnap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto py-8">
        <div class="bg-white p-6 rounded-md shadow-lg">
            <h1 class="text-2xl font-semibold mb-4">Welcome, <?php echo $user['username']; ?></h1>
            <p>Email: <?php echo $user['email']; ?></p>

            <h2 class="text-xl font-semibold mt-4">Your Posts</h2>
            <?php
            $posts = $conn->query("SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC");
            while ($post = $posts->fetch_assoc()) {
                echo "<div class='mt-4 bg-white p-4 rounded-md shadow-md'>
                    <video class='w-full rounded-md' controls>
                        <source src='uploads/{$post['video_path']}' type='video/mp4'>
                    </video>
                    <p class='mt-2'>{$post['caption']}</p>
                </div>";
            }
            ?>
        </div>
    </div>

</body>
</html>
