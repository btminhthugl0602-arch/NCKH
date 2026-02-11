<?php
session_start();
$tb_dang_nhap = "";

if (isset($_POST['btn_dang_nhap'])) {

    $tai_khoan = isset($_POST['tendangnhap']) ? chuan_hoa_chuoi_sql($conn, $_POST['tendangnhap']) : "";
    $mat_khau = isset($_POST['matkhau']) ? $_POST['matkhau'] : "";

    if ($tai_khoan == "" || $mat_khau == "") {
        $tb_dang_nhap = "Vui lòng nhập đầy đủ thông tin!!";
    } else {
        
        $row = truy_van_mot_ban_ghi($conn, 'users', 'user_name', $tai_khoan);

        if (!$row) {
            $row = truy_van_mot_ban_ghi($conn, 'users', 'user_email', $tai_khoan);
        }

        if ($row) {
            if ($mat_khau == $row['user_password']) {
                
                if ($row['user_status'] != 'active') {
                    $tb_dang_nhap = "Tài khoản của bạn đã bị vô hiệu hóa.";
                } else {
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['user'] = $row['user_name'];
                    $_SESSION['email'] = $row['user_email'];
                    $_SESSION['role'] = $row['user_tac_nhan'];

                    if ($_SESSION['role'] == 1) {
                        header("Location: trang_chu_admin.php");
                    } else {
                        header("Location: trang_chu_user.php");
                    }
                    exit();
                }
            } else {
                $tb_dang_nhap = "Mật khẩu không chính xác";
            }
        } else {
            $tb_dang_nhap = "Tên đăng nhập/Email hoặc mật khẩu không chính xác";
        }
    }
}
?>