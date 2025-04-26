<?php
session_start();

if (!isset($_GET['post_id'])) {
    header("Location: index.php");
    exit;
}

$post_id = intval($_GET['post_id']);
$shareUrl = "http://localhost/edusnap/view.php?post_id=$post_id";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share Post</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

<div class="bg-white p-6 rounded shadow-lg text-center max-w-md">
    <h2 class="text-2xl font-bold mb-4">Share this post</h2>

    <div class="space-y-4">
        <a href="https://wa.me/?text=<?php echo urlencode("Check this out! $shareUrl"); ?>" 
           target="_blank" class="block bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
            Share on WhatsApp
        </a>

        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($shareUrl); ?>" 
           target="_blank" class="block bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700">
            Share on Facebook
        </a>

        <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode("Watch this on Edusnap!"); ?>&url=<?php echo urlencode($shareUrl); ?>" 
           target="_blank" class="block bg-blue-400 text-white py-2 px-4 rounded hover:bg-blue-500">
            Share on Twitter
        </a>

        <div>
            <input id="copyLink" type="text" value="<?php echo $shareUrl; ?>" 
                   class="w-full p-2 border rounded mb-2 text-center" readonly>
            <button onclick="copyToClipboard()" 
                    class="bg-gray-800 text-white py-2 px-4 rounded hover:bg-gray-900">
                Copy Link
            </button>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    const copyText = document.getElementById("copyLink");
    copyText.select();
    document.execCommand("copy");
    alert("Link copied to clipboard!");
}
</script>

</body>
</html>