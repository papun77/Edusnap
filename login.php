<?php
session_start();
$host = "localhost";
$db = "edusnap";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = trim($_POST["identifier"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Edusnap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6 text-blue-700">Login to Edusnap</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 mb-4 rounded"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Email or Username</label>
        <input type="text" name="identifier" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-400">
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Password</label>
        <input type="password" name="password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:border-blue-400">
      </div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
    </form>

    <div class="flex justify-between mt-4 text-sm">
      <a href="signup.php" class="text-blue-600 hover:underline">Sign Up</a>
      <a href="forget_password.php" class="text-blue-600 hover:underline">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
