<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

require_once __DIR__ . '/../db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Chỉ admin hoặc người tạo sự kiện mới được sửa
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

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_event'])) {
    $tenSK = mysqli_real_escape_string($conn, $_POST['tenSK']);
    $moTa = mysqli_real_escape_string($conn, $_POST['moTa']);
    $idCap = (int)$_POST['idCap'];
    $ngayMoDangKy = mysqli_real_escape_string($conn, $_POST['ngayMoDangKy']);
    $ngayDongDangKy = mysqli_real_escape_string($conn, $_POST['ngayDongDangKy']);
    $ngayBatDau = mysqli_real_escape_string($conn, $_POST['ngayBatDau']);
    $ngayKetThuc = mysqli_real_escape_string($conn, $_POST['ngayKetThuc']);
    
    $sql_update = "UPDATE sukien SET 
                   tenSK = '$tenSK',
                   moTa = '$moTa',
                   idCap = $idCap,
                   ngayMoDangKy = '$ngayMoDangKy',
                   ngayDongDangKy = '$ngayDongDangKy',
                   ngayBatDau = '$ngayBatDau',
                   ngayKetThuc = '$ngayKetThuc'
                   WHERE idSK = $event_id";
    
    if (mysqli_query($conn, $sql_update)) {
        $success_message = "Cập nhật sự kiện thành công!";
    } else {
        $error_message = "Lỗi cập nhật sự kiện: " . mysqli_error($conn);
    }
}

// Lấy thông tin sự kiện
$sql = "SELECT sk.*, ct.tenCap 
        FROM sukien sk
        LEFT JOIN cap_tochuc ct ON sk.idCap = ct.idCap
        WHERE sk.idSK = $event_id AND sk.isActive = 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

$event = mysqli_fetch_assoc($result);

// Kiểm tra quyền sửa
if ($user_role == 2 && $event['nguoiTao'] != $user_id) {
    header("Location: " . _HOST_URL . "?module=event");
    exit();
}

// Lấy danh sách cấp tổ chức
$sql_cap = "SELECT * FROM cap_tochuc ORDER BY tenCap";
$result_cap = mysqli_query($conn, $sql_cap);
$caps = [];
if ($result_cap) {
    while ($row = mysqli_fetch_assoc($result_cap)) {
        $caps[] = $row;
    }
}

$data = [
    'page_title' => 'Sửa sự kiện',
    'active_page' => 'event'
];

layout('header', $data);
layout('sidebar');
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php layout('navbar'); ?>

    <div class="container-fluid py-4">
        
        <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Thành công!</strong> <?= $success_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Lỗi!</strong> <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                            <h6 class="text-white ps-3">Sửa thông tin sự kiện</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="tenSK" class="form-label">Tên sự kiện <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tenSK" name="tenSK" 
                                           value="<?= htmlspecialchars($event['tenSK']) ?>" required>
                                </div>
                                
                                <div class="col-12 mb-3">
                                    <label for="moTa" class="form-label">Mô tả <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="moTa" name="moTa" rows="3" required><?= htmlspecialchars($event['moTa']) ?></textarea>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="idCap" class="form-label">Cấp tổ chức <span class="text-danger">*</span></label>
                                    <select class="form-select" id="idCap" name="idCap" required>
                                        <option value="">Chọn cấp tổ chức</option>
                                        <?php foreach ($caps as $cap): ?>
                                        <option value="<?= $cap['idCap'] ?>" <?= $event['idCap'] == $cap['idCap'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cap['tenCap']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ngayMoDangKy" class="form-label">Ngày mở đăng ký <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngayMoDangKy" name="ngayMoDangKy" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($event['ngayMoDangKy'])) ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ngayDongDangKy" class="form-label">Ngày đóng đăng ký <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngayDongDangKy" name="ngayDongDangKy" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($event['ngayDongDangKy'])) ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ngayBatDau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngayBatDau" name="ngayBatDau" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($event['ngayBatDau'])) ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="ngayKetThuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="ngayKetThuc" name="ngayKetThuc" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($event['ngayKetThuc'])) ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <a href="?module=event&action=view&id=<?= $event_id ?>" class="btn bg-gradient-secondary">
                                        <i class="material-symbols-rounded">arrow_back</i>
                                        Quay lại
                                    </a>
                                    <button type="submit" name="update_event" class="btn bg-gradient-primary">
                                        <i class="material-symbols-rounded">save</i>
                                        Cập nhật
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<?php layout('footer'); ?>
