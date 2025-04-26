<?php
include 'db.php';

$post_id = $_GET['post_id'];
$comments = $conn->query("SELECT * FROM comments WHERE post_id = $post_id");

while ($comment = $comments->fetch_assoc()) {
    echo "<div class='bg-gray-100 p-4 rounded-md mb-2'>
            <p><strong>{$comment['user_id']}:</strong> {$comment['comment']}</p>
          </div>";
}
?>
