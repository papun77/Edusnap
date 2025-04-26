<?php
include 'db.php';
session_start();

if (!isset($_GET['post_id'])) {
    echo "Post ID missing.";
    exit;
}

$post_id = intval($_GET['post_id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text'])) {
    $comment_text = trim($_POST['comment_text']);
    if ($comment_text !== '') {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $post_id, $user_id, $comment_text);
        $stmt->execute();
    }
    header("Location: comment.php?post_id=$post_id");
    exit;
}

// Handle delete comment
if (isset($_GET['delete']) && isset($_GET['comment_id'])) {
    $comment_id = intval($_GET['comment_id']);
    
    // Ensure user owns the comment
    $checkStmt = $conn->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
    $checkStmt->bind_param("ii", $comment_id, $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $deleteStmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
        $deleteStmt->bind_param("i", $comment_id);
        $deleteStmt->execute();
    }

    header("Location: comment.php?post_id=$post_id");
    exit;
}

// Get all comments
$stmt = $conn->prepare("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 text-gray-900">

<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Comments</h2>

    <!-- Comment Form -->
    <form method="post" class="mb-6">
        <textarea name="comment_text" class="w-full p-2 border rounded mb-2" placeholder="Write your comment..." required></textarea>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Post Comment</button>
    </form>

    <!-- Comment List -->
    <div>
        <?php if ($comments->num_rows == 0): ?>
            <p class="text-gray-500">No comments yet.</p>
        <?php else: ?>
            <?php while ($row = $comments->fetch_assoc()): ?>
                <div class="border-b py-3">
                    <div class="flex justify-between items-center">
                        <strong>@<?php echo htmlspecialchars($row['username']); ?></strong>
                        <?php if ($user_id == $row['user_id']): ?>
                            <a href="?post_id=<?php echo $post_id; ?>&delete=true&comment_id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Delete this comment?')" 
                               class="text-red-500 hover:underline text-sm">Delete</a>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-700"><?php echo htmlspecialchars($row['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

