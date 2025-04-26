<?php
// Generate a thumbnail for a given video file
function generateThumbnail($videoPath, $thumbnailPath) {
    // Get duration in seconds
    $duration_cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 \"$videoPath\"";
    $duration = (int)shell_exec($duration_cmd);

    // Capture frame at 1/3 of video length or at 1s minimum
    $timestamp = max(1, (int)($duration / 3));

    // Build thumbnail command
    $cmd = "ffmpeg -ss $timestamp -i \"$videoPath\" -vframes 1 -q:v 2 \"$thumbnailPath\" -y";
    shell_exec($cmd);
}

// Example usage
$videoDir = 'uploads/';
$thumbnailDir = 'thumbnails/';
if (!is_dir($thumbnailDir)) {
    mkdir($thumbnailDir, 0755, true);
}

$videos = glob($videoDir . "*.mp4");
foreach ($videos as $video) {
    $filename = pathinfo($video, PATHINFO_FILENAME);
    $thumbnailPath = $thumbnailDir . $filename . '.jpg';

    if (!file_exists($thumbnailPath)) {
        generateThumbnail($video, $thumbnailPath);
        echo "Thumbnail created for $video<br>";
    } else {
        echo "Thumbnail already exists for $video<br>";
    }
}
?>
