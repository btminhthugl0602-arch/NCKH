<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

try {
    // Tạo DSN (Data Source Name)
    $dsn = _DRIVER . ':host=' . _HOST . ';dbname=' . _DB . ';charset=utf8mb4';
    
    // Tạo kết nối PDO
    $conn = new PDO($dsn, _USER, _PASS);
    
    // Thiết lập error mode
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Thiết lập fetch mode mặc định
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Thiết lập emulate prepares
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    if (_DEBUG) {
        echo "<!-- Database connected successfully -->\n";
    }
    
} catch (PDOException $e) {
    if (_DEBUG) {
        die("Lỗi kết nối database: " . $e->getMessage());
    } else {
        die("Lỗi hệ thống. Vui lòng liên hệ quản trị viên.");
    }
}
