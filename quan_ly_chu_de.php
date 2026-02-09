<?php 
    function btc_tao_chu_de(
        $conn,
        $id_nguoi_tao,
        $id_su_kien,
        $ten_chu_de,
        $mo_ta = ''
    ) {
        if (!xac_thuc_quyen_truy_cap($conn, $id_nguoi_tao, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền cấu hình sự kiện'];
        }

        $su_kien = truy_van_mot_ban_ghi($conn, 'SUKIEN', 'idSK', $id_su_kien);
        if (!$su_kien) {
            return ['status' => false, 'message' => 'Sự kiện không tồn tại'];
        }

        if (!in_array($su_kien['trangThai'], ['NHAP', 'CAU_HINH'])) {
            return ['status' => false, 'message' => 'Không thể thêm chủ đề ở trạng thái hiện tại'];
        }

        $ten_chu_de = chuan_hoa_chuoi_sql($conn, $ten_chu_de);
        $mo_ta = chuan_hoa_chuoi_sql($conn, $mo_ta);

        if (empty($ten_chu_de)) {
            return ['status' => false, 'message' => 'Tên chủ đề không được để trống'];
        }

        $sql = "
            INSERT INTO CHUDE (idSK, tenChuDe, moTa, nguoiTao)
            VALUES ('$id_su_kien', '$ten_chu_de', '$mo_ta', '$id_nguoi_tao')
        ";

        if (!mysqli_query($conn, $sql)) {
            return ['status' => false, 'message' => 'Không thể tạo chủ đề'];
        }

        return [
            'status' => true,
            'message' => 'Đã tạo chủ đề',
            'idChuDe' => mysqli_insert_id($conn)
        ];
    }

    function btc_cap_nhat_chu_de(
        $conn,
        $id_nguoi_thuc_hien,
        $id_chu_de,
        $ten_chu_de,
        $mo_ta = ''
    ) {
        if (!xac_thuc_quyen_truy_cap($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        $chu_de = truy_van_mot_ban_ghi($conn, 'CHUDE', 'idChuDe', $id_chu_de);
        if (!$chu_de) {
            return ['status' => false, 'message' => 'Chủ đề không tồn tại'];
        }

        $ten_chu_de = chuan_hoa_chuoi_sql($conn, $ten_chu_de);
        $mo_ta = chuan_hoa_chuoi_sql($conn, $mo_ta);

        mysqli_query($conn, "
            UPDATE CHUDE
            SET tenChuDe = '$ten_chu_de',
                moTa = '$mo_ta'
            WHERE idChuDe = '$id_chu_de'
        ");

        return ['status' => true, 'message' => 'Cập nhật chủ đề thành công'];
    }

    function btc_kich_hoat_chu_de(
        $conn,
        $id_nguoi_thuc_hien,
        $id_chu_de,
        $trang_thai
    ) {
        if (!xac_thuc_quyen_truy_cap($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không có quyền'];
        }

        $trang_thai = $trang_thai ? 1 : 0;

        mysqli_query($conn, "
            UPDATE CHUDE
            SET isActive = '$trang_thai'
            WHERE idChuDe = '$id_chu_de'
        ");

        return ['status' => true, 'message' => 'Đã cập nhật trạng thái chủ đề'];
    }
    function btc_danh_sach_chu_de_su_kien(
        $conn,
        $id_su_kien,
        $chi_lay_dang_hoat_dong = true
    ) {
        $dk = $chi_lay_dang_hoat_dong ? "AND isActive = 1" : "";

        $sql = "
            SELECT *
            FROM CHUDE
            WHERE idSK = '$id_su_kien'
            $dk
            ORDER BY thoiGianTao ASC
        ";

        $res = mysqli_query($conn, $sql);
        $ds = [];

        while ($row = mysqli_fetch_assoc($res)) {
            $ds[] = $row;
        }

        return $ds;
    }
?>
