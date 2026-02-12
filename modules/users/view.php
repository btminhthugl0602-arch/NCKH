<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Sinh viên và giảng viên có thể xem
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: " . _HOST_URL . "?module=auth&action=login");
    exit();
}

global $conn;

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id == 0) {
    header("Location: " . _HOST_URL . "?module=users");
    exit();
}

// Lấy thông tin user
$sql = "SELECT tk.*, ltk.tenLoaiTK, sv.tenSV, sv.MSV, sv.idLop, l.tenLop, k1.tenKhoa as svKhoa,
        gv.tenGV, k2.tenKhoa as gvKhoa
        FROM taikhoan tk
        LEFT JOIN loaitaikhoan ltk ON tk.idLoaiTK = ltk.idLoaiTK
        LEFT JOIN sinhvien sv ON tk.idTK = sv.idTK
        LEFT JOIN lop l ON sv.idLop = l.idLop
        LEFT JOIN khoa k1 ON sv.idKhoa = k1.idKhoa
        LEFT JOIN giangvien gv ON tk.idTK = gv.idTK
        LEFT JOIN khoa k2 ON gv.idKhoa = k2.idKhoa
        WHERE tk.idTK = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: " . _HOST_URL . "?module=users");
    exit();
}

$user = mysqli_fetch_assoc($result);

$data = [
    'page_title' => 'Xem thông tin tài khoản'
];

$active_page = 'users';

layout('header', $data);
layout('sidebar', ['active_page' => $active_page]);
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php layout('navbar', $data); ?>
    
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                            <h6 class="text-white ps-3">Thông tin chi tiết tài khoản</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Tên đăng nhập</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['tenTK']) ?></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Họ và tên</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['tenSV'] ?? $user['tenGV'] ?? 'Admin') ?></p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Vai trò</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['tenLoaiTK']) ?></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Trạng thái</h6>
                                <?php if ($user['isActive'] == 1): ?>
                                    <span class="badge bg-gradient-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-gradient-secondary">Khóa</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($user['idLoaiTK'] == 3): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Mã sinh viên</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['MSV'] ?? 'N/A') ?></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Lớp</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['tenLop'] ?? 'N/A') ?></p>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Khoa</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['svKhoa'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                        <?php elseif ($user['idLoaiTK'] == 2): ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-sm mb-2">Khoa</h6>
                                <p class="text-sm"><?= htmlspecialchars($user['gvKhoa'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-12">
                                <a href="?module=users" class="btn bg-gradient-secondary">
                                    <i class="material-symbols-rounded">arrow_back</i>
                                    Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php layout('footer'); ?>
