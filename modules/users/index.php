<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: " . _HOST_URL . "?module=auth&action=login");
    exit();
}

global $conn;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

// Xử lý tạo tài khoản (chỉ admin)
if (isset($_POST['create_user']) && $user_role == 1) {
    $tenTK = mysqli_real_escape_string($conn, $_POST['tenTK']);
    $matKhau = password_hash($_POST['matKhau'], PASSWORD_DEFAULT);
    $idLoaiTK = (int)$_POST['idLoaiTK'];
    $hoTen = mysqli_real_escape_string($conn, $_POST['hoTen']);
    
    mysqli_begin_transaction($conn);
    
    try {
        // Tạo tài khoản chính
        $sql = "INSERT INTO taikhoan (tenTK, matKhau, idLoaiTK, isActive) 
                VALUES ('$tenTK', '$matKhau', $idLoaiTK, 1)";
        
        if (!mysqli_query($conn, $sql)) {
            throw new Exception("Lỗi tạo tài khoản: " . mysqli_error($conn));
        }
        
        $idTK = mysqli_insert_id($conn);
        
        // Nếu là sinh viên (idLoaiTK = 3)
        if ($idLoaiTK == 3) {
            $MSV = mysqli_real_escape_string($conn, $_POST['MSV']);
            $idLop = (int)$_POST['idLop'];
            
            // Lấy idKhoa từ lớp
            $sql_lop = "SELECT idKhoa FROM lop WHERE idLop = $idLop";
            $result_lop = mysqli_query($conn, $sql_lop);
            if ($result_lop && mysqli_num_rows($result_lop) > 0) {
                $lop = mysqli_fetch_assoc($result_lop);
                $idKhoa = $lop['idKhoa'];
                
                $sql_sv = "INSERT INTO sinhvien (idTK, tenSV, MSV, idLop, idKhoa) 
                           VALUES ($idTK, '$hoTen', '$MSV', $idLop, $idKhoa)";
                
                if (!mysqli_query($conn, $sql_sv)) {
                    throw new Exception("Lỗi tạo hồ sơ sinh viên: " . mysqli_error($conn));
                }
            } else {
                throw new Exception("Lớp không tồn tại");
            }
        }
        // Nếu là giảng viên (idLoaiTK = 2)
        elseif ($idLoaiTK == 2) {
            $idKhoa = (int)$_POST['idKhoa'];
            
            $sql_gv = "INSERT INTO giangvien (idTK, tenGV, idKhoa) 
                       VALUES ($idTK, '$hoTen', $idKhoa)";
            
            if (!mysqli_query($conn, $sql_gv)) {
                throw new Exception("Lỗi tạo hồ sơ giảng viên: " . mysqli_error($conn));
            }
        }
        
        mysqli_commit($conn);
        $success_message = "Tạo tài khoản thành công!";
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error_message = $e->getMessage();
    }
}

$keyword = $_GET['keyword'] ?? '';

// Truy vấn danh sách người dùng
$sql = "
    SELECT 
        tk.idTK, 
        tk.tenTK, 
        tk.isActive, 
        ltk.tenLoaiTK,
        ltk.idLoaiTK,
        COALESCE(sv.tenSV, gv.tenGV, 'Admin') as hoTen
    FROM taikhoan tk
    LEFT JOIN loaitaikhoan ltk ON tk.idLoaiTK = ltk.idLoaiTK
    LEFT JOIN sinhvien sv ON tk.idTK = sv.idTK
    LEFT JOIN giangvien gv ON tk.idTK = gv.idTK
";

// Nếu không phải admin, không hiển thị tài khoản admin
if ($user_role != 1) {
    $sql .= " WHERE tk.idLoaiTK != 1 ";
}

if (!empty($keyword)) {
    $keyword = mysqli_real_escape_string($conn, $keyword);
    if ($user_role != 1) {
        $sql .= " AND (tk.tenTK LIKE '%$keyword%' OR sv.tenSV LIKE '%$keyword%' OR gv.tenGV LIKE '%$keyword%') ";
    } else {
        $sql .= " WHERE (tk.tenTK LIKE '%$keyword%' OR sv.tenSV LIKE '%$keyword%' OR gv.tenGV LIKE '%$keyword%') ";
    }
}

$sql .= " ORDER BY tk.idTK DESC";

$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Lấy danh sách khoa cho dropdown
$sql_khoa = "SELECT * FROM khoa ORDER BY tenKhoa";
$result_khoa = mysqli_query($conn, $sql_khoa);
$khoas = [];
if ($result_khoa) {
    while ($row = mysqli_fetch_assoc($result_khoa)) {
        $khoas[] = $row;
    }
}

