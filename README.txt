
۱. فایل‌ها را در public_html آپلود و استخراج کنید.
۲. وارد phpMyAdmin شوید و دیتابیس `hmtchir1_goods` را باز کنید.
۳. در تب SQL کد زیر را اجرا نمایید:

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50),
  password VARCHAR(50),
  role ENUM('employee','evaluator','warehouse','admin')
);

INSERT INTO users (username, password, role) VALUES
('admin', '1234', 'admin');

۴. مرورگر را باز کرده و وارد https://www.hmtch.ir شوید.
