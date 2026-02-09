<?php

    function btc_tao_su_kien(
        $conn,
        $id_nguoi_tao,
        $ten_su_kien,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_tao, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền tạo sự kiện'];
        }

        $ten_su_kien = chuan_hoa_chuoi($conn, $ten_su_kien);
        $mo_ta = chuan_hoa_chuoi($conn, $mo_ta);

        if (empty($ten_su_kien)) {
            return ['status' => false, 'message' => 'Tên sự kiện không được để trống'];
        }

        $sql = "
            INSERT INTO SUKIEN (tenSK, moTa, trangThai, nguoiTao)
            VALUES ('$ten_su_kien', '$mo_ta', 'NHAP', '$id_nguoi_tao')
        ";

        if (!mysqli_query($conn, $sql)) {
            return ['status' => false, 'message' => 'Không thể tạo sự kiện'];
        }

        return [
            'status' => true,
            'message' => 'Đã khởi tạo sự kiện',
            'idSK' => mysqli_insert_id($conn)
        ];
    }

    function btc_cap_nhat_su_kien(
        $conn,
        $id_nguoi_thuc_hien,
        $id_su_kien,
        $ten_su_kien,
        $mo_ta,
        $thoi_gian_bat_dau = null,
        $thoi_gian_ket_thuc = null
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        $su_kien = truy_van_mot_ban_ghi($conn, 'SUKIEN', 'idSK', $id_su_kien);
        if (!$su_kien) {
            return ['status' => false, 'message' => 'Sự kiện không tồn tại'];
        }

        if ($su_kien['trangThai'] !== 'NHAP') {
            return ['status' => false, 'message' => 'Chỉ được chỉnh sửa khi sự kiện ở trạng thái NHÁP'];
        }

        $ten_su_kien = chuan_hoa_chuoi($conn, $ten_su_kien);
        $mo_ta = chuan_hoa_chuoi($conn, $mo_ta);

        $sql = "
            UPDATE SUKIEN
            SET tenSK = '$ten_su_kien',
                moTa = '$mo_ta',
                thoiGianBatDau = " . ($thoi_gian_bat_dau ? "'$thoi_gian_bat_dau'" : "NULL") . ",
                thoiGianKetThuc = " . ($thoi_gian_ket_thuc ? "'$thoi_gian_ket_thuc'" : "NULL") . "
            WHERE idSK = '$id_su_kien'
        ";

        mysqli_query($conn, $sql);
        return ['status' => true, 'message' => 'Cập nhật sự kiện thành công'];
    }
?>
