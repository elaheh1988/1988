<?php
// تنظیم دامنه برای ذخیره سشن در تمام زیر دامنه‌ها
ini_set('session.cookie_domain', '.hmtch.ir');
session_start();

/**
 * بررسی سطح دسترسی کاربر بر اساس نقش
 *
 * @param string $role نقش مورد انتظار (مثلاً 'admin', 'employee', ...)
 */
function require_role($role) {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== $role) {
        header("Location: login.php");
        exit;
    }
}
?>
