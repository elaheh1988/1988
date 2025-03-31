<?php
require_once 'libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// اتصال به دیتابیس
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

if (!isset($_GET['uuid'])) {
  die("❌ کد یکتا مشخص نشده است.");
}
$uuid = $_GET['uuid'];

// دریافت اطلاعات فرم
$stmt = $conn->prepare("SELECT * FROM products WHERE uuid = ?");
$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
  die("⛔ اطلاعاتی با این کد یکتا یافت نشد.");
}

// HTML رسید
$html = '
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; direction: rtl; }
    .receipt {
      border: 1px dashed #000;
      padding: 20px;
      width: 100%;
    }
    h2 { text-align: center; }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
    }
    td {
      padding: 8px;
      border-bottom: 1px solid #ccc;
    }
    .signature {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
      font-size: 16px;
    }
    .stamp {
      text-align: center;
      margin-top: 30px;
      border: 1px dashed #666;
      padding: 10px;
      font-size: 18px;
    }
  </style>
</head>
<body>
  <div class="receipt">
    <h2>رسید تحویل کالا</h2>
    <strong>کد یکتا: ' . $data['uuid'] . '</strong><br><br>
    <table>
      <tr><td>نام و نام خانوادگی:</td><td>' . $data['full_name'] . '</td></tr>
      <tr><td>کد ملی:</td><td>' . $data['national_code'] . '</td></tr>
      <tr><td>استان / شهر:</td><td>' . $data['province'] . ' / ' . $data['city'] . '</td></tr>
      <tr><td>نشانی:</td><td>' . $data['address'] . '</td></tr>
      <tr><td>شماره تماس:</td><td>' . $data['phone'] . '</td></tr>
      <tr><td>نوع محصول:</td><td>' . $data['product_type'] . '</td></tr>
      <tr><td>نام محصول:</td><td>' . $data['product_name'] . '</td></tr>
      <tr><td>تعداد:</td><td>' . $data['quantity'] . '</td></tr>
      <tr><td>هزینه کل (دینار):</td><td>' . number_format($data['cost_dinar']) . '</td></tr>
    </table>
    <div class="signature">
      <div>امضای مشتری: ......................</div>
      <div>امضای انباردار: ......................</div>
    </div>
    <div class="stamp">
      🔖 مهر رسمی شرکت / سازمان
    </div>
  </div>
</body>
</html>
';

// تولید PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ذخیره فایل در هاست
if (!is_dir('receipts')) {
  mkdir('receipts', 0777, true);
}
file_put_contents("receipts/receipt_$uuid.pdf", $dompdf->output());

// نمایش PDF
$dompdf->stream("receipt_$uuid.pdf", ["Attachment" => false]);
exit;
?>
