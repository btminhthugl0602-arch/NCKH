<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

require_once __DIR__ . '/../db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Xử lý tạo sự kiện mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event']) && $user_role == 1) {
    $tenSK = mysqli_real_escape_string($conn, $_POST['tenSK']);
    $moTa = mysqli_real_escape_string($conn, $_POST['moTa']);
    $idCap = (int)$_POST['idCap'];
    $ngayMoDangKy = mysqli_real_escape_string($conn, $_POST['ngayMoDangKy']);
    $ngayDongDangKy = mysqli_real_escape_string($conn, $_POST['ngayDongDangKy']);
    $ngayBatDau = mysqli_real_escape_string($conn, $_POST['ngayBatDau']);
    $ngayKetThuc = mysqli_real_escape_string($conn, $_POST['ngayKetThuc']);
    
    $sql_insert = "INSERT INTO sukien (tenSK, moTa, idCap, nguoiTao, ngayMoDangKy, ngayDongDangKy, ngayBatDau, ngayKetThuc, isActive) 
                   VALUES ('$tenSK', '$moTa', $idCap, $user_id, '$ngayMoDangKy', '$ngayDongDangKy', '$ngayBatDau', '$ngayKetThuc', 1)";
    
    if (mysqli_query($conn, $sql_insert)) {
        $event_created = true;
    }
}

// Lấy từ khóa tìm kiếm
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$data = [
    'page_title' => ($user_role == 1) ? 'Quản lý sự kiện' : 'Sự kiện'
];

$active_page = 'event';

// Lấy danh sách sự kiện với tìm kiếm
$sql = "SELECT sk.*, ct.tenCap, tk.tenTK as nguoiTaoTen
        FROM sukien sk
        LEFT JOIN cap_tochuc ct ON sk.idCap = ct.idCap
        LEFT JOIN taikhoan tk ON sk.nguoiTao = tk.idTK
        WHERE sk.isActive = 1";

if (!empty($search)) {
    $sql .= " AND (sk.tenSK LIKE '%$search%' OR sk.moTa LIKE '%$search%')";
}

$sql .= " ORDER BY sk.idSK DESC";

$result = mysqli_query($conn, $sql);
$events = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = $row;
    }
}

// Lấy danh sách cấp tổ chức cho dropdown
$sql_cap = "SELECT * FROM cap_tochuc ORDER BY tenCap";
$result_cap = mysqli_query($conn, $sql_cap);
$caps = [];
if ($result_cap) {
    while ($row = mysqli_fetch_assoc($result_cap)) {
        $caps[] = $row;
    }
}

