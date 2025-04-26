<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Edusnap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="max-w-3xl mx-auto mt-10">
    <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold mb-4 text-blue-800">Welcome to Edusnap Dashboard</h1>
        <p class="text-xl font-semibold text-gray-800 mb-4">Explore and enhance your learning experience!</p>

        <div class="mt-6 flex flex-col sm:flex-row justify-center gap-4">
            <a href="upload.php" class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600">Upload Video</a>
            <a href="quiz.php" class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600">Take a Quiz</a>
            <a href="shorts.php" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700">Scroll</a>
        </div>
    </div>
</div>

</body>
</html>
