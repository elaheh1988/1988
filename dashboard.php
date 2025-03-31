<?php
include 'check_auth.php';

switch ($_SESSION['user']['role']) {
    case 'employee':
        header('Location: employee.php');
        exit;
    case 'evaluator':
        header('Location: evaluator.php');
        exit;
    case 'warehouse':
        header('Location: warehouse.php');
        exit;
    case 'admin':
        header('Location: admin.php');
        exit;
    default:
        echo 'نقش نامعتبر است';
}
?>
