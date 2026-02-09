<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// ==================== AUTHENTICATION ====================

/**
 * Kiểm tra user đã đăng nhập chưa
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Yêu cầu đăng nhập - redirect nếu chưa login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . _HOST_URL . '?module=auth&action=login');
        exit;
    }
}

// ==================== USER INFO ====================

/**
 * Lấy ID user đang đăng nhập
 * @return int|null
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy tên user đang đăng nhập
 * @return string
 */
function getUserName() {
    return $_SESSION['user_name'] ?? 'Guest';
}

/**
 * Lấy loại tài khoản (1=Admin, 2=GiangVien, 3=SinhVien)
 * @return int|null
 */
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Lấy email user
 * @return string|null
 */
function getUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

// ==================== ROLE CHECK ====================

/**
 * Kiểm tra user có phải Admin không
 * @return bool
 */
function isAdmin() {
    return getUserType() == 1;
}

/**
 * Kiểm tra user có phải Giảng viên không
 * @return bool
 */
function isGiangVien() {
    return getUserType() == 2;
}

/**
 * Kiểm tra user có phải Sinh viên không
 * @return bool
 */
function isSinhVien() {
    return getUserType() == 3;
}

/**
 * Yêu cầu quyền Admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . _HOST_URL . '?module=errors&action=403');
        exit;
    }
}

// ==================== SESSION MANAGEMENT ====================

/**
 * Set thông tin user vào session
 * @param array $user Mảng thông tin user
 */
function setUserSession($user) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user['idTK'];
    $_SESSION['user_name'] = $user['hoTen'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_type'] = $user['idLoaiTK'];
    $_SESSION['login_time'] = time();
    
    // Regenerate session ID để bảo mật
    session_regenerate_id(true);
}

/**
 * Đăng xuất
 */
function logout() {
    // Xóa tất cả session variables
    $_SESSION = array();
    
    // Xóa session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy session
    session_destroy();
    
    // Redirect về trang login
    header('Location: ' . _HOST_URL . '?module=auth&action=login');
    exit;
}

/**
 * Kiểm tra session timeout (30 phút)
 */
function checkSessionTimeout() {
    if (isLoggedIn()) {
        $timeout = 1800; // 30 phút
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $timeout)) {
            logout();
        }
        // Update login time
        $_SESSION['login_time'] = time();
    }
}

// Auto check timeout mỗi request
checkSessionTimeout();
