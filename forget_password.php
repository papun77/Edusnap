<!-- forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-6 rounded shadow w-full max-w-md">
    <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Reset Password</h2>
    <form method="POST" action="send_reset_email.php">
      <label class="block mb-2 text-gray-700">Enter your registered email</label>
      <input type="email" name="email" required class="w-full px-3 py-2 border rounded mb-4">
      <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Send Reset Link</button>
    </form>
  </div>
</body>
</html>