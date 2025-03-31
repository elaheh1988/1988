<?php
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// ساخت یوزر admin با رمز 1234
$username = "admin";
$password = password_hash("1234", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $password, $role);
$stmt->execute();

echo "کاربر admin ساخته شد با رمز 1234";
?>
