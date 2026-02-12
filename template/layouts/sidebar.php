<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Lấy role của user
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
?>
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="<?= _HOST_URL ?>?module=dashboard">
            <img src="<?= _HOST_URL_TEMPLATES ?>/assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26"
                height="26" alt="main_logo">
            <span class="ms-1 text-sm text-dark">Quản lý Sự kiện</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=dashboard">
                    <i class="material-symbols-rounded opacity-5">dashboard</i>
                    <span class="nav-link-text ms-1">Trang chủ</span>
                </a>
            </li>

            <?php if ($user_role != 'guest'): ?>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Quản lý</h6>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'event') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=event">
                    <i class="material-symbols-rounded opacity-5">event</i>
                    <span class="nav-link-text ms-1"><?= ($user_role == 1) ? 'Quản lí sự kiện' : 'Sự kiện' ?></span>
                </a>
            </li>

            <?php if ($user_role == 1): // Chỉ Admin mới thấy ?>
            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'users') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=users">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">Người dùng</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'groups') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=groups">
                    <i class="material-symbols-rounded opacity-5">group</i>
                    <span class="nav-link-text ms-1">Nhóm</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($user_role != 'guest'): ?>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Cài đặt</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'profile') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=profile">
                    <i class="material-symbols-rounded opacity-5">person</i>
                    <span class="nav-link-text ms-1">Hồ sơ</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-dark" href="<?= _HOST_URL ?>?module=auth&action=logout">
                    <i class="material-symbols-rounded opacity-5">logout</i>
                    <span class="nav-link-text ms-1">Đăng xuất</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="sidenav-footer position-absolute w-100 bottom-0">
        <div class="mx-3">
            <a class="btn btn-outline-dark mt-4 w-100" href="javascript:;" type="button">
                <i class="material-symbols-rounded me-2">help</i>Trợ giúp
            </a>
        </div>
    </div>
</aside>
