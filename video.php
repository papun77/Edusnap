<?php
include 'db.php';
session_start();

$result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edusnap Feed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        video {
            display: block;
            margin: auto;
        }
    </style>
</head>
<body class="bg-gray-100 py-10 min-h-screen">
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-4xl font-bold text-center text-blue-700 mb-8">Edusnap Feed</h1>

    <?php if ($result->num_rows > 0): ?>
        <div class="flex flex-col gap-10 items-center">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-gray-50 p-4 rounded-lg shadow w-full max-w-3xl">
                    <div class="mb-2 text-sm text-gray-600">
                        Posted by <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                    </div>

                    <div class="text-lg font-semibold mb-3"><?php echo htmlspecialchars($row['caption']); ?></div>

                    <div class="overflow-auto">
                        <video controls class="rounded-lg edusnap-video" style="max-width: 100%;">
                            <source src="uploads/<?php echo htmlspecialchars($row['video_path']); ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">No videos have been uploaded yet.</p>
    <?php endif; ?>
</div>

<script>
    const videos = document.querySelectorAll('.edusnap-video');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Pause all other videos
                videos.forEach(video => {
                    if (video !== entry.target) {
                        video.pause();
                    }
                });
            }
        });
    }, {
        threshold: 0.6 // 60% of video should be in view
    });

    videos.forEach(video => observer.observe(video));
</script>
</body>
</html>
