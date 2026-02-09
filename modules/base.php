<?php

    function chuan_hoa_chuoi_sql($conn, $str) {
        return mysqli_real_escape_string($conn, trim($str));
    }

    function kiem_tra_ton_tai_ban_ghi($conn, $bang, $cot, $gia_tri) {
        $gia_tri = chuan_hoa_chuoi_sql($conn, $gia_tri);
        $sql = "SELECT COUNT(*) AS soLuong FROM `$bang` WHERE `$cot` = '$gia_tri' LIMIT 1";
        $kq = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($kq);
        return $row['soLuong'] > 0;
    }

    function truy_van_mot_ban_ghi($conn, $bang, $cot_khoa, $gia_tri_khoa) {
        $gia_tri_khoa = chuan_hoa_chuoi_sql($conn, $gia_tri_khoa);
        $sql = "SELECT * FROM `$bang` WHERE `$cot_khoa` = '$gia_tri_khoa' LIMIT 1";
        $kq = mysqli_query($conn, $sql);
        return mysqli_num_rows($kq) > 0 ? mysqli_fetch_assoc($kq) : null;
    }


    function kiem_tra_quyen_he_thong($conn, $id_tai_khoan, $ma_quyen) {
        $user = truy_van_mot_ban_ghi($conn, 'TAIKHOAN', 'idTK', $id_tai_khoan);
        if (!$user) return false;

        if ($user['idLoaiTK'] == 1) return true;

        $now = date('Y-m-d H:i:s');
        $sql = "SELECT 1
                FROM TAIKHOAN_QUYEN tq
                JOIN QUYEN q ON tq.idQuyen = q.idQuyen
                WHERE tq.idTK = '$id_tai_khoan'
                AND q.maQuyen = '$ma_quyen'
                AND tq.isActive = 1
                AND tq.thoiGianBatDau <= '$now'
                AND (tq.thoiGianKetThuc IS NULL OR tq.thoiGianKetThuc >= '$now')
                LIMIT 1";

        return mysqli_num_rows(mysqli_query($conn, $sql)) > 0;
    }

    function anh_xa_ma_quyen($conn, $ma_quyen) {
        $ma_quyen = chuan_hoa_chuoi_sql($conn, $ma_quyen);
        $sql = "SELECT idQuyen FROM QUYEN WHERE maQuyen = '$ma_quyen' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        return mysqli_num_rows($res) > 0 ? mysqli_fetch_assoc($res)['idQuyen'] : null;
    }
?>
