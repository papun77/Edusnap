<?php
include 'db.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch users with watch stats
$query = "
    SELECT users.id, users.username, COALESCE(user_limits.watch_count, 0) AS watch_count, user_limits.last_reset 
    FROM users 
    LEFT JOIN user_limits ON users.id = user_limits.user_id
";
$results = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Edusnap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-4xl font-bold text-blue-800 mb-6 text-center">Admin Dashboard</h1>

    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">User Video Watch Stats</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="py-3 px-4">User ID</th>
                    <th class="py-3 px-4">Username</th>
                    <th class="py-3 px-4">Watched Videos Today</th>
                    <th class="py-3 px-4">Last Reset Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetch_assoc()) { ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-2 px-4"><?php echo $row['id']; ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-2 px-4 text-green-600"><?php echo $row['watch_count']; ?></td>
                        <td class="py-2 px-4"><?php echo $row['last_reset'] ?? 'Never'; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
