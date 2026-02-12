<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Khách';
?>
<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur"
    data-scroll="true">
    <div class="container-fluid py-1 px-3">
        <!-- Toggle Sidebar Button -->
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sidebar-toggle me-3 p-0 border-0 bg-transparent" id="sidebarToggleBtn"
                title="Ẩn/Hiện thanh bên" aria-label="Toggle sidebar">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                    <i class="sidenav-toggler-line"></i>
                </div>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark" href="<?= _HOST_URL ?>">Trang chủ</a>
                    </li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                        <?= isset($breadcrumb) ? $breadcrumb : 'Dashboard' ?>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">

            </div>
            <ul class="navbar-nav d-flex align-items-center justify-content-end">
                <?php if ($user_role != 'guest'): ?>
                <li class="nav-item px-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0">
                        <i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
                    </a>
                </li>
                <li class="nav-item dropdown pe-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-symbols-rounded">notifications</i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="my-auto">
                                        <img src="<?= _HOST_URL_TEMPLATES ?>/assets/img/team-2.jpg"
                                            class="avatar avatar-sm me-3">
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">Tin nhắn mới</span> từ Admin
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="fa fa-clock me-1"></i>
                                            13 phút trước
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="mb-2">
                            <a class="dropdown-item border-radius-md" href="javascript:;">
                                <div class="d-flex py-1">
                                    <div class="avatar avatar-sm bg-gradient-secondary me-3 my-auto">
                                        <i class="material-symbols-rounded text-white">event</i>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">
                                            <span class="font-weight-bold">Sự kiện mới</span> được tạo
                                        </h6>
                                        <p class="text-xs text-secondary mb-0">
                                            <i class="fa fa-clock me-1"></i>
                                            1 giờ trước
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <li class="nav-item d-flex align-items-center">
                    <?php if ($user_role == 'guest'): ?>
                    <a href="<?= _HOST_URL ?>?module=auth&action=login"
                        class="nav-link text-body font-weight-bold px-0">
                        <i class="material-symbols-rounded">account_circle</i>
                        <sp clas s="d-sm-inline d-none ms-1">Đăng nhập</sp an>
                    </a>
                    <?php else: ?>
                    <a href="<?= _HOST_URL ?>?module=auth&action=logout"
                        class="nav-link text-body font-weight-bold px-0">
                        <i class="material-symbols-rounded">account_circle</i>
                        <span class="d-sm-inline d-none ms-1">
                            <?= htmlspecialchars($user_name) ?>
                        </span>
                    </a>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->