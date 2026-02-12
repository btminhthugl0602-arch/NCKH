<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Chỉ admin mới được truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: " . _HOST_URL . "?module=users");
    exit();
}

global $conn;

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id == 0) {
    header("Location: " . _HOST_URL . "?module=users");
    exit();
}

// Xử lý cập nhật
if (isset($_POST['update_user'])) {
    $tenTK = mysqli_real_escape_string($conn, $_POST['tenTK']);
    $hoTen = mysqli_real_escape_string($conn, $_POST['hoTen']);
    $isActive = (int)$_POST['isActive'];
    $idLoaiTK = (int)$_POST['idLoaiTK'];
    
    mysqli_begin_transaction($conn);
    
    try {
        // Cập nhật bảng taikhoan
        $sql = "UPDATE taikhoan SET tenTK = '$tenTK', isActive = $isActive, idLoaiTK = $idLoaiTK 
                WHERE idTK = $user_id";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Lỗi cập nhật tài khoản: " . mysqli_error($conn));
        }
        
        // Cập nhật mật khẩu nếu có
        if (!empty($_POST['matKhau'])) {
            $matKhau = password_hash($_POST['matKhau'], PASSWORD_DEFAULT);
            $sql_pass = "UPDATE taikhoan SET matKhau = '$matKhau' WHERE idTK = $user_id";
            mysqli_query($conn, $sql_pass);
        }
        
        // Cập nhật thông tin sinh viên
        if ($idLoaiTK == 3 && isset($_POST['MSV'])) {
            $MSV = mysqli_real_escape_string($conn, $_POST['MSV']);
            $idLop = (int)$_POST['idLop'];
            
            // Lấy idKhoa từ lớp
            $sql_lop = "SELECT idKhoa FROM lop WHERE idLop = $idLop";
            $result_lop = mysqli_query($conn, $sql_lop);
            if ($result_lop && mysqli_num_rows($result_lop) > 0) {
                $lop = mysqli_fetch_assoc($result_lop);
                $idKhoa = $lop['idKhoa'];
                
                $sql_sv = "UPDATE sinhvien SET tenSV = '$hoTen', MSV = '$MSV', 
                           idLop = $idLop, idKhoa = $idKhoa WHERE idTK = $user_id";
                
                if (!mysqli_query($conn, $sql_sv)) {
                    throw new Exception("Lỗi cập nhật hồ sơ sinh viên: " . mysqli_error($conn));
                }
            }
        }
        // Cập nhật thông tin giảng viên
        elseif ($idLoaiTK == 2 && isset($_POST['idKhoa'])) {
            $idKhoa = (int)$_POST['idKhoa'];
            
            $sql_gv = "UPDATE giangvien SET tenGV = '$hoTen', idKhoa = $idKhoa WHERE idTK = $user_id";
            
            if (!mysqli_query($conn, $sql_gv)) {
                throw new Exception("Lỗi cập nhật hồ sơ giảng viên: " . mysqli_error($conn));
            }
        }
        
        mysqli_commit($conn);
        $success_message = "Cập nhật tài khoản thành công!";
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = $e->getMessage();
    }
}

// Lấy thông tin user
$sql = "SELECT tk.*, ltk.tenLoaiTK, sv.tenSV, sv.MSV, sv.idLop, sv.idKhoa as svKhoa,
        gv.tenGV, gv.idKhoa as gvKhoa
        FROM taikhoan tk
        LEFT JOIN loaitaikhoan ltk ON tk.idLoaiTK = ltk.idLoaiTK
        LEFT JOIN sinhvien sv ON tk.idTK = sv.idTK
        LEFT JOIN giangvien gv ON tk.idTK = gv.idTK
        WHERE tk.idTK = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: " . _HOST_URL . "?module=users");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Lấy danh sách khoa
$sql_khoa = "SELECT * FROM khoa ORDER BY tenKhoa";
$result_khoa = mysqli_query($conn, $sql_khoa);
$khoas = [];
if ($result_khoa) {
    while ($row = mysqli_fetch_assoc($result_khoa)) {
        $khoas[] = $row;
    }
}

// Lấy danh sách lớp
$sql_lop = "SELECT l.*, k.tenKhoa FROM lop l 
            LEFT JOIN khoa k ON l.idKhoa = k.idKhoa 
            ORDER BY l.tenLop";
