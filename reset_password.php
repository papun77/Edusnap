<?php
// reset_password.php
$conn = new mysqli("localhost", "root", "", "edusnap");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("ss", $new_pass, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green'>Password updated successfully. <a href='login.php'>Login</a></p>";
    } else {
        echo "<p style='color:red'>Invalid or expired token.</p>";
    }
    exit;
}

if (!isset($_GET['token'])) {
    die("Token missing.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow w-full max-w-md">
        <h2 class="text-2xl font-semibold text-blue-600 mb-4 text-center">Set New Password</h2>
        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <label class="block mb-2 text-gray-700">New Password</label>
            <input type="password" name="password" required class="w-full px-3 py-2 border rounded mb-4">
            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Reset Password</button>
        </form>
    </div>
</body>
</html>