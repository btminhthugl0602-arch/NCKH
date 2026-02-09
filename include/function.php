<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// ==================== REDIRECT & URL ====================

/**
 * Redirect đến URL
 * @param string $url
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Tạo URL cho module/action
 * @param string $module
 * @param string $action
 * @param array $params
 * @return string
 */
function url($module, $action = 'index', $params = []) {
    $url = _HOST_URL . "?module=$module&action=$action";
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    return $url;
}

// ==================== FLASH MESSAGES ====================

/**
 * Set flash message
 * @param string $key Khóa message
 * @param string $message Nội dung
 * @param string $type Loại: success, danger, warning, info
 */
function setFlash($key, $message, $type = 'success') {
    $_SESSION['flash'][$key] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Lấy và xóa flash message
 * @param string $key
 * @return array|null ['message' => '', 'type' => '']
 */
function getFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $flash = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $flash;
    }
    return null;
}

/**
 * Hiển thị flash message dưới dạng HTML
 * @param string $key
 * @return string HTML
 */
function showFlash($key) {
    $flash = getFlash($key);
    if ($flash) {
        $type = $flash['type'];
        $message = htmlspecialchars($flash['message']);
        return "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
                    {$message}
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
    }
    return '';
}

// ==================== VALIDATION ====================

/**
 * Validate required field
 * @param mixed $value
 * @param string $fieldName
 * @return string|null Error message hoặc null nếu hợp lệ
 */
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return "$fieldName không được để trống";
    }
    return null;
}

/**
 * Validate email
 * @param string $email
 * @return string|null
 */
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Email không hợp lệ";
    }
    return null;
}

/**
 * Validate độ dài
 * @param string $value
 * @param int $min
 * @param int $max
 * @param string $fieldName
 * @return string|null
 */
function validateLength($value, $min, $max, $fieldName) {
    $length = strlen(trim($value));
    if ($length < $min) {
        return "$fieldName phải có ít nhất $min ký tự";
    }
    if ($length > $max) {
        return "$fieldName không được quá $max ký tự";
    }
    return null;
}

/**
 * Validate số điện thoại
 * @param string $phone
 * @return string|null
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 10 || strlen($phone) > 11) {
        return "Số điện thoại không hợp lệ";
    }
    return null;
}

/**
 * Validate ngày tháng
 * @param string $date
 * @param string $format
 * @return bool
 */
function validateDate($date, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

// ==================== UPLOAD FILE ====================

/**
 * Upload file ảnh
 * @param array $file $_FILES['field']
 * @param string $targetDir Thư mục đích
 * @return array ['success' => bool, 'path' => string, 'error' => string]
 */
function uploadImage($file, $targetDir = 'uploads/') {
    // Kiểm tra có file không
    if (!isset($file) || $file['error'] == UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'error' => 'Chưa chọn file'];
    }
    
    // Kiểm tra lỗi upload
    if ($file['error'] != UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Lỗi khi upload file'];
    }
    
    // Kiểm tra loại file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'error' => 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)'];
    }
    
    // Kiểm tra kích thước (max 5MB)
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'File quá lớn (tối đa 5MB)'];
    }
    
    // Tạo tên file unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '_' . time() . '.' . $extension;
    
    // Đảm bảo thư mục tồn tại
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $targetPath = $targetDir . $fileName;
    
    // Move file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $targetPath];
    }
    
    return ['success' => false, 'error' => 'Lỗi khi lưu file'];
}

// ==================== FORMAT DATA ====================

/**
 * Format ngày tháng
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format số tiền VNĐ
 * @param float $amount
 * @return string
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Escape HTML
 * @param string $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Cắt chuỗi và thêm dấu ...
 * @param string $string
 * @param int $length
 * @return string
 */
function truncate($string, $length = 100) {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . '...';
}

// ==================== HELPERS ====================

/**
 * Debug - var_dump và die
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Get status badge HTML
 * @param string $status
 * @return string
 */
function getStatusBadge($status) {
    $badges = [
        'ACTIVE' => '<span class="badge bg-success">Đang hoạt động</span>',
        'INACTIVE' => '<span class="badge bg-secondary">Không hoạt động</span>',
        'LOCKED' => '<span class="badge bg-danger">Đã khóa</span>',
        'DRAFT' => '<span class="badge bg-warning">Nháp</span>',
        'COMPLETED' => '<span class="badge bg-info">Hoàn thành</span>',
        'CANCELLED' => '<span class="badge bg-dark">Đã hủy</span>',
        'PENDING' => '<span class="badge bg-warning">Chờ duyệt</span>',
        'APPROVED' => '<span class="badge bg-success">Đã duyệt</span>',
        'REJECTED' => '<span class="badge bg-danger">Từ chối</span>',
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CSRF token input field
 * @return string HTML
 */
function csrfField() {
    $token = generateCsrfToken();
    return "<input type='hidden' name='csrf_token' value='$token'>";
}
