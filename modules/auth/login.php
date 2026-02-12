<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}
// Xử lý đăng nhập
$tb_dang_nhap = "";
$error_class = "danger";

// Xử lý đăng nhập Guest
if (isset($_GET['guest']) && $_GET['guest'] == '1') {
    $_SESSION['user_id'] = 0;
    $_SESSION['user_name'] = 'Khách';
    $_SESSION['role'] = 'guest';
    
    // Chuyển hướng dựa trên tham số
    if (isset($_GET['redirect']) && $_GET['redirect'] == 'event') {
        header("Location: " . _HOST_URL . "?module=event&action=index");
    } else {
        header("Location: " . _HOST_URL . "?module=dashboard&action=index");
    }
    exit();
}

if (isset($_POST['btn_dang_nhap'])) {

    $ten_tk = isset($_POST['tendangnhap']) ? chuan_hoa_chuoi_sql($conn, $_POST['tendangnhap']) : "";
    $mat_khau = isset($_POST['matkhau']) ? $_POST['matkhau'] : "";

    if ($ten_tk == "" || $mat_khau == "") {
        $tb_dang_nhap = "Vui lòng nhập đầy đủ thông tin!!";
    } else {

        $row = truy_van_mot_ban_ghi($conn, 'taikhoan', 'tenTK', $ten_tk);

        if ($row) {
            if ($mat_khau == $row['matKhau']) {

                if ($row['isActive'] == 0) {
                    $tb_dang_nhap = "Tài khoản của bạn đã bị khóa.";
                } else {
                    $_SESSION['user_id'] = $row['idTK'];
                    $_SESSION['user_name'] = $row['tenTK'];
                    $_SESSION['role'] = $row['idLoaiTK'];

                    header("Location: " . _HOST_URL . "?module=dashboard&action=index");
                    exit();
                }
            } else {
                $tb_dang_nhap = "Mật khẩu không chính xác";
            }
        } else {
            $tb_dang_nhap = "Tên đăng nhập không tồn tại";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/favicon.png">
    <title>
        Đăng nhập
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="<?= _HOST_URL_TEMPLATES ?>/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/switch.css" rel="stylesheet" />
    <style>
    /* CSS cho responsive background */
    @media (max-width: 991px) {

        /* Ẩn thanh cuộn */
        body {
            overflow-x: hidden;
        }

        section {
            overflow: hidden;
        }

        /* Background tràn viền full screen - chiều cao vừa đủ */
        .page-header {
            background-image: url('<?php echo _HOST_URL_TEMPLATES; ?>/assets/img/doraemon.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            min-height: 100vh;
            height: auto;
            padding: 120px 20px 40px;
        }

        /* Lớp phủ mờ cho background */
        .page-header::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.2);
            z-index: 0;
        }

        /* Container phải có z-index cao hơn overlay */
        .page-header .container {
            position: relative;
            z-index: 1;
        }

        /* Card form trong suốt - thu gọn để vừa màn hình */
        .card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        /* Thu nhỏ padding của card để tiết kiệm không gian */
        .card-header,
        .card-body,
        .card-footer {
            padding: 1rem !important;
        }

        .card-header h4 {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }

        .card-header p {
            font-size: 0.875rem;
        }

        /* Form input nhỏ gọn hơn */
        .input-group {
            margin-bottom: 0.75rem !important;
        }

        .form-group {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* Button nhỏ hơn */
        .btn-lg {
            padding: 0.75rem 1rem !important;
            font-size: 1rem !important;
        }

        /* Fix navbar không bị lệch */
        .container.position-sticky {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }

        .navbar {
            background: #1565C0 !important;
            backdrop-filter: blur(10px);
            margin: 0 !important;
            border-radius: 0 !important;
            padding: 0.75rem 1rem !important;
        }

        /* Main content có margin-top để tránh navbar đè */
        main.main-content {
            margin-top: 0 !important;
        }
    }
    </style>
</head>

<body class="">
    <div class="container position-sticky z-index-sticky top-0 ">
        <div class="row ">
            <div class="col-12 white">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg border-radius-xl top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4"
                    style="background-color : #1565C0">
                    <div class="container-fluid ps-2 pe-0">
                        <a class="nav-link d-flex align-items-center me-2 active text-white " href="<?= _HOST_URL ?>">
                            Hệ thống quản lý sự kiện
                        </a>
                        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon mt-2">
                                <span class="navbar-toggler-bar bar1" style="background: #fff;"></span>
                                <span class="navbar-toggler-bar bar2" style="background: #fff;"></span>
                                <span class="navbar-toggler-bar bar3" style="background: #fff;"></span>
                            </span>
                        </button>
                        <div class="collapse navbar-collapse" id="navigation">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2 active text-white"
                                        aria-current="page" href="<?= _HOST_URL ?>?module=auth&action=login&guest=1">
                                        <i class="fa fa-chart-pie opacity-6 text-white me-1"></i>
                                        Trang chủ
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link me-2 text-white"
                                        href="<?= _HOST_URL ?>?module=auth&action=login&guest=1&redirect=event">
                                        <i class=" fa fa-calendar opacity-6 text-white me-1"></i>
                                        Sự kiện
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link me-2 text-white"
                                        href="<?= _HOST_URL ?>?module=auth&action=login">
                                        <i class="fas fa-key opacity-6 text-white me-1"></i>
                                        Đăng nhập
                                    </a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100 bg-color-primary">
                <div class="container">
                    <div class="row">
                        <div
                            class="col-6 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-3 px-7 border-radius-lg d-flex flex-column justify-content-center"
                                style="background-image: url('<?php echo _HOST_URL_TEMPLATES; ?>/assets/img/doraemon.jpg'); background-size: cover;">
                            </div>
                        </div>
                        <div
                            class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-5 login-form-wrapper">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="font-weight-bolder">Sign In</h4>
                                    <p class="mb-0">Vui lòng nhập tài khoản và mật khẩu để đăng nhập</p>
                                </div>
                                <div class="card-body">
                                    <?php if ($tb_dang_nhap != ""): ?>
                                    <div class="alert alert-<?= $error_class ?> alert-dismissible fade show"
                                        role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i><?= $tb_dang_nhap ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                    <?php endif; ?>
                                    <form action="" method="POST" role="form">
                                        <div class="form-group text-primary">
                                            Tài khoản
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <input type="text" name="tendangnhap" class="form-control"
                                                placeholder="Nhập tài khoản..."
                                                value="<?= isset($_POST['tendangnhap']) ? htmlspecialchars($_POST['tendangnhap']) : '' ?>">
                                        </div>

                                        <div class="form-group text-primary">
                                            Mật khẩu
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <input type="password" name="matkhau" class="form-control"
                                                placeholder="Nhập mật khẩu...">
                                        </div>

                                        <div class="custom-switch-wrapper">
                                            <label class="switch-minimal">
                                                <input type="checkbox" name="remember_me" id="rememberMe" checked>
                                                <span class="slider-minimal"></span>
                                            </label>
                                            <label class="custom-switch-label" for="rememberMe">
                                                Ghi nhớ đăng nhập
                                            </label>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" name="btn_dang_nhap"
                                                class="btn btn-lg btn-lg w-100 mt-4 mb-0 text-white"
                                                style="background-color: #4DABF7;">
                                                Đăng nhập
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-2 text-sm mx-auto">
                                        <a href="<?= _HOST_URL ?>?module=auth&action=login&guest=1"
                                            class="text-primary text-gradient font-weight-bold">Đăng nhập với tư cách
                                            khách</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!--   Core JS Files   -->
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/popper.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/bootstrap.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>

    <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>


</html>