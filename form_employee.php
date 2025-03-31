<?php
require 'auth.php';
require_role('employee');

// اتصال به دیتابیس
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// تولید UUID
function generate_uuid() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
  );
}

$uuid = generate_uuid();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $national_code = $_POST['national_code'];
  $one_year_ago = date('Y-m-d H:i:s', strtotime('-1 year'));

  $check = $conn->prepare("SELECT id FROM products WHERE national_code = ? AND created_at >= ?");
  $check->bind_param("ss", $national_code, $one_year_ago);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $message = "❌ این کد ملی در یک سال گذشته قبلاً ثبت شده و امکان ثبت مجدد ندارد.";
  } else {
    $stmt = $conn->prepare("INSERT INTO products 
      (uuid, full_name, national_code, province, city, address, postal_code, phone, product_type, product_name, quantity, cost_dinar, created_at, evaluator_status, warehouse_status)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending', 'pending')");
    $stmt->bind_param("ssssssssssii",
      $uuid,
      $_POST['full_name'], $_POST['national_code'], $_POST['province'], $_POST['city'],
      $_POST['address'], $_POST['postal_code'], $_POST['phone'],
      $_POST['product_type'], $_POST['product_name'], $_POST['quantity'], $_POST['cost_dinar']
    );
    if ($stmt->execute()) {
      echo "<script>window.location.href='receipt.php?uuid=$uuid';</script>";
      exit;
    } else {
      $message = "❌ خطا در ثبت اطلاعات: " . $stmt->error;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>فرم ثبت کالا</title>
  <style>
    body {
      font-family: Tahoma, sans-serif;
      background: linear-gradient(135deg, #9be15d, #00e3ae);
      min-height: 100vh;
      margin: 0;
      padding: 30px;
      direction: rtl;
    }
    form {
      background: white;
      padding: 20px;
      border-radius: 12px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }
    button {
      background-color: #28a745;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    .message {
      text-align: center;
      margin-bottom: 20px;
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <form method="post">
    <h2 style="text-align:center;">فرم ثبت اطلاعات کالا توسط کارمند</h2>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <label>نام و نام خانوادگی</label>
    <input name="full_name" required>

    <label>کد ملی</label>
    <input name="national_code" maxlength="10" required>

    <label>استان</label>
    <input name="province" required>

    <label>شهر</label>
    <input name="city" required>

    <label>آدرس دقیق</label>
    <textarea name="address" required></textarea>

    <label>کد پستی</label>
    <input name="postal_code">

    <label>شماره تماس</label>
    <input name="phone">

    <label>نوع محصول</label>
    <select name="product_type" required>
      <option value="">انتخاب کنید</option>
      <option>محصول شمارشی</option>
      <option>محصول وزنی</option>
      <option>تلویزیون</option>
      <option>پارچه</option>
    </select>

    <label>نام محصول</label>
    <input name="product_name" required>

    <label>تعداد</label>
    <input name="quantity" type="number" required>

    <label>هزینه کل (دینار)</label>
    <input name="cost_dinar" type="number" required>

    <button type="submit">ثبت و ذخیره</button>
  </form>
</body>
</html>
