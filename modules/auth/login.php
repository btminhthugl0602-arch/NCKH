<?php
if (!defined('_AUTHEN')) {
  die('Truy cập không hợp lệ');
}

// Xử lý đăng nhập
$tb_dang_nhap = "";
$error_class = "danger";

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
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/favicon.png">
    <title>Đăng nhập - Hệ thống quản lý sự kiện</title>

    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link id="pagestyle" href="<?= _HOST_URL_TEMPLATES ?>/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

</head>

<body class="login-bg">
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <nav
                    class="navbar navbar-expand-lg blur border-radius-lg top-0 z-index-3 shadow position-absolute mt-4 py-2 start-0 end-0 mx-4">
                    <div class="container-fluid ps-2 pe-0">
                        <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="<?= _HOST_URL ?>">
                            <i class="fas fa-calendar-alt me-2"></i>Hệ thống quản lý sự kiện
                        </a>
                        <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon mt-2">
                                <span class="navbar-toggler-bar bar1"></span>
                                <span class="navbar-toggler-bar bar2"></span>
                                <span class="navbar-toggler-bar bar3"></span>
                            </span>
                        </button>
                        <div class="collapse navbar-collapse" id="navigation">
                            <ul class="navbar-nav mx-auto">
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2" href="<?= _HOST_URL ?>">
                                        <i class="fa fa-home opacity-6 text-dark me-1"></i>
                                        Trang Chủ
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link d-flex align-items-center me-2 active" aria-current="page"
                                        href="<?= _HOST_URL ?>?module=auth&action=login">
                                        <i class="fas fa-sign-in-alt opacity-6 text-dark me-1"></i>
                                        Đăng nhập
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <main class="main-content mt-0">
        <section class="vh-100" style="background-color: #9A616D;">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col col-xl-10">
                        <div class="card" style="border-radius: 1rem;">
                            <div class="row g-0">
                                <div class="col-md-6 col-lg-5 d-none d-md-block">
                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/img1.webp"
                                        alt="login form" class="img-fluid" style="border-radius: 1rem 0 0 1rem;" />
                                </div>
                                <div class="col-md-6 col-lg-7 d-flex align-items-center">
                                    <div class="card-body p-4 p-lg-5 text-black">

                                        <form>

                                            <div class="d-flex align-items-center mb-3 pb-1">
                                                <i class="fas fa-cubes fa-2x me-3" style="color: #ff6219;"></i>
                                                <span class="h1 fw-bold mb-0">Logo</span>
                                            </div>

                                            <h5 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Sign into your
                                                account</h5>

                                            <div data-mdb-input-init class="form-outline mb-4">
                                                <input type="email" id="form2Example17"
                                                    class="form-control form-control-lg" />
                                                <label class="form-label" for="form2Example17">Email address</label>
                                            </div>

                                            <div data-mdb-input-init class="form-outline mb-4">
                                                <input type="password" id="form2Example27"
                                                    class="form-control form-control-lg" />
                                                <label class="form-label" for="form2Example27">Password</label>
                                            </div>

                                            <div class="pt-1 mb-4">
                                                <button data-mdb-button-init data-mdb-ripple-init
                                                    class="btn btn-dark btn-lg btn-block" type="button">Login</button>
                                            </div>

                                            <a class="small text-muted" href="#!">Forgot password?</a>
                                            <p class="mb-5 pb-lg-2" style="color: #393f81;">Don't have an account? <a
                                                    href="#!" style="color: #393f81;">Register here</a></p>
                                            <a href="#!" class="small text-muted">Terms of use.</a>
                                            <a href="#!" class="small text-muted">Privacy policy</a>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Core JS Files -->
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/popper.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/bootstrap.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>