<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$videos = $conn->query("SELECT posts.*, users.username,
                        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) as like_count,
                        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) as comment_count
                        FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edusnap - Scroll Videos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        video { max-height: 400px; }
    </style>
</head>
<body class="bg-gray-100">
<div class="max-w-2xl mx-auto p-6 space-y-10">
    <h1 class="text-3xl font-bold text-center text-blue-600 mb-8">Edusnap Scroll</h1>

    <?php while ($video = $videos->fetch_assoc()): ?>
        <div class="bg-white shadow-md rounded-lg p-4">
            <p class="text-sm text-gray-500">Posted by <strong><?= htmlspecialchars($video['username']) ?></strong></p>
            <h2 class="text-xl font-bold mt-2"><?= htmlspecialchars($video['caption']) ?></h2>

            <video class="mt-3 w-full video-player rounded-lg" controls muted>
                <source src="uploads/<?= htmlspecialchars($video['video_path']) ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <div class="mt-3 flex justify-between text-sm">
                <span><?= $video['like_count'] ?> Likes</span>
                <span><?= $video['comment_count'] ?> Comments</span>
            </div>

            <div class="mt-2 flex gap-4 text-sm">
                <a href="like.php?post_id=<?= $video['id'] ?>" class="text-blue-600 hover:underline">Like</a>
                <a href="comment.php?post_id=<?= $video['id'] ?>" class="text-yellow-600 hover:underline">Comment</a>
                <a href="share.php?post_id=<?= $video['id'] ?>" class="text-green-600 hover:underline">Share</a>
                <?php if ($video['user_id'] == $user_id): ?>
                    <a href="delete.php?id=<?= $video['id'] ?>" class="text-red-600 hover:underline">Delete</a>
                <?php endif; ?>
            </div>

            <?php
            $comments = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = " . $video['id'] . " ORDER BY comments.id DESC");
            if ($comments->num_rows > 0):
            ?>
            <div class="mt-4 bg-gray-100 p-2 rounded">
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="flex justify-between text-sm mb-1">
                        <span><strong><?= htmlspecialchars($comment['username']) ?>:</strong>
                        <?= isset($comment['content']) ? htmlspecialchars($comment['content']) : '[No content]' ?></span>
                        <?php if ($comment['user_id'] == $user_id): ?>
                            <a href="delete_comment.php?id=<?= $comment['id'] ?>&post_id=<?= $video['id'] ?>&redirect=scroll" class="text-red-500 hover:underline">Delete</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
</div>

<script>
    const videos = document.querySelectorAll('.video-player');
    let playing = null;

    window.addEventListener('scroll', () => {
        videos.forEach(video => {
            const rect = video.getBoundingClientRect();
            if (rect.top >= 0 && rect.bottom <= window.innerHeight) {
                if (playing && playing !== video) {
                    playing.pause();
                }
                if (video.paused) {
                    video.play();
                    playing = video;
                }
            } else {
                video.pause();
            }
        });
    });
</script>
</body>
</html>
