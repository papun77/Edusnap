<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$comment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] . '.php' : 'index.php';
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

if ($comment_id > 0) {
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $comment_id, $user_id);
    $stmt->execute();
}

header("Location: $redirect#post_$post_id");
exit();
