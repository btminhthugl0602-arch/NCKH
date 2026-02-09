<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Set flash message trước khi logout
setFlash('logout_success', 'Đăng xuất thành công!', 'success');

// Gọi hàm logout (đã định nghĩa trong session.php)
logout();
