<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Không có quyền truy cập</title>
    <link href="<?php echo _HOST_URL; ?>/template/assets/css/material-dashboard.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-200">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-6 col-md-8">
                <div class="card">
                    <div class="card-body text-center p-5">
                        <h1 class="display-1 text-primary">403</h1>
                        <h3 class="mb-3">Không có quyền truy cập</h3>
                        <p class="text-muted mb-4">
                            Bạn không có quyền truy cập vào trang này.<br>
                            Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là lỗi.
                        </p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="material-icons">arrow_back</i> Quay lại
                            </a>
                            <a href="<?php echo url('dashboard', 'index'); ?>" class="btn btn-primary">
                                <i class="material-icons">home</i> Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
