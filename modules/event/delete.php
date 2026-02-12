<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

require_once __DIR__ . '/../db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Chỉ admin hoặc người tạo sự kiện mới được xóa
if ($user_role != 1 && $user_role != 2) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id == 0) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

global $conn;

// Kiểm tra quyền xóa
$sql_check = "SELECT nguoiTao FROM sukien WHERE idSK = $event_id";
$result_check = mysqli_query($conn, $sql_check);

if (!$result_check || mysqli_num_rows($result_check) == 0) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

$event = mysqli_fetch_assoc($result_check);

// Giảng viên chỉ được xóa sự kiện do mình tạo
if ($user_role == 2 && $event['nguoiTao'] != $user_id) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

// Xóa sự kiện (đánh dấu isActive = 0)
$sql_delete = "UPDATE sukien SET isActive = 0 WHERE idSK = $event_id";

if (mysqli_query($conn, $sql_delete)) {
    // Xóa các dữ liệu liên quan nếu cần
    // Xóa lịch trình
    mysqli_query($conn, "DELETE FROM lichtrinh WHERE idSK = $event_id");
    
    // Xóa chủ đề sự kiện
    mysqli_query($conn, "UPDATE chude_sukien SET isActive = 0 WHERE idSK = $event_id");
    
    // Redirect về trang danh sách
    header("Location: " . _HOST_URL . "?module=event&message=deleted");
} else {
    header("Location: " . _HOST_URL . "?module=event&message=error");
}
exit();
