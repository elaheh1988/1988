<?php
require 'auth.php';
require_role('evaluator');
?>
<?php
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

// ثبت تأیید یا رد
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['uuid'], $_POST['action'])) {
    $uuid = $_POST['uuid'];
    $status = $_POST['action'];
    $note = $status === 'rejected' ? $_POST['note'] : null;

    $stmt = $conn->prepare("UPDATE products SET evaluator_status=?, evaluator_note=? WHERE uuid=?");
    $stmt->bind_param("sss", $status, $note, $uuid);
    $stmt->execute();
}

// دریافت فرم‌های در انتظار
$pending = $conn->query("SELECT * FROM products WHERE evaluator_status = 'pending' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>بررسی فرم‌ها توسط کارشناس</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Tahoma, sans-serif;
      background: linear-gradient(135deg, #007cf0, #00dfd8);
      padding: 20px;
      direction: rtl;
    }
    h2 {
      text-align: center;
      color: #fff;
      margin-bottom: 30px;
    }
    .stats {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }
    .card {
      background: white;
      padding: 15px 20px;
      margin: 10px;
      border-radius: 10px;
      width: 200px;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
      text-align: center;
    }
    .card h3 {
      margin: 10px 0;
      font-size: 18px;
    }
    .pending { background: #fdf6d8; border-right: 5px solid #ffc107; }
    .approved { background: #d1f7e9; border-right: 5px solid #28a745; }
    .rejected { background: #fde2e1; border-right: 5px solid #dc3545; }
    .total { background: #e2f1f9; border-right: 5px solid #00bcd4; }

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
    textarea {
      width: 90%;
      display: none;
    }
    .action-buttons button {
      padding: 5px 10px;
      margin: 2px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    .approve { background-color: #28a745; color: white; }
    .reject { background-color: #dc3545; color: white; }
    canvas {
      max-width: 300px;
      margin: 0 auto 40px;
      display: block;
    }
  </style>
  <script>
    function toggleNote(uuid) {
      const textarea = document.getElementById('note-' + uuid);
      textarea.style.display = 'block';
    }

    // آپدیت آمار با AJAX
    async function fetchStats() {
      const res = await fetch('evaluator_stats.php');
      const data = await res.json();

      document.getElementById('pending-count').innerText = data.pending;
      document.getElementById('approved-count').innerText = data.approved;
      document.getElementById('rejected-count').innerText = data.rejected;
      document.getElementById('total-count').innerText = data.total;

      pieChart.data.datasets[0].data = [
        data.approved,
        data.rejected,
        data.pending
      ];
      pieChart.update();
    }

    // رفرش هر ۵ ثانیه
    setInterval(fetchStats, 5000);
    window.onload = fetchStats;
  </script>
</head>
<body>
  <h2>فرم‌های در انتظار بررسی</h2>

  <div class="stats">
    <div class="card pending">
      <h3>🕒 در انتظار</h3>
      <div id="pending-count">0</div>
    </div>
    <div class="card approved">
      <h3>✅ تأیید شده</h3>
      <div id="approved-count">0</div>
    </div>
    <div class="card rejected">
      <h3>❌ رد شده</h3>
      <div id="rejected-count">0</div>
    </div>
    <div class="card total">
      <h3>📦 مجموع فرم‌ها</h3>
      <div id="total-count">0</div>
    </div>
  </div>

  <canvas id="pieChart"></canvas>

  <table>
    <tr>
      <th>کد یکتا</th>
      <th>نام</th>
      <th>کد ملی</th>
      <th>نام کالا</th>
      <th>تعداد</th>
      <th>هزینه</th>
      <th>عملیات</th>
    </tr>
    <?php while ($row = $pending->fetch_assoc()): ?>
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
            <div class="action-buttons">
              <button class="approve" name="action" value="approved">تأیید</button>
              <button class="reject" type="button" onclick="toggleNote('<?= $row['uuid'] ?>')">رد</button>
              <div>
                <textarea name="note" id="note-<?= $row['uuid'] ?>" placeholder="دلیل رد..."></textarea>
                <br>
                <button class="reject" name="action" value="rejected">ثبت رد</button>
              </div>
            </div>
          </form>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <script>
    const pieChart = new Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: {
        labels: ['تأیید شده', 'رد شده', 'در انتظار'],
        datasets: [{
          data: [0, 0, 0],
          backgroundColor: ['#28a745', '#dc3545', '#ffc107'],
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
