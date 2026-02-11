<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="<?= _HOST_URL_TEMPLATES ?>/assets/img/favicon.png">
    <title>
        <?php echo isset($data['page_title']) ? $data['page_title'] : (isset($page_title) ? $page_title : 'Hệ thống quản lý sự kiện'); ?>
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
    <!-- Custom CSS -->
    <link href="<?= _HOST_URL_TEMPLATES ?>/assets/css/custom.css" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">