<?php
// send_reset_email.php
session_start();
$conn = new mysqli("localhost", "root", "", "edusnap");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(32));
    $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expiry, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // In real use, send via PHPMailer or similar
        $reset_link = "http://localhost/edusnap/reset_password.php?token=$token";
        echo "<p style='color:green'>Reset link: <a href='$reset_link'>$reset_link</a></p>";
    } else {
        echo "<p style='color:red'>Email not found.</p>";
    }
}
?>