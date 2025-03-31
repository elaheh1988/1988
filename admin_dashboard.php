<?php
require 'auth.php';
require_role('admin');
?>
<?php
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// آمار کلی
$stats = [
  'total' => 0,
  'approved' => 0,
  'pending' => 0,
  'rejected' => 0,
  'delivered' => 0,
  'in_warehouse' => 0,
  'total_dinar' => 0,
  'total_rial' => 0
];

// وضعیت فرم‌ها
$result = $conn->query("SELECT evaluator_status, warehouse_status, SUM(cost_dinar) as total, COUNT(*) as count FROM products GROUP BY evaluator_status, warehouse_status");
while ($row = $result->fetch_assoc()) {
    $stats['total'] += $row['count'];
    $stats['total_dinar'] += (int)$row['total'];

    if ($row['evaluator_status'] === 'approved') {
        if ($row['warehouse_status'] === 'delivered') {
            $stats['delivered'] += $row['count'];
        } else {
            $stats['in_warehouse'] += $row['count'];
        }
        $stats['approved'] += $row['count'];
    } elseif ($row['evaluator_status'] === 'pending') {
        $stats['pending'] += $row['count'];
    } elseif ($row['evaluator_status'] === 'rejected') {
        $stats['rejected'] += $row['count'];
    }
}

$stats['total_rial'] = $stats['total_dinar'] * 100; // فرض نرخ دینار به ریال
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>داشبورد مدیریت</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Tahoma;
      background: linear-gradient(135deg, #007cf0, #00dfd8);
      color: #333;
      margin: 0;
      padding: 20px;
    }
    h2 { text-align: center; color: white; margin-bottom: 30px; }
    .stats {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 12px;
      width: 260px;
      margin: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
      font-size: 16px;
    }
    .link-bar {
      text-align: center;
      margin-top: 30px;
    }
    .link-bar a {
      margin: 10px;
      background: #005f73;
      color: white;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
    }
    canvas {
      max-width: 400px;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <h2>داشبورد مدیریت سیستم</h2>

  <div class="stats">
    <div class="card">📦 کل فرم‌ها: <?= $stats['total'] ?></div>
    <div class="card">🟢 تأییدشده: <?= $stats['approved'] ?></div>
    <div class="card">🟡 در انتظار: <?= $stats['pending'] ?></div>
    <div class="card">🔴 ردشده: <?= $stats['rejected'] ?></div>
    <div class="card">✅ تحویل‌شده: <?= $stats['delivered'] ?></div>
    <div class="card">📥 مانده در انبار: <?= $stats['in_warehouse'] ?></div>
    <div class="card">💰 مجموع ارزش (دینار): <?= number_format($stats['total_dinar']) ?></div>
    <div class="card">💵 معادل (ریال): <?= number_format($stats['total_rial']) ?></div>
  </div>

  <canvas id="statusChart"></canvas>

  <div class="link-bar">
    <a href="form_employee.php">➕ ثبت فرم</a>
    <a href="evaluator.php">🧾 ارزیابی</a>
    <a href="warehouse.php">🏷 انبار</a>
    <a href="#">👤 مدیریت کاربران</a>
  </div>

  <script>
    const ctx = document.getElementById('statusChart');
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['تأیید شده', 'رد شده', 'در انتظار'],
        datasets: [{
          label: 'وضعیت ارزیابی',
          data: [<?= $stats['approved'] ?>, <?= $stats['rejected'] ?>, <?= $stats['pending'] ?>],
          backgroundColor: ['#28a745', '#dc3545', '#ffc107']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  </script>
</body>
</html>
