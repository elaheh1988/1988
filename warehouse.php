<?php
require 'auth.php';
require_role('warehouse');
?>
<?php
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// تحویل کالا
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['uuid'])) {
    $uuid = $_POST['uuid'];
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE products SET warehouse_status='delivered', warehouse_delivery_time=? WHERE uuid=?");
    $stmt->bind_param("ss", $now, $uuid);
    $stmt->execute();
}

// آمار بالا
$counts = ['approved' => 0, 'delivered' => 0, 'remaining' => 0];
$result = $conn->query("SELECT warehouse_status, COUNT(*) as count FROM products WHERE evaluator_status = 'approved' GROUP BY warehouse_status");
while ($row = $result->fetch_assoc()) {
    if ($row['warehouse_status'] === 'delivered') {
        $counts['delivered'] = $row['count'];
    } else {
        $counts['remaining'] = $row['count'];
    }
    $counts['approved'] += $row['count'];
}

// دریافت فرم‌ها
$search = "";
$where = "WHERE evaluator_status = 'approved' AND warehouse_status = 'pending'";
if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $where .= " AND (national_code LIKE '%$search%' OR uuid LIKE '%$search%')";
}
$data = $conn->query("SELECT * FROM products $where ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>پنل انباردار</title>
  <style>
    body {
      font-family: Tahoma;
      background: linear-gradient(135deg, #0f9b8e, #57d1c9);
      padding: 20px;
      direction: rtl;
    }
    h2 { text-align: center; color: white; margin-bottom: 30px; }
    .stats {
      display: flex;
      justify-content: space-around;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }
    .card {
      background: white;
      padding: 15px 20px;
      border-radius: 10px;
      width: 220px;
      margin: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
      font-size: 16px;
      font-weight: bold;
    }
    .approved { border-right: 5px solid #007bff; }
    .delivered { border-right: 5px solid #28a745; }
    .remaining { border-right: 5px solid #ffc107; }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }
    form { margin: 0; }
    .deliver {
      background: #28a745;
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    .search-box {
      text-align: center;
      margin-bottom: 20px;
    }
    input[type="text"] {
      padding: 8px;
      width: 300px;
      font-size: 16px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .btn {
      padding: 8px 15px;
      border: none;
      border-radius: 6px;
      background-color: #007bff;
      color: white;
      cursor: pointer;
      margin-right: 5px;
      font-size: 14px;
    }
    .btn-link {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <h2>پنل انباردار</h2>

  <div class="stats">
    <div class="card approved">کل فرم‌های تأییدشده: <?= $counts['approved'] ?></div>
    <div class="card delivered">تحویل‌داده‌شده: <?= $counts['delivered'] ?></div>
    <div class="card remaining">در انبار: <?= $counts['remaining'] ?></div>
  </div>

  <div class="search-box">
    <form method="get">
      <input type="text" name="search" placeholder="جستجو با کد ملی یا کد یکتا" value="<?= htmlspecialchars($search) ?>">
      <button class="btn" type="submit">جستجو</button>
      <button class="btn" type="button" onclick="window.print()">🖨 چاپ</button>
      <a href="export_excel.php" class="btn btn-link"><button class="btn" type="button">📥 خروجی اکسل</button></a>
    </form>
  </div>

  <table>
    <tr>
      <th>کد یکتا</th>
      <th>نام</th>
      <th>کد ملی</th>
      <th>نام محصول</th>
      <th>تعداد</th>
      <th>هزینه</th>
      <th>عملیات</th>
    </tr>
    <?php while ($row = $data->fetch_assoc()): ?>
    <tr>
      <td><?= $row['uuid'] ?></td>
      <td><?= $row['full_name'] ?></td>
      <td><?= $row['national_code'] ?></td>
      <td><?= $row['product_name'] ?></td>
      <td><?= $row['quantity'] ?></td>
      <td><?= number_format($row['cost_dinar']) ?> دینار</td>
      <td>
        <form method="post">
          <input type="hidden" name="uuid" value="<?= $row['uuid'] ?>">
          <button class="deliver" type="submit">تحویل داده شد</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
