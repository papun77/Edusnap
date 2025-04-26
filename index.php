<?php
session_start();
$host = "localhost";
$db = "edusnap";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

$error = "";
$show_forgot_link = false;

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: index.php");
            exit;
        } else {
            $error = "Incorrect password.";
            $show_forgot_link = true;
        }
    } else {
        $error = "Account not found.";
    }
}

// Fetch feed posts
$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("
    SELECT 
        posts.*, 
        users.username,
        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
        (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id) AS comment_count
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    WHERE users.username LIKE CONCAT('%', ?, '%') OR posts.caption LIKE CONCAT('%', ?, '%')
    ORDER BY posts.id DESC
");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edusnap - Home</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function playVideo(container, videoSrc) {
      if (container.querySelector('video')) return;
      const video = document.createElement('video');
      video.src = videoSrc;
      video.controls = true;
      video.autoplay = true;
      video.className = "w-full h-auto rounded-md";
      video.playsInline = true;
      video.addEventListener('loadeddata', () => { video.play(); });
      container.innerHTML = '';
      container.appendChild(video);
    }

    function sharePost(url) {
      if (navigator.share) {
        navigator.share({ title: "Check this video on Edusnap", url });
      } else {
        navigator.clipboard.writeText(url);
        alert("Link copied to clipboard!");
      }
    }
  </script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-8 px-4">

  <!-- Navbar -->
  <nav class="bg-blue-600 p-4 rounded-md shadow-md mb-4">
    <div class="flex justify-between items-center">
      <a href="index.php" class="text-white font-bold text-2xl">Edusnap</a>
      <div>
        <?php if (isset($_SESSION['user_id'])) { ?>
          <a href="dashboard.php" class="text-white px-4 py-2 rounded-md hover:bg-blue-700">Dashboard</a>
          <a href="logout.php" class="text-white px-4 py-2 rounded-md hover:bg-red-500">Logout</a>
        <?php } else { ?>
          <button onclick="document.getElementById('loginModal').classList.remove('hidden')" class="text-white px-4 py-2 rounded-md hover:bg-blue-700">Login</button>
          <a href="signup.php" class="text-white px-4 py-2 rounded-md hover:bg-blue-700">Sign Up</a>
        <?php } ?>
      </div>
    </div>
  </nav>

  <!-- Notice -->
  <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded">
    <p class="font-semibold">⚠️ This site is only for Educational content. Please do not upload any non-educational content.</p>
  </div>

  <?php if (isset($_SESSION['user_id'])) { ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <a href="upload.php" class="bg-green-500 text-white text-center px-4 py-3 rounded-md hover:bg-green-600">Upload Video</a>
      <a href="quiz.php" class="bg-yellow-500 text-white text-center px-4 py-3 rounded-md hover:bg-yellow-600">Start Quiz</a>
      <a href="shorts.php" class="bg-purple-500 text-white text-center px-4 py-3 rounded-md hover:bg-purple-600">Scroll</a>
    </div>
  <?php } ?>

  <!-- Search -->
  <form method="get" class="mb-6 flex items-center">
    <input type="text" name="search" placeholder="Search by caption or username..." value="<?php echo htmlspecialchars($search); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none"/>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">Search</button>
  </form>

  <!-- Feed -->
  <div class="bg-white p-6 rounded-md shadow-lg">
    <h2 class="text-3xl font-bold text-blue-800 mb-6 text-center">Edusnap Feed</h2>

    <?php if ($posts->num_rows === 0): ?>
      <p class="text-center text-gray-500">No posts found.</p>
    <?php endif; ?>

    <?php while ($post = $posts->fetch_assoc()) {
      $filename = pathinfo($post['video_path'], PATHINFO_FILENAME);
      $thumbnailPath = "thumbnails/$filename.jpg";
      $videoPath = "uploads/" . $post['video_path'];
      $postUrl = "http://" . $_SERVER['HTTP_HOST'] . "/video.php?id=" . $post['id'];
    ?>
      <div class="mb-8 p-4 bg-gray-50 rounded-lg shadow-sm">
        <div class="relative group cursor-pointer mb-3 rounded overflow-hidden shadow" onclick="playVideo(this, '<?php echo $videoPath; ?>')">
          <img src="<?php echo $thumbnailPath; ?>" alt="Video Thumbnail" class="w-full h-auto max-h-[500px] object-contain transition duration-300 ease-in-out transform group-hover:scale-105"/>
          <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 text-white text-4xl">▶</div>
        </div>

        <p class="text-lg font-semibold text-gray-800">@<?php echo htmlspecialchars($post['username']); ?></p>
        <p class="text-gray-700 mb-2"><?php echo htmlspecialchars($post['caption']); ?></p>

        <div class="text-sm text-gray-600 mb-3 flex space-x-6">
          <span>Likes: <?php echo $post['like_count']; ?></span>
          <span>Comments: <?php echo $post['comment_count']; ?></span>
        </div>

        <div class="flex flex-wrap space-x-4 items-center">
          <a href="video.php?id=<?php echo $post['id']; ?>" class="text-blue-600 hover:underline">Watch</a>
          <a href="like.php?post_id=<?php echo $post['id']; ?>" class="text-red-500 hover:underline">Like</a>
          <a href="comment.php?post_id=<?php echo $post['id']; ?>" class="text-yellow-500 hover:underline">Comment</a>
          <button onclick="sharePost('<?php echo $postUrl; ?>')" class="text-green-600 hover:underline">Share</button>
          <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) { ?>
            <a href="delete.php?post_id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')" class="text-red-700 hover:underline">Delete</a>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<!-- Login Modal -->
<div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white p-6 rounded-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-blue-700 mb-4">Login to Edusnap</h2>

    <?php if ($error): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="hidden" name="login" value="1">
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required class="w-full px-4 py-2 border rounded">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 border rounded">
      </div>
      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
    </form>

    <div class="flex justify-between mt-4 text-sm">
      <a href="signup.php" class="text-blue-600 hover:underline">Sign Up</a>
      <?php if ($show_forgot_link): ?>
        <a href="forgot_password.php" class="text-blue-600 hover:underline">Forgot Password?</a>
      <?php endif; ?>
    </div>

    <button onclick="document.getElementById('loginModal').classList.add('hidden')" class="mt-4 text-gray-500 hover:text-gray-700 text-sm">Cancel</button>
  </div>
</div>

<!-- Contact Admin Floating Button -->
<button onclick="document.getElementById('contactAdminModal').classList.remove('hidden')"
  class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-full shadow-lg text-sm z-50">
  @Helpdesk
</button>

<!-- Contact Admin Modal -->
<div id="contactAdminModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-sm">
    <h3 class="text-xl font-bold mb-2 text-blue-700">Contact Admin</h3>
    <p class="text-gray-600 text-sm mb-4">Need help? Reach out to our support team.</p>
    <p class="text-gray-800 mb-2">📧 <a href="mailto:helpedusnap@gmail.com" class="text-blue-600 hover:underline">helpedusnap@gmail.com</a></p>
    <button onclick="document.getElementById('contactAdminModal').classList.add('hidden')"
      class="mt-4 w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 rounded">Close</button>
  </div>
</div>

</body>
</html>
