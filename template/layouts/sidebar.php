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

            <?php if ($user_role != 'guest'): // Tất cả user đăng nhập đều thấy ?>
            <li class="nav-item">
                <a class="nav-link <?= (isset($active_page) && $active_page == 'users') ? 'active bg-gradient-dark text-white' : 'text-dark' ?>"
                    href="<?= _HOST_URL ?>?module=users">
                    <i class="material-symbols-rounded opacity-5">people</i>
                    <span class="nav-link-text ms-1">Người dùng</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if ($user_role == 1): // Chỉ Admin mới thấy Nhóm ?>
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

<style>
/* CSS cho việc ẩn/hiện sidebar hoàn toàn */
.sidenav.sidebar-hidden {
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
}

.sidenav {
    transition: transform 0.3s ease-in-out;
}

/* Điều chỉnh main content khi sidebar ẩn */
body.sidebar-hidden .main-content {
    margin-left: 0 !important;
}

body:not(.sidebar-hidden) .main-content {
    margin-left: 250px;
}

/* Responsive */
@media (max-width: 1199.98px) {
    body:not(.sidebar-hidden) .main-content {
        margin-left: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidenav-main');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const body = document.body;
    
    // Kiểm tra trạng thái sidebar từ localStorage
    const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
    if (sidebarHidden) {
        sidebar.classList.add('sidebar-hidden');
        body.classList.add('sidebar-hidden');
    }
    
    // Toggle sidebar khi click nút
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-hidden');
            body.classList.toggle('sidebar-hidden');
            
            // Lưu trạng thái vào localStorage
            const isHidden = sidebar.classList.contains('sidebar-hidden');
            localStorage.setItem('sidebarHidden', isHidden);
        });
    }
});
</script>
