<?php
// تنظیمات حرفه‌ای کوکی سشن برای تمام زیردامنه‌ها
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Lax');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '.hmtch.ir',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();

// اگر لاگین شده بود، بره به داشبورد
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "لطفاً همه فیلدها را پر کنید.";
    } else {
        $conn = new mysqli("localhost", "hmtchir1_admin", "Amerfarihi67@", "hmtchir1_goods");
        $conn->set_charset("utf8");

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;

                // هدایت بر اساس نقش
                switch ($user['role']) {
                    case 'admin': header("Location: admin_dashboard.php"); break;
                    case 'employee': header("Location: form_employee.php"); break;
                    case 'evaluator': header("Location: evaluator.php"); break;
                    case 'warehouse': header("Location: warehouse.php"); break;
                    default: die("نقش نامعتبر است.");
                }
                exit;
            } else {
                $error = "نام کاربری یا رمز عبور اشتباه است.";
            }
        } else {
            $error = "نام کاربری یا رمز عبور اشتباه است.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>ورود به سامانه</title>
  <style>
    body {
      background: linear-gradient(135deg, #89f7fe, #66a6ff);
      font-family: Tahoma, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 15px;
    }
    .login-container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #222;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }
    input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 16px;
    }
    button {
      margin-top: 25px;
      width: 100%;
      background: #007bff;
      color: white;
      border: none;
      padding: 12px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }
    .error {
      color: red;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <form method="post">
      <h2>ورود به سامانه</h2>

      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <label for="username">نام کاربری</label>
      <input type="text" id="username" name="username" required>

      <label for="password">رمز عبور</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">ورود</button>
    </form>
  </div>
</body>
</html>
