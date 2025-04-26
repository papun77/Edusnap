<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db.php';
    session_start();

    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Check if username or email already exists
    $result = $conn->query("SELECT * FROM users WHERE username = '$username' OR email = '$email'");

    if ($result->num_rows > 0) {
        $error = "Username or email already exists.";
    } else {
        // Register user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')");
        $message = "Registration successful. You can now log in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Edusnap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-96">
            <h2 class="text-2xl font-semibold text-center mb-4">Sign Up</h2>

            <?php if (isset($error)) { ?>
                <div class="bg-red-500 text-white text-sm p-2 rounded-md mb-4"><?php echo $error; ?></div>
            <?php } ?>

            <?php if (isset($message)) { ?>
                <div class="bg-green-500 text-white text-sm p-2 rounded-md mb-4"><?php echo $message; ?></div>
            <?php } ?>

            <form method="POST">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700">Username</label>
                    <input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded-md" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Password</label>
                    <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded-md" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="w-full p-2 border border-gray-300 rounded-md" required>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Sign Up</button>
            </form>

            <p class="mt-4 text-center">Already have an account? <a href="login.php" class="text-blue-500">Login</a></p>
        </div>
    </div>

</body>
</html>
