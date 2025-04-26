<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = $_POST['caption'];
    $video = $_FILES['video'];
    $user_id = $_SESSION['user_id'];

    if ($video['size'] > 314572800) {
        $message = "Video too large. Max ~300MB (approx. 60s).";
    } else {
        $videoName = uniqid() . "_" . basename($video['name']);
        $videoTarget = "uploads/" . $videoName;

        if (move_uploaded_file($video['tmp_name'], $videoTarget)) {
            // Auto-generate thumbnail using FFmpeg
            $thumbnailName = pathinfo($videoName, PATHINFO_FILENAME) . ".jpg";
            $thumbnailPath = "thumbnails/" . $thumbnailName;

            $escapedVideoPath = escapeshellarg($videoTarget);
            $escapedThumbnailPath = escapeshellarg($thumbnailPath);
            $ffmpegCmd = "ffmpeg -i $escapedVideoPath -ss 00:00:01.000 -vframes 1 $escapedThumbnailPath";
            shell_exec($ffmpegCmd);

            // Validate user ID
            $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
            $check->bind_param("i", $user_id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows === 0) {
                $message = "Error: Invalid user ID. Please log in again.";
            } else {
                $stmt = $conn->prepare("INSERT INTO posts (user_id, caption, video_path, thumbnail_path) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isss", $user_id, $caption, $videoName, $thumbnailName);
                if ($stmt->execute()) {
                    $message = "Upload and thumbnail generation successful!";
                } else {
                    $message = "Database error: " . $stmt->error;
                }
            }
        } else {
            $message = "Video upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Video - Edusnap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10">
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-3xl font-bold text-center text-blue-700 mb-6">Upload a Video</h1>

    <?php if ($message): ?>
        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label class="block font-semibold mb-1">Caption</label>
            <input type="text" name="caption" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label class="block font-semibold mb-1">Video (Max 60 sec / ~300MB)</label>
            <input type="file" name="video" accept="video/mp4" required class="w-full">
        </div>

        <div class="text-sm text-gray-600 italic">* Thumbnail will be automatically generated after upload.</div>

        <div class="text-center">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">Upload</button>
        </div>
    </form>
</div>
</body>
</html>