// Lấy danh sách lớp cho dropdown
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
    'page_title' => 'Quản trị người dùng'
];

$active_page = 'users';

ob_start();
layout('header', $data);
layout('sidebar', ['active_page' => $active_page]);

?>

<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKDMSK6" height="0" width="0"
        style="display:none;visibility:hidden"></iframe></noscript>
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <?php layout('navbar', $data); ?>
    <div class="container-fluid py-2">
        
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
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Danh sách người dùng</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="row mb-3 px-3">

                            <!-- Ô tìm kiếm -->
                            <div class="col-md-6">
                                <form method="GET" action="">
                                    <input type="hidden" name="module" value="users">

                                    <div class="input-group input-group-outline">
                                        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>"
                                            class="form-control" placeholder="Tìm kiếm theo tên hoặc tên đăng nhập...">

                                        <button class="btn bg-gradient-dark mb-0" type="submit">
                                            <i class="material-symbols-rounded">search</i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Nút tạo tài khoản - chỉ admin -->
                            <?php if ($user_role == 1): ?>
                            <div class="col-md-6 text-end">
                                <button class="btn bg-gradient-primary" data-bs-toggle="modal"
                                    data-bs-target="#createUserModal">
                                    <i class="material-symbols-rounded">person_add</i>
                                    Thêm tài khoản
                                </button>
                            </div>
                            <?php endif; ?>

                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Họ và tên</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Tên đăng nhập</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Trạng thái</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Vai trò</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Hoạt động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($users)): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td>
                                                    <h6 class="mb-0 text-sm ps-3">
                                                        <?= htmlspecialchars($user['hoTen']) ?>
                                                    </h6>
                                                </td>

                                                <td>
                                                    <p class="text-sm mb-0">
                                                        <?= htmlspecialchars($user['tenTK']) ?>
                                                    </p>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($user['isActive'] == 1): ?>
                                                        <span class="badge bg-gradient-success">
                                                            Hoạt động
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-gradient-secondary">
                                                            Khóa
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?= htmlspecialchars($user['tenLoaiTK']) ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($user_role == 1): ?>
                                                        <a href="?module=users&action=edit&id=<?= $user['idTK'] ?>" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="material-symbols-rounded text-sm">edit</i>
                                                            Sửa
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="?module=users&action=view&id=<?= $user['idTK'] ?>" 
                                                           class="btn btn-sm btn-primary">
                                                            <i class="material-symbols-rounded text-sm">visibility</i>
                                                            Xem
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Không có dữ liệu
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Tạo Tài Khoản - Chỉ Admin -->
    <?php if ($user_role == 1): ?>
    <div class="modal fade" id="createUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <form method="POST" id="createUserForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm tài khoản mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Loại tài khoản <span class="text-danger">*</span></label>
                                <select name="idLoaiTK" id="idLoaiTK" class="form-select" required>
                                    <option value="">Chọn loại tài khoản</option>
                                    <option value="1">Quản trị viên</option>
                                    <option value="2">Giảng viên</option>
                                    <option value="3">Sinh viên</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" name="tenTK" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="matKhau" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="hoTen" class="form-control" required>
                            </div>
                        </div>

                        <!-- Phần dành cho Sinh viên -->
                        <div id="sinhvienFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã sinh viên <span class="text-danger">*</span></label>
                                    <input type="text" name="MSV" class="form-control">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lớp <span class="text-danger">*</span></label>
                                    <select name="idLop" class="form-select">
                                        <option value="">Chọn lớp</option>
                                        <?php foreach ($lops as $lop): ?>
                                        <option value="<?= $lop['idLop'] ?>">
                                            <?= htmlspecialchars($lop['tenLop']) ?> - <?= htmlspecialchars($lop['tenKhoa']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Phần dành cho Giảng viên -->
                        <div id="giangvienFields" style="display: none;">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                    <select name="idKhoa" class="form-select">
                                        <option value="">Chọn khoa</option>
                                        <?php foreach ($khoas as $khoa): ?>
                                        <option value="<?= $khoa['idKhoa'] ?>">
                                            <?= htmlspecialchars($khoa['tenKhoa']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
                            Hủy
                        </button>

                        <button type="submit" name="create_user" class="btn bg-gradient-primary">
                            <i class="material-symbols-rounded">save</i>
                            Lưu
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <?php endif; ?>
    
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

<!--   Core JS Files   -->
<script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/core/popper.min.js"></script>
<script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/core/bootstrap.min.js"></script>
<script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>

</div>
<?php layout('footer'); ?>
