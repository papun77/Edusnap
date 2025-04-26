<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = ""; // default for XAMPP
$dbname = "edusnap";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO users (username, password , email)
        VALUES ('testuser', '1234' , 'test@example.com')";

if ($conn->query($sql) === TRUE) {
  echo "User inserted successfully";
} else {
  echo "Error: " . $conn->error;
}

$conn->close();
?>