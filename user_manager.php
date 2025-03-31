<?php
require 'auth.php';
require_role('admin');

$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // جلوگیری از ذخیره رمز ساده
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $role);

    if ($stmt->execute()) {
        $message = "✅ کاربر با موفقیت اضافه شد";
    } else {
        $message = "❌ خطا در افزودن کاربر: " . $stmt->error;
    }
}

// دریافت کاربران فعلی
$result = $conn->query("SELECT id, username, role FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت کاربران</title>
    <style>
        body { font-family: Tahoma; padding: 30px; background: #f0f8ff; }
        form, table { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 0 10px #ccc; }
        input, select, button { padding: 10px; margin: 10px 0; width: 100%; font-size: 15px; border-radius: 6px; border: 1px solid #aaa; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        th { background-color: #f2f2f2; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>مدیریت کاربران</h2>

    <?php if ($message): ?>
        <div class="<?= strpos($message, '✅') !== false ? 'success' : 'error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <h3>افزودن کاربر جدید</h3>
        <label>نام کاربری</label>
        <input name="username" required>

        <label>رمز عبور</label>
        <input name="password" required>

        <label>نقش</label>
        <select name="role" required>
            <option value="admin">مدیر</option>
            <option value="employee">کارمند</option>
            <option value="evaluator">کارشناس ارزیاب</option>
            <option value="warehouse">انباردار</option>
        </select>

        <button type="submit">افزودن کاربر</button>
    </form>

    <h3>لیست کاربران</h3>
    <table>
        <tr>
            <th>شناسه</th>
            <th>نام کاربری</th>
            <th>نقش</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['username'] ?></td>
                <td><?= $row['role'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
