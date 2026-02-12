<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}
?>
<footer class="footer py-4">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-muted text-lg-start">
                    © <script>
                    document.write(new Date().getFullYear())
                    </script>,
                    Hệ thống quản lý sự kiện
                </div>
            </div>
            <div class="col-lg-6">
                <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link text-muted">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link text-muted">Liên hệ</a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link pe-0 text-muted">Trợ giúp</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<!--   Core JS Files   -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/popper.min.js"></script>
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/core/bootstrap.min.js"></script>
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>

<!-- Custom Scripts -->
<script>
var win = navigator.platform.indexOf('Win') > -1;
if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
        damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
}

// Sidebar Toggle Script
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const iconSidenav = document.getElementById('iconSidenav');
    const body = document.body;
    const sidenav = document.getElementById('sidenav-main');
    
    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function() {
            if (body.classList.contains('g-sidenav-pinned')) {
                body.classList.remove('g-sidenav-pinned');
                body.classList.add('g-sidenav-hidden');
            } else {
                body.classList.add('g-sidenav-pinned');
                body.classList.remove('g-sidenav-hidden');
            }
        });
    }
    
    // Mobile close button
    if (iconSidenav) {
        iconSidenav.addEventListener('click', function() {
            body.classList.remove('g-sidenav-pinned');
            body.classList.add('g-sidenav-hidden');
        });
    }
    
    // Auto show sidebar on desktop
    if (window.innerWidth >= 1200) {
        body.classList.add('g-sidenav-show', 'g-sidenav-pinned');
    }
});
</script>

<!-- Control Center for Material Dashboard -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>

</html>
