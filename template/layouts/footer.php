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
</script>

<!-- Control Center for Material Dashboard -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/material-dashboard.min.js?v=3.2.0"></script>

<!-- Custom JavaScript -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/custom.js"></script>

</body>

</html>

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
</script>

<!-- Control Center for Material Dashboard -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/material-dashboard.min.js?v=3.2.0"></script>

<!-- Custom JavaScript -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/custom.js"></script>

</body>

</html>