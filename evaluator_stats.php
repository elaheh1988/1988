<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'hmtchir1_goods';
$user = 'hmtchir1_admin';
$pass = 'Amerfarihi67@';
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8");

$stats = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'total' => 0
];

$result = $conn->query("SELECT evaluator_status, COUNT(*) as count FROM products GROUP BY evaluator_status");
while ($row = $result->fetch_assoc()) {
    $stats[$row['evaluator_status']] = (int)$row['count'];
    $stats['total'] += (int)$row['count'];
}

echo json_encode($stats);
