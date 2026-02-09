<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
session_start();
ob_start();
require_once('config.php');
require_once('./include/connect.php');
require_once('./include/database.php');
require_once('./include/session.php');
require_once('./include/function.php');

$module = _MODULES;
$action = _ACTION;

if (!empty($_GET['module'])) {
    $module = $_GET['module'];
}
if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

// Các module không cần login
$publicModules = ['auth', 'errors'];

// Kiểm tra login (trừ các module public)
if (!in_array($module, $publicModules)) {
    requireLogin();
}

$path = 'modules/' . $module . '/' . $action . '.php';

if (!empty($path)) {
    if (file_exists($path)) {
        require_once $path;
    } else {
        require_once './modules/errors/404.php';
    }
} else {
    require_once './modules/errors/505.php';
}
