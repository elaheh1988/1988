<?php
$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=warehouse_report.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "کد یکتا\tنام\tکد ملی\tنام محصول\tتعداد\tهزینه کل (دینار)\tتاریخ تحویل\n";

$result = $conn->query("SELECT * FROM products WHERE evaluator_status = 'approved'");

while($row = $result->fetch_assoc()) {
  echo $row['uuid'] . "\t" .
       $row['full_name'] . "\t" .
       $row['national_code'] . "\t" .
       $row['product_name'] . "\t" .
       $row['quantity'] . "\t" .
       $row['cost_dinar'] . "\t" .
       ($row['warehouse_delivery_time'] ?? '-') . "\n";
}
?>
