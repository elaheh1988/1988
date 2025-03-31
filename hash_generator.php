<?php
// برای امنیت، فایل رو بعد از استفاده حذف کن یا محدودش کن
$password = "963852"; // ← رمز مورد نظر رو اینجا بذار
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "<h3>رمز عبور: <code>$password</code></h3>";
echo "<h3>هش شده:</h3><code>$hash</code>";
?>
