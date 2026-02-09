<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Nếu đã login, redirect về dashboard
if (isLoggedIn()) {
    redirect(url('dashboard', 'index'));
}

$errors = [];
$oldData = [];

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy dữ liệu từ form
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Lưu lại old data để hiển thị lại form
    $oldData['username'] = $username;
    
    // Validation
    if (empty($username)) {
        $errors['username'] = 'Vui lòng nhập email hoặc tên đăng nhập';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Vui lòng nhập mật khẩu';
    }
    
    // Nếu không có lỗi validation, tiến hành đăng nhập
    if (empty($errors)) {
        
        // Query tìm user (có thể login bằng username hoặc email)
        $sql = "SELECT t.*, lt.tenLoaiTK 
                FROM taikhoan t 
                LEFT JOIN loaitaikhoan lt ON t.idLoaiTK = lt.idLoaiTK
                WHERE (t.tenDangNhap = :username OR t.email = :username)
                LIMIT 1";
        
        $user = db_query($sql, ['username' => $username]);
        
        if ($user && count($user) > 0) {
            $user = $user[0];
            
            // Kiểm tra trạng thái tài khoản
            if ($user['trangThai'] == 'LOCKED') {
                $errors['general'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
            } 
            else if ($user['trangThai'] == 'INACTIVE') {
                $errors['general'] = 'Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email.';
            }
            // Verify password
            else if (!password_verify($password, $user['matKhau'])) {
                $errors['password'] = 'Mật khẩu không chính xác';
            }
            // Đăng nhập thành công
            else {
                // Set session
                setUserSession($user);
                
                // Set flash message
                setFlash('login_success', 'Đăng nhập thành công! Chào mừng ' . $user['hoTen'], 'success');
                
                // Redirect về dashboard
                redirect(url('dashboard', 'index'));
            }
            
        } else {
            $errors['username'] = 'Tài khoản không tồn tại';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Đăng nhập - Hệ thống quản lý NCKH</title>
    
    <!-- Fonts -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    
    <!-- Font Awesome Icons -->
    <link href="<?php echo _HOST_URL; ?>/template/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?php echo _HOST_URL; ?>/template/assets/css/nucleo-svg.css" rel="stylesheet" />
    
    <!-- Material Dashboard CSS -->
    <link id="pagestyle" href="<?php echo _HOST_URL; ?>/template/assets/css/material-dashboard.min.css" rel="stylesheet" />
    
    <style>
        .bg-gradient-primary {
            background-image: linear-gradient(195deg, #ec407a 0%, #d81b60 100%);
        }
        .form-control:focus {
            border-color: #e91e63;
            box-shadow: 0 0 0 2px rgba(233, 30, 99, .25);
        }
    </style>
</head>

<body class="bg-gray-200">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100" style="background-image: url('https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80');">
            <span class="mask bg-gradient-dark opacity-6"></span>
            
            <div class="container my-auto">
                <div class="row">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Đăng nhập</h4>
                                    <p class="text-white text-center mb-2">Hệ thống quản lý sự kiện NCKH</p>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <span class="text-sm"><?php echo $errors['general']; ?></span>
                                    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <?php endif; ?>
                                
                                <form role="form" method="POST" action="" class="text-start">
                                    <!-- Username/Email -->
                                    <div class="input-group input-group-outline mb-3 <?php echo !empty($oldData['username']) ? 'is-filled' : ''; ?>">
                                        <label class="form-label">Email hoặc Tên đăng nhập</label>
                                        <input type="text" 
                                               name="username" 
                                               class="form-control <?php echo !empty($errors['username']) ? 'is-invalid' : ''; ?>"
                                               value="<?php echo e($oldData['username'] ?? ''); ?>">
                                    </div>
                                    <?php if (!empty($errors['username'])): ?>
                                    <p class="text-danger text-xs mt-n2 mb-2"><?php echo $errors['username']; ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Password -->
                                    <div class="input-group input-group-outline mb-3">
                                        <label class="form-label">Mật khẩu</label>
                                        <input type="password" 
                                               name="password" 
                                               class="form-control <?php echo !empty($errors['password']) ? 'is-invalid' : ''; ?>">
                                    </div>
                                    <?php if (!empty($errors['password'])): ?>
                                    <p class="text-danger text-xs mt-n2 mb-2"><?php echo $errors['password']; ?></p>
                                    <?php endif; ?>
                                    
                                    <!-- Remember me -->
                                    <div class="form-check form-switch d-flex align-items-center mb-3">
                                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                                        <label class="form-check-label mb-0 ms-2" for="rememberMe">Ghi nhớ đăng nhập</label>
                                    </div>
                                    
                                    <!-- Submit button -->
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Đăng nhập</button>
                                    </div>
                                    
                                    <!-- Forgot password -->
                                    <p class="mt-4 text-sm text-center">
                                        <a href="#" class="text-primary text-gradient font-weight-bold" style="pointer-events: none; opacity: 0.5;">Quên mật khẩu?</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Info box -->
                        <div class="card mt-3">
                            <div class="card-body p-3">
                                <p class="text-sm mb-2"><strong>Tài khoản demo:</strong></p>
                                <ul class="text-xs mb-0">
                                    <li>Admin: <code>admin</code> / <code>admin123</code></li>
                                    <li>Giảng viên: <code>gv001</code> / <code>gv123456</code></li>
                                    <li>Sinh viên: <code>sv001</code> / <code>sv123456</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Core JS Files -->
    <script src="<?php echo _HOST_URL; ?>/template/assets/js/core/popper.min.js"></script>
    <script src="<?php echo _HOST_URL; ?>/template/assets/js/core/bootstrap.min.js"></script>
    <script src="<?php echo _HOST_URL; ?>/template/assets/js/material-dashboard.min.js"></script>
</body>
</html>
