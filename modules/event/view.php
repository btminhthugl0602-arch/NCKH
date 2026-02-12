<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

require_once __DIR__ . '/../db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($event_id == 0) {
    header('Location: ' . _HOST_URL . '?module=event');
    exit;
}

// Lấy thông tin sự kiện
$sql = "SELECT sk.*, ct.tenCap, tk.tenTK as nguoiTaoTen
        FROM sukien sk
        LEFT JOIN cap_tochuc ct ON sk.idCap = ct.idCap
        LEFT JOIN taikhoan tk ON sk.nguoiTao = tk.idTK
        WHERE sk.idSK = $event_id AND sk.isActive = 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header('Location: ' . _HOST_URL . '?module=event');
    exit;
}

$event = mysqli_fetch_assoc($result);

$data = [
    'page_title' => $event['tenSK'],
    'active_page' => 'event'
];

layout('header', $data);
layout('sidebar');

// Tính trạng thái sự kiện
$now = time();
$batDau = strtotime($event['ngayBatDau']);
$ketThuc = strtotime($event['ngayKetThuc']);
$moDangKy = strtotime($event['ngayMoDangKy']);
$dongDangKy = strtotime($event['ngayDongDangKy']);

$trangThai = '';
$badgeClass = '';
if ($now < $moDangKy) {
    $trangThai = 'Sắp mở đăng ký';
    $badgeClass = 'bg-gradient-secondary';
} elseif ($now >= $moDangKy && $now <= $dongDangKy) {
    $trangThai = 'Đang mở đăng ký';
    $badgeClass = 'bg-gradient-success';
} elseif ($now > $dongDangKy && $now < $batDau) {
    $trangThai = 'Đã đóng đăng ký';
    $badgeClass = 'bg-gradient-warning';
} elseif ($now >= $batDau && $now <= $ketThuc) {
    $trangThai = 'Đang diễn ra';
    $badgeClass = 'bg-gradient-info';
} else {
    $trangThai = 'Đã kết thúc';
    $badgeClass = 'bg-gradient-dark';
}
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php layout('navbar'); ?>

    <div class="container-fluid py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm">
                    <a class="opacity-5 text-dark" href="<?= _HOST_URL ?>?module=event">Sự kiện</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Chi tiết</li>
            </ol>
        </nav>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <div class="d-flex justify-content-between align-items-center px-3">
                                <h4 class="text-white text-capitalize mb-0">
                                    <?= htmlspecialchars($event['tenSK']) ?>
                                </h4>
                                <span class="badge <?= $badgeClass ?>"><?= $trangThai ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-4 py-4">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="mb-3">Mô tả</h6>
                                <p class="text-sm"><?= nl2br(htmlspecialchars($event['moTa'])) ?></p>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-sm mb-2">
                                            <i class="material-symbols-rounded text-sm me-1">calendar_today</i>
                                            Thời gian đăng ký
                                        </h6>
                                        <p class="text-sm mb-1">
                                            <strong>Mở:</strong> <?= date('d/m/Y H:i', strtotime($event['ngayMoDangKy'])) ?>
                                        </p>
                                        <p class="text-sm mb-0">
                                            <strong>Đóng:</strong> <?= date('d/m/Y H:i', strtotime($event['ngayDongDangKy'])) ?>
                                        </p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-sm mb-2">
                                            <i class="material-symbols-rounded text-sm me-1">event</i>
                                            Thời gian diễn ra
                                        </h6>
                                        <p class="text-sm mb-1">
                                            <strong>Bắt đầu:</strong> <?= date('d/m/Y H:i', strtotime($event['ngayBatDau'])) ?>
                                        </p>
                                        <p class="text-sm mb-0">
                                            <strong>Kết thúc:</strong> <?= date('d/m/Y H:i', strtotime($event['ngayKetThuc'])) ?>
                                        </p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-sm mb-2">
                                            <i class="material-symbols-rounded text-sm me-1">location_on</i>
                                            Cấp tổ chức
                                        </h6>
                                        <p class="text-sm mb-0"><?= htmlspecialchars($event['tenCap'] ?? 'N/A') ?></p>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h6 class="text-sm mb-2">
                                            <i class="material-symbols-rounded text-sm me-1">person</i>
                                            Người tạo
                                        </h6>
                                        <p class="text-sm mb-0"><?= htmlspecialchars($event['nguoiTaoTen']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-gradient-light">
                                    <div class="card-body">
                                        <h6 class="mb-3">Thao tác</h6>
                                        
                                        <?php if ($now >= $moDangKy && $now <= $dongDangKy && $user_role == 3): ?>
                                        <a href="<?= _HOST_URL ?>?module=event&action=register&id=<?= $event_id ?>" 
                                           class="btn btn-success w-100 mb-2">
                                            <i class="material-symbols-rounded me-1">app_registration</i>
                                            Đăng ký tham gia
                                        </a>
                                        <?php endif; ?>

                                        <?php if ($user_role == 1 || ($user_role == 2 && $event['nguoiTao'] == $user_id)): ?>
                                        <a href="<?= _HOST_URL ?>?module=event&action=edit&id=<?= $event_id ?>" 
                                           class="btn btn-info w-100 mb-2">
                                            <i class="material-symbols-rounded me-1">edit</i>
                                            Chỉnh sửa
                                        </a>
                                        <a href="<?= _HOST_URL ?>?module=event&action=delete&id=<?= $event_id ?>" 
                                           class="btn btn-danger w-100 mb-2"
                                           onclick="return confirm('Bạn có chắc muốn xóa sự kiện này?')">
                                            <i class="material-symbols-rounded me-1">delete</i>
                                            Xóa sự kiện
                                        </a>
                                        <?php endif; ?>

                                        <a href="<?= _HOST_URL ?>?module=event" class="btn btn-outline-secondary w-100">
                                            <i class="material-symbols-rounded me-1">arrow_back</i>
                                            Quay lại
                                        </a>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h6 class="mb-3">Thông tin thêm</h6>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="material-symbols-rounded text-sm me-2">info</i>
                                            <small class="text-muted">ID Sự kiện: <?= $event['idSK'] ?></small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="material-symbols-rounded text-sm me-2">check_circle</i>
                                            <small class="text-muted">Trạng thái: <?= $trangThai ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php layout('footer'); ?>
