<?php 
    function tao_tieu_chi(
        $conn,
        $id_nguoi_tao,
        $ten_tieu_chi,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_tao, 'criteria.manage')) {
            return ['status' => false, 'message' => 'Không có quyền tạo tiêu chí'];
        }

        $ten_tieu_chi = chuan_hoa_chuoi($conn, $ten_tieu_chi);
        $mo_ta = chuan_hoa_chuoi($conn, $mo_ta);

        if (empty($ten_tieu_chi)) {
            return ['status' => false, 'message' => 'Tên tiêu chí không được trống'];
        }

        mysqli_query($conn, "
            INSERT INTO TIEUCHI (tenTieuChi, moTa, nguoiTao)
            VALUES ('$ten_tieu_chi', '$mo_ta', '$id_nguoi_tao')
        ");

        return ['status' => true, 'message' => 'Đã tạo tiêu chí'];
    }

    function tao_bo_tieu_chi(
        $conn,
        $id_nguoi_tao,
        $id_su_kien,
        $ten_bo,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_tao, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        $su_kien = truy_van_mot_ban_ghi($conn, 'SUKIEN', 'idSK', $id_su_kien);
        if (!$su_kien) {
            return ['status' => false, 'message' => 'Sự kiện không tồn tại'];
        }

        if ($su_kien['trangThai'] !== 'CAU_HINH') {
            return ['status' => false, 'message' => 'Chỉ cấu hình tiêu chí khi sự kiện ở giai đoạn CẤU HÌNH'];
        }

        $ten_bo = chuan_hoa_chuoi($conn, $ten_bo);
        $mo_ta = chuan_hoa_chuoi($conn, $mo_ta);

        mysqli_query($conn, "
            INSERT INTO BOTIEUCHI (idSK, tenBo, moTa)
            VALUES ('$id_su_kien', '$ten_bo', '$mo_ta')
        ");

        return [
            'status' => true,
            'message' => 'Đã tạo bộ tiêu chí',
            'idBo' => mysqli_insert_id($conn)
        ];
    }
    function them_tieu_chi_vao_bo(
        $conn,
        $id_nguoi_thuc_hien,
        $id_bo,
        $id_tieu_chi,
        $diem_toi_da,
        $trong_so,
        $bat_buoc = 1
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        $bo = truy_van_mot_ban_ghi($conn, 'BOTIEUCHI', 'idBo', $id_bo);
        if (!$bo) {
            return ['status' => false, 'message' => 'Bộ tiêu chí không tồn tại'];
        }

        $tc = truy_van_mot_ban_ghi($conn, 'TIEUCHI', 'idTieuChi', $id_tieu_chi);
        if (!$tc) {
            return ['status' => false, 'message' => 'Tiêu chí không tồn tại'];
        }

        mysqli_query($conn, "
            INSERT INTO BOTIEUCHI_TIEUCHI
            (idBo, idTieuChi, diemToiDa, trongSo, batBuoc)
            VALUES
            ('$id_bo', '$id_tieu_chi', '$diem_toi_da', '$trong_so', '$bat_buoc')
        ");

        return ['status' => true, 'message' => 'Đã thêm tiêu chí vào bộ'];
    }
    function khoa_bo_tieu_chi(
        $conn,
        $id_nguoi_thuc_hien,
        $id_bo
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        mysqli_query($conn, "
            UPDATE BOTIEUCHI
            SET isKhoa = 1
            WHERE idBo = '$id_bo'
        ");

        return ['status' => true, 'message' => 'Đã khóa bộ tiêu chí'];
    }

    function btc_gan_btc_cho_su_kien(
        $conn,
        $id_nguoi_thuc_hien,
        $id_su_kien,
        $id_btc
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền'];
        }

        mysqli_query($conn, "
            INSERT INTO SUKIEN_BTC (idSK, idBTC)
            VALUES ('$id_su_kien', '$id_btc')
            ON DUPLICATE KEY UPDATE idBTC = '$id_btc'
        ");

        return ['status' => true, 'message' => 'Đã gán bộ tiêu chí cho sự kiện'];
    }

?>