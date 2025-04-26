<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caption = trim($_POST["caption"]);
    $user_id = $_SESSION['user_id'];

    $videoName = uniqid() . "_" . basename($_FILES["video"]["name"]);
    $videoPath = "uploads/" . $videoName;
    move_uploaded_file($_FILES["video"]["tmp_name"], $videoPath);

    // Process base64 thumbnail
    $thumbnailData = $_POST['thumbnail_data'];
    $thumbName = uniqid() . "_thumb.jpg";
    $thumbPath = "thumbnails/" . $thumbName;

    if (strpos($thumbnailData, 'data:image') === 0) {
        $thumbnailData = explode(',', $thumbnailData)[1];
        file_put_contents($thumbPath, base64_decode($thumbnailData));
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, video_path, thumbnail_path, caption) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $videoName, $thumbName, $caption);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
