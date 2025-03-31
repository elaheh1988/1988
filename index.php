<?php
// اگر لاگین بود، هدایت به داشبورد مربوط به نقش
session_start();
if (isset($_SESSION['user'])) {
  $role = $_SESSION['user']['role'];
  switch ($role) {
    case 'admin':
      header("Location: admin_dashboard.php"); exit;
    case 'employee':
      header("Location: form_employee.php"); exit;
    case 'evaluator':
      header("Location: evaluator.php"); exit;
    case 'warehouse':
      header("Location: warehouse.php"); exit;
  }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>سامانه رهگیری کالا | ورود</title>
  <style>
    body {
      background: linear-gradient(135deg, #8fd3f4, #84fab0);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'B Titr', Tahoma;
    }
    .box {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0,0,0,0.2);
      text-align: center;
    }
    a {
      display: inline-block;
      padding: 15px 30px;
      background: #007bff;
      color: white;
      border-radius: 10px;
      text-decoration: none;
      font-size: 18px;
    }
    a:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>به سامانه رهگیری کالا خوش آمدید</h2>
    <a href="login.php">ورود به سامانه</a>
  </div>
</body>
</html>