$result_lop = mysqli_query($conn, $sql_lop);
$lops = [];
if ($result_lop) {
    while ($row = mysqli_fetch_assoc($result_lop)) {
        $lops[] = $row;
    }
}

$data = [
    'page_title' => 'Sửa tài khoản'
];

$active_page = 'users';

layout('header', $data);
layout('sidebar', ['active_page' => $active_page]);
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php layout('navbar', $data); ?>
    
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
                            <h6 class="text-white ps-3">Sửa thông tin tài khoản</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Loại tài khoản <span class="text-danger">*</span></label>
                                    <select name="idLoaiTK" id="idLoaiTK" class="form-select" required>
                                        <option value="1" <?= $user['idLoaiTK'] == 1 ? 'selected' : '' ?>>Quản trị viên</option>
                                        <option value="2" <?= $user['idLoaiTK'] == 2 ? 'selected' : '' ?>>Giảng viên</option>
                                        <option value="3" <?= $user['idLoaiTK'] == 3 ? 'selected' : '' ?>>Sinh viên</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" name="tenTK" class="form-control" 
                                           value="<?= htmlspecialchars($user['tenTK']) ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                                    <input type="password" name="matKhau" class="form-control">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="hoTen" class="form-control" 
                                           value="<?= htmlspecialchars($user['tenSV'] ?? $user['tenGV'] ?? '') ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                    <select name="isActive" class="form-select" required>
                                        <option value="1" <?= $user['isActive'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
                                        <option value="0" <?= $user['isActive'] == 0 ? 'selected' : '' ?>>Khóa</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Phần dành cho Sinh viên -->
                            <div id="sinhvienFields" style="display: <?= $user['idLoaiTK'] == 3 ? 'block' : 'none' ?>;">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                                        <input type="text" name="MSV" class="form-control" 
                                               value="<?= htmlspecialchars($user['MSV'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Lớp <span class="text-danger">*</span></label>
                                        <select name="idLop" class="form-select">
                                            <option value="">Chọn lớp</option>
                                            <?php foreach ($lops as $lop): ?>
                                            <option value="<?= $lop['idLop'] ?>" 
                                                    <?= (isset($user['idLop']) && $user['idLop'] == $lop['idLop']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($lop['tenLop']) ?> - <?= htmlspecialchars($lop['tenKhoa']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Phần dành cho Giảng viên -->
                            <div id="giangvienFields" style="display: <?= $user['idLoaiTK'] == 2 ? 'block' : 'none' ?>;">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                        <select name="idKhoa" class="form-select">
                                            <option value="">Chọn khoa</option>
                                            <?php foreach ($khoas as $khoa): ?>
                                            <option value="<?= $khoa['idKhoa'] ?>" 
                                                    <?= (isset($user['gvKhoa']) && $user['gvKhoa'] == $khoa['idKhoa']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($khoa['tenKhoa']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <a href="?module=users" class="btn bg-gradient-secondary">
                                        <i class="material-symbols-rounded">arrow_back</i>
                                        Quay lại
                                    </a>
                                    <button type="submit" name="update_user" class="btn bg-gradient-primary">
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

<script>
// Hiển thị/ẩn các trường theo loại tài khoản
document.addEventListener('DOMContentLoaded', function() {
    const idLoaiTK = document.getElementById('idLoaiTK');
    const sinhvienFields = document.getElementById('sinhvienFields');
    const giangvienFields = document.getElementById('giangvienFields');
    
    if (idLoaiTK) {
        idLoaiTK.addEventListener('change', function() {
            const value = this.value;
            
            // Ẩn tất cả
            sinhvienFields.style.display = 'none';
            giangvienFields.style.display = 'none';
            
            // Bỏ required cho tất cả các trường
            sinhvienFields.querySelectorAll('input, select').forEach(el => {
                el.removeAttribute('required');
            });
            giangvienFields.querySelectorAll('input, select').forEach(el => {
                el.removeAttribute('required');
            });
            
            // Hiển thị và thêm required cho trường tương ứng
            if (value == '3') { // Sinh viên
                sinhvienFields.style.display = 'block';
                sinhvienFields.querySelectorAll('input, select').forEach(el => {
                    el.setAttribute('required', 'required');
                });
            } else if (value == '2') { // Giảng viên
                giangvienFields.style.display = 'block';
                giangvienFields.querySelectorAll('select').forEach(el => {
                    el.setAttribute('required', 'required');
                });
            }
        });
    }
});
</script>

<?php layout('footer'); ?>