layout('header', $data);
layout('sidebar');
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

    <?php layout('navbar'); ?>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0"><?= ($user_role == 1) ? 'Danh sách sự kiện' : 'Sự kiện' ?></h3>
            <?php if ($user_role == 1): ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEventModal">
                <i class="material-symbols-rounded me-1">add</i>
                Tạo sự kiện mới
            </button>
            <?php endif; ?>
        </div>

        <!-- Thanh tìm kiếm -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="">
                            <input type="hidden" name="module" value="event">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">
                                    <i class="material-symbols-rounded">search</i>
                                </span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Tìm kiếm sự kiện theo tên hoặc mô tả..." 
                                       value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-primary mb-0" type="submit">Tìm kiếm</button>
                                <?php if (!empty($search)): ?>
                                <a href="<?= _HOST_URL ?>?module=event" class="btn btn-outline-secondary mb-0">
                                    Xóa tìm kiếm
                                </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($search)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            Tìm thấy <strong><?= count($events) ?></strong> kết quả cho từ khóa "<strong><?= htmlspecialchars($search) ?></strong>"
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (empty($events)): ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="material-symbols-rounded text-muted" style="font-size: 64px;">event_busy</i>
                <h5 class="text-muted mt-3">
                    <?php if (!empty($search)): ?>
                        Không tìm thấy sự kiện nào
                    <?php else: ?>
                        Chưa có sự kiện nào
                    <?php endif; ?>
                </h5>
                <p class="text-sm text-muted">
                    <?php if ($user_role == 1): ?>
                        Hãy tạo sự kiện đầu tiên của bạn!
                    <?php else: ?>
                        Hiện tại chưa có sự kiện nào được tổ chức.
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): 
                $ngayMoDangKy = date('d/m/Y', strtotime($event['ngayMoDangKy']));
                $ngayDongDangKy = date('d/m/Y', strtotime($event['ngayDongDangKy']));
                $ngayBatDau = date('d/m/Y', strtotime($event['ngayBatDau']));
                $ngayKetThuc = date('d/m/Y', strtotime($event['ngayKetThuc']));
                
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
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card h-100" data-animation="true">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <a class="d-block blur-shadow-image">
                            <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                        </a>
                    </div>
                    <div class="card-body text-center">
                        <div class="d-flex mt-n6 mx-auto justify-content-center">
                            <span class="badge <?= $badgeClass ?>"><?= $trangThai ?></span>
                        </div>
                        <h5 class="font-weight-normal mt-3">
                            <?= htmlspecialchars($event['tenSK']) ?>
                        </h5>
                        <p class="mb-0 text-sm">
                            <?= htmlspecialchars(substr($event['moTa'], 0, 100)) ?>
                            <?= strlen($event['moTa']) > 100 ? '...' : '' ?>
                        </p>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="material-symbols-rounded text-sm">calendar_today</i>
                                Đăng ký: <?= $ngayMoDangKy ?> - <?= $ngayDongDangKy ?>
                            </small>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="material-symbols-rounded text-sm">event</i>
                                    <?= $ngayBatDau ?> - <?= $ngayKetThuc ?>
                                </small>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="material-symbols-rounded text-sm">location_on</i>
                                    <?= htmlspecialchars($event['tenCap'] ?? 'N/A') ?>
                                </small>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="material-symbols-rounded text-sm">person</i>
                                Người tạo: <?= htmlspecialchars($event['nguoiTaoTen']) ?>
                            </small>
                        </div>
                        <div class="mt-2">
                            <a href="<?= _HOST_URL ?>?module=event&action=view&id=<?= $event['idSK'] ?>" 
                               class="btn btn-sm btn-info w-100">
                                <i class="material-symbols-rounded text-sm">visibility</i>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>

</main>

<!-- Modal Tạo Sự Kiện -->
<?php if ($user_role == 1): ?>
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">Tạo Sự Kiện Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="tenSK" class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tenSK" name="tenSK" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="moTa" class="form-label">Mô tả <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="moTa" name="moTa" rows="3" required></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="idCap" class="form-label">Cấp tổ chức <span class="text-danger">*</span></label>
                            <select class="form-select" id="idCap" name="idCap" required>
                                <option value="">Chọn cấp tổ chức</option>
                                <?php foreach ($caps as $cap): ?>
                                <option value="<?= $cap['idCap'] ?>"><?= htmlspecialchars($cap['tenCap']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ngayMoDangKy" class="form-label">Ngày mở đăng ký <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngayMoDangKy" name="ngayMoDangKy" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ngayDongDangKy" class="form-label">Ngày đóng đăng ký <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngayDongDangKy" name="ngayDongDangKy" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ngayBatDau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngayBatDau" name="ngayBatDau" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ngayKetThuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="ngayKetThuc" name="ngayKetThuc" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="create_event" class="btn btn-primary">
                        <i class="material-symbols-rounded me-1">add</i>
                        Tạo sự kiện
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($event_created) && $event_created): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('Tạo sự kiện thành công!', 'success');
    setTimeout(function() {
        window.location.href = '<?= _HOST_URL ?>?module=event';
    }, 1500);
});
</script>
<?php endif; ?>

<?php
layout('footer');
?>
