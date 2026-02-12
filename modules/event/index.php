<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Kiểm tra đăng nhập
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';

$data = [
    'page_title' => ($user_role == 1) ? 'Quản lý sự kiện' : 'Sự kiện'
];

$active_page = 'event';

layout('header', $data);
layout('sidebar');
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

    <?php layout('navbar'); ?>

    <div class="container-fluid py-4">
        <h3><?= ($user_role == 1) ? 'Danh sách sự kiện' : 'Sự kiện' ?></h3>
        </br>

        <!-- Charts Row -->


        <div class="card mt-3">
            <div class="row">
                <!-- Card 1 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Cuộc thi Khoa học Công nghệ 2024</a>
                            </h5>
                            <p class="mb-0">
                                Cuộc thi dành cho sinh viên khoa CNTT, đăng ký trước ngày 15/03/2024
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">15/03/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> Hà Nội</p>
                        </div>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Hội thảo AI & Machine Learning</a>
                            </h5>
                            <p class="mb-0">
                                Hội thảo chuyên đề về trí tuệ nhân tạo và học máy, dành cho giảng viên và sinh viên
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">20/03/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> TP.HCM</p>
                        </div>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Hackathon 2024</a>
                            </h5>
                            <p class="mb-0">
                                Cuộc thi lập trình 48 giờ, giải thưởng hấp dẫn cho đội chiến thắng
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">25/03/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> Đà Nẵng</p>
                        </div>
                    </div>
                </div>

                <!-- Card 4 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Triển lãm Công nghệ</a>
                            </h5>
                            <p class="mb-0">
                                Triển lãm các sản phẩm công nghệ mới nhất từ các doanh nghiệp và startup
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">30/03/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> Cần Thơ</p>
                        </div>
                    </div>
                </div>

                <!-- Card 5 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Workshop IoT</a>
                            </h5>
                            <p class="mb-0">
                                Workshop thực hành về Internet of Things và ứng dụng thực tế
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">05/04/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> Huế</p>
                        </div>
                    </div>
                </div>

                <!-- Card 6 -->
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="card" data-animation="true">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <a class="d-block blur-shadow-image">
                                <img src="https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg"
                                    alt="img-blur-shadow" class="img-fluid shadow border-radius-lg">
                            </a>
                            <div class="colored-shadow"
                                style="background-image: url(&quot;https://demos.creative-tim.com/test/material-dashboard-pro/assets/img/products/product-1-min.jpg&quot;);">
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <div class="d-flex mt-n6 mx-auto">
                                <a class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Refresh">
                                    <i class="material-symbols-rounded text-lg">refresh</i>
                                </a>
                                <button class="btn btn-link text-info me-auto border-0" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="<?= ($user_role == 1) ? 'Edit' : 'Xem' ?>">
                                    <i class="material-symbols-rounded text-lg"><?= ($user_role == 1) ? 'edit' : 'visibility' ?></i>
                                </button>
                            </div>
                            <h5 class="font-weight-normal mt-3">
                                <a href="javascript:;">Hội nghị Blockchain</a>
                            </h5>
                            <p class="mb-0">
                                Hội nghị về công nghệ Blockchain và ứng dụng trong tài chính
                            </p>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer d-flex">
                            <p class="font-weight-normal my-auto">10/04/2024</p>
                            <i class="material-symbols-rounded position-relative ms-auto text-lg me-1 my-auto">place</i>
                            <p class="text-sm my-auto"> Nha Trang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>

</main>

<?php
// Include footer
layout('footer');
?>
