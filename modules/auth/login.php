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

  <style>
    .login-bg {
    background: linear-gradient(135deg, #cfe9f7 0%, #a8d8f0 100%);
      position: relative;
      overflow: hidden;
      font-size: 1.3rem;
    }

    .login-bg::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
      background-size: 50px 50px;
      animation: moveBackground 20s linear infinite;
    }

    @keyframes moveBackground {
      0% {
        transform: translate(0, 0);
      }

      100% {
        transform: translate(50px, 50px);
      }
    }

    .login-illustration {
      background: none !important;
      background-size: cover;
      background-position: center;
      min-height: 100vh;
      position: relative;
    }

    .login-card {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      font-size: 1.3rem;
    }

    .floating-icon {
      position: absolute;
      font-size: 3rem;
      opacity: 0.15;
      animation: float 6s ease-in-out infinite;
    }

    .floating-icon:nth-child(1) {
      top: 10%;
      left: 10%;
      animation-delay: 0s;
    }

    .floating-icon:nth-child(2) {
      top: 60%;
      left: 20%;
      animation-delay: 2s;
    }

    .floating-icon:nth-child(3) {
      top: 30%;
      left: 80%;
      animation-delay: 4s;
    }

    .floating-icon:nth-child(4) {
      top: 80%;
      left: 70%;
      animation-delay: 1s;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    .input-group-outline input:focus {
      border-color: #667eea;
    }

    .btn-login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      color: #ffffff !important;
      font-weight: 600;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    @media (max-width: 991px) {
      .login-illustration {
        display: none !important;
      }

      .login-card {
        margin-top: 2rem;
      }
    }

    @media (max-width: 576px) {
      .card-header h4 {
        font-size: 1.5rem;
      }

      .navbar-brand {
        font-size: 0.9rem;
      }
    }
        /* Mô tả dưới tiêu đề */
    .card-header p {
        color: #444 !important;
        font-weight: 500;
    }

    /* Label input */
    .form-label {
        color: #333 !important;
        font-weight: 600;
    }

    /* Chữ ghi nhớ đăng nhập */
    .form-check-label {
        color: #333 !important;
        font-weight: 500;
    }
  </style>
</head>

<body class="login-bg">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>

  <main class="main-content mt-0">
    <section>
      <div class="page-header min-vh-100">
        <div class="container-fluid">
          <div class="row min-vh-100 d-flex align-items-center justify-content-center">
            <!-- Right Side - Login Form -->
            <div
              class="col-md-6 col-lg-4 col-11">
              <div class="card login-card mt-8">
                <div class="card-header pb-0 text-start">
                  <h3 class="font-weight-bolder text-gradient text-primary text-center">Hệ thống quản lý cuộc thi</h3>
                  <p class="mb-0">Nhập thông tin tài khoản của bạn để tiếp tục</p>
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

                  <form role="form" method="POST" action="">
                    <div class="mb-3">
                      <label class="form-label">Tài khoản hoặc Email</label>
                      <div class="input-group input-group-outline">
                        <input type="text" name="tendangnhap" class="form-control"
                          placeholder="Nhập tài khoản hoặc email..."
                          value="<?= isset($_POST['tendangnhap']) ? htmlspecialchars($_POST['tendangnhap']) : '' ?>"
                          required>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Mật khẩu</label>
                      <div class="input-group input-group-outline">
                        <input type="password" name="matkhau" id="password" class="form-control"
                          placeholder="Nhập mật khẩu..." required>
                      </div>
                    </div>

                    <div class="form-check form-switch d-flex align-items-center mb-3">
                      <input class="form-check-input" type="checkbox" id="rememberMe">
                      <label class="form-check-label mb-0 ms-3" for="rememberMe">Ghi nhớ đăng
                        nhập</label>
                    </div>

                    <div class="text-center">
                      <button type="submit" name="btn_dang_nhap"
                        class="btn btn-login btn-lg w-100 mt-2 mb-0">
                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                      </button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                  <p class="mb-4 text-sm mx-auto">
                    <a href="javascript:;" class="text-primary text-gradient font-weight-bold">Quên
                      mật khẩu?</a>
                  </p>
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