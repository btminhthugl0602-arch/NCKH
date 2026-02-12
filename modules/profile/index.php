<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

require_once __DIR__ . '/../db_connect.php';

// Lấy thông tin user hiện tại
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

$user_data = [];
$success_message = '';
$error_message = '';

// Xử lý đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['currentPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];
    
    if ($new_password !== $confirm_password) {
        $error_message = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
    } else {
        $sql_check = "SELECT matKhau FROM taikhoan WHERE idTK = $user_id";
        $result_check = mysqli_query($conn, $sql_check);
        
        if ($result_check && mysqli_num_rows($result_check) > 0) {
            $row = mysqli_fetch_assoc($result_check);
            
            if ($row['matKhau'] === $current_password) {
                $new_password_escaped = mysqli_real_escape_string($conn, $new_password);
                $sql_update_pass = "UPDATE taikhoan SET matKhau = '$new_password_escaped' WHERE idTK = $user_id";
                
                if (mysqli_query($conn, $sql_update_pass)) {
                    $success_message = 'password_changed';
                } else {
                    $error_message = 'Có lỗi xảy ra khi đổi mật khẩu!';
                }
            } else {
                $error_message = 'Mật khẩu hiện tại không đúng!';
            }
        }
    }
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $tenTK = mysqli_real_escape_string($conn, $_POST['tenTK']);
    
    $sql_update_tk = "UPDATE taikhoan SET tenTK = '$tenTK' WHERE idTK = $user_id";
    
    if (mysqli_query($conn, $sql_update_tk)) {
        if ($user_role == 3) {
            $tenSV = mysqli_real_escape_string($conn, $_POST['tenSV']);
            $MSV = mysqli_real_escape_string($conn, $_POST['MSV']);
            
            $sql_update_sv = "UPDATE sinhvien SET tenSV = '$tenSV', MSV = '$MSV' WHERE idTK = $user_id";
            mysqli_query($conn, $sql_update_sv);
        }
        
        if ($user_role == 2) {
            $tenGV = mysqli_real_escape_string($conn, $_POST['tenGV']);
            $maGV = mysqli_real_escape_string($conn, $_POST['maGV']);
            
            $sql_update_gv = "UPDATE giangvien SET tenGV = '$tenGV', maGV = '$maGV' WHERE idTK = $user_id";
            mysqli_query($conn, $sql_update_gv);
        }
        
        $_SESSION['user_name'] = $tenTK;
        $success_message = 'profile_updated';
    } else {
        $error_message = 'Có lỗi xảy ra khi cập nhật thông tin!';
    }
}

// Lấy thông tin user từ database
if ($user_id > 0) {
    $sql = "SELECT * FROM taikhoan WHERE idTK = $user_id";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        
        if ($user_role == 3) {
            $sql_sv = "SELECT sv.*, l.tenLop, k.tenKhoa 
                       FROM sinhvien sv 
                       LEFT JOIN lop l ON sv.idLop = l.idLop 
                       LEFT JOIN khoa k ON sv.idKhoa = k.idKhoa 
                       WHERE sv.idTK = $user_id";
            $result_sv = mysqli_query($conn, $sql_sv);
            if ($result_sv && mysqli_num_rows($result_sv) > 0) {
                $sv_data = mysqli_fetch_assoc($result_sv);
                $user_data = array_merge($user_data, $sv_data);
            }
        }
        
        if ($user_role == 2) {
            $sql_gv = "SELECT gv.*, k.tenKhoa 
                       FROM giangvien gv 
                       LEFT JOIN khoa k ON gv.idKhoa = k.idKhoa 
                       WHERE gv.idTK = $user_id";
            $result_gv = mysqli_query($conn, $sql_gv);
            if ($result_gv && mysqli_num_rows($result_gv) > 0) {
                $gv_data = mysqli_fetch_assoc($result_gv);
                $user_data = array_merge($user_data, $gv_data);
            }
        }
    }
}

$data = [
    'page_title' => 'Hồ sơ',
    'active_page' => 'profile'
];

layout('header', $data);
layout('sidebar');

?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php layout('navbar'); ?>
    
    <div class="container-fluid px-4 mt-4">
        <?php if ($error_message && $error_message != 'password_changed' && $error_message != 'profile_updated'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Lỗi!</strong> <?= $error_message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <hr class="mt-0 mb-4">
        
        <div class="row">
            <div class="col-xl-4">
                <div class="card mb-4 mb-xl-0">
                    <div class="card-header">Ảnh đại diện</div>
                    <div class="card-body text-center">
                        <img class="rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;"
                            src="http://bootdey.com/img/Content/avatar/avatar1.png" alt="">
                        <div class="small font-italic text-muted mb-4">JPG hoặc PNG không quá 5 MB</div>
                        <button class="btn btn-primary" type="button">Tải ảnh lên</button>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header">Thông tin tài khoản</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Loại tài khoản:</strong>
                            <span class="badge bg-gradient-info ms-2">
                                <?php 
                                    if ($user_role == 1) echo 'Admin';
                                    elseif ($user_role == 2) echo 'Giảng viên';
                                    elseif ($user_role == 3) echo 'Sinh viên';
                                    else echo 'Khách';
                                ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <strong>Ngày tạo:</strong>
                            <p class="text-sm mb-0">
                                <?= isset($user_data['ngayTao']) ? date('d/m/Y', strtotime($user_data['ngayTao'])) : 'N/A' ?>
                            </p>
                        </div>
                        <?php if ($user_role == 3 && isset($user_data['GPA'])): ?>
                        <div class="mb-3">
                            <strong>GPA:</strong>
                            <p class="text-sm mb-0"><?= $user_data['GPA'] ?></p>
                        </div>
                        <div class="mb-3">
                            <strong>Điểm rèn luyện:</strong>
                            <p class="text-sm mb-0"><?= $user_data['DRL'] ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-8">
                <div class="card mb-4">
                    <div class="card-header">Thông tin chi tiết</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="small mb-1" for="tenTK">Tên tài khoản</label>
                                <input class="form-control" id="tenTK" name="tenTK" type="text"
                                    value="<?= htmlspecialchars($user_data['tenTK'] ?? '') ?>" required>
                            </div>
                            
                            <?php if ($user_role == 3): ?>
                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="tenSV">Họ và tên</label>
                                    <input class="form-control" id="tenSV" name="tenSV" type="text"
                                        value="<?= htmlspecialchars($user_data['tenSV'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="MSV">Mã sinh viên</label>
                                    <input class="form-control" id="MSV" name="MSV" type="text"
                                        value="<?= htmlspecialchars($user_data['MSV'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="tenLop">Lớp</label>
                                    <input class="form-control" id="tenLop" type="text"
                                        value="<?= htmlspecialchars($user_data['tenLop'] ?? 'Chưa có') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="tenKhoa">Khoa</label>
                                    <input class="form-control" id="tenKhoa" type="text"
                                        value="<?= htmlspecialchars($user_data['tenKhoa'] ?? 'Chưa có') ?>" readonly>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($user_role == 2): ?>
                            <div class="row gx-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small mb-1" for="tenGV">Họ và tên</label>
                                    <input class="form-control" id="tenGV" name="tenGV" type="text"
                                        value="<?= htmlspecialchars($user_data['tenGV'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small mb-1" for="maGV">Mã giảng viên</label>
                                    <input class="form-control" id="maGV" name="maGV" type="text"
                                        value="<?= htmlspecialchars($user_data['maGV'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small mb-1" for="tenKhoaGV">Khoa</label>
                                <input class="form-control" id="tenKhoaGV" type="text"
                                    value="<?= htmlspecialchars($user_data['tenKhoa'] ?? 'Chưa có') ?>" readonly>
                            </div>
                            <?php endif; ?>
                            
                            <button class="btn btn-primary" type="submit" name="update_profile">
                                <i class="material-symbols-rounded me-1">save</i>
                                Lưu thay đổi
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">Đổi mật khẩu</div>
                    <div class="card-body">
                        <form method="POST" action="" id="changePasswordForm">
                            <div class="mb-3">
                                <label class="small mb-1" for="currentPassword">Mật khẩu hiện tại</label>
                                <input class="form-control" id="currentPassword" name="currentPassword" type="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="newPassword">Mật khẩu mới</label>
                                <input class="form-control" id="newPassword" name="newPassword" type="password" required>
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="confirmPassword">Xác nhận mật khẩu mới</label>
                                <input class="form-control" id="confirmPassword" name="confirmPassword" type="password" required>
                            </div>
                            <button class="btn btn-primary" type="submit" name="change_password">
                                <i class="material-symbols-rounded me-1">lock</i>
                                Đổi mật khẩu
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php if ($success_message == 'password_changed'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('Đổi mật khẩu thành công!', 'success');
    document.getElementById('changePasswordForm').reset();
});
</script>
<?php endif; ?>

<?php if ($success_message == 'profile_updated'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showToast('Cập nhật thông tin thành công!', 'success');
});
</script>
<?php endif; ?>

<script>
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        showToast('Mật khẩu mới và xác nhận mật khẩu không khớp!', 'danger');
    }
});
</script>

<?php layout('footer'); ?>
