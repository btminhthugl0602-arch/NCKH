<?php
    function tao_tai_khoan_sinh_vien(
        $conn,
        $id_tai_khoan,
        $ho_ten,
        $ma_so_sinh_vien,
        $id_lop
    ) {
        $ho_ten = chuan_hoa_chuoi($conn, $ho_ten);
        $ma_so_sinh_vien = chuan_hoa_chuoi($conn, $ma_so_sinh_vien);
        $id_lop = (int)$id_lop;

        if (kiem_tra_ton_tai_ban_ghi($conn, 'SINHVIEN', 'MSV', $ma_so_sinh_vien)) {
            throw new Exception('Mã sinh viên đã tồn tại');
        }

        $lop = lay_ban_ghi_theo_khoa($conn, 'LOP', 'idLop', $id_lop);
        if (!$lop) {
            throw new Exception('Lớp không tồn tại');
        }

        $id_khoa = $lop['idKhoa'];

        $sql = "
            INSERT INTO SINHVIEN (idTK, tenSV, MSV, idLop, idKhoa)
            VALUES ('$id_tai_khoan', '$ho_ten', '$ma_so_sinh_vien', '$id_lop', '$id_khoa')
        ";

        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Không thể tạo hồ sơ sinh viên');
        }
    }
    function tao_tai_khoan_giang_vien(
        $conn,
        $id_tai_khoan,
        $ho_ten,
        $id_khoa
    ) {
        $ho_ten = chuan_hoa_chuoi($conn, $ho_ten);
        $id_khoa = (int)$id_khoa;

        $sql = "
            INSERT INTO GIANGVIEN (idTK, tenGV, idKhoa)
            VALUES ('$id_tai_khoan', '$ho_ten', '$id_khoa')
        ";

        if (!mysqli_query($conn, $sql)) {
            throw new Exception('Không thể tạo hồ sơ giảng viên');
        }
    }

    function admin_tao_tai_khoan(
        $conn,
        $id_nguoi_thuc_hien,
        $ten_dang_nhap,
        $mat_khau,
        $id_loai_tai_khoan,
        $ho_ten,
        $id_don_vi,
        $ma_so_sinh_vien = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'user.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền thao tác'];
        }

        $ten_dang_nhap = chuan_hoa_chuoi($conn, $ten_dang_nhap);

        if (kiem_tra_ton_tai_ban_ghi($conn, 'TAIKHOAN', 'tenTK', $ten_dang_nhap)) {
            return ['status' => false, 'message' => 'Tên đăng nhập đã tồn tại'];
        }

        mysqli_begin_transaction($conn);

        try {
            $mat_khau_ma_hoa = password_hash($mat_khau, PASSWORD_DEFAULT);

            mysqli_query($conn, "
                INSERT INTO TAIKHOAN (tenTK, matKhau, idLoaiTK, isActive)
                VALUES ('$ten_dang_nhap', '$mat_khau_ma_hoa', '$id_loai_tai_khoan', 1)
            ");

            $id_tai_khoan = mysqli_insert_id($conn);

            if ($id_loai_tai_khoan == 3) {
                tao_tai_khoan_sinh_vien(
                    $conn,
                    $id_tai_khoan,
                    $ho_ten,
                    $ma_so_sinh_vien,
                    $id_don_vi
                );
            }

            if ($id_loai_tai_khoan == 2) {
                tao_tai_khoan_giang_vien(
                    $conn,
                    $id_tai_khoan,
                    $ho_ten,
                    $id_don_vi
                );
            }

            mysqli_commit($conn);
            return ['status' => true, 'message' => 'Tạo tài khoản thành công'];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    function admin_khoa_tai_khoan($conn, $id_nguoi_thuc_hien, $id_tai_khoan) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'user.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền thao tác'];
        }

        if ($id_nguoi_thuc_hien == $id_tai_khoan) {
            return ['status' => false, 'message' => 'Không thể tự khóa tài khoản'];
        }

        $sql = "UPDATE TAIKHOAN SET isActive = 0 WHERE idTK = '$id_tai_khoan'";
        return mysqli_query($conn, $sql)
            ? ['status' => true, 'message' => 'Đã khóa tài khoản']
            : ['status' => false, 'message' => 'Lỗi hệ thống'];
    }

    function admin_gan_quyen_cho_tai_khoan($conn, $id_nguoi_thuc_hien, $id_tai_khoan, $ma_quyen) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'system.assign_role')) {
            return ['status' => false, 'message' => 'Không đủ quyền thao tác'];
        }

        $id_quyen = lay_id_quyen_theo_ma($conn, $ma_quyen);
        if (!$id_quyen) {
            return ['status' => false, 'message' => 'Mã quyền không hợp lệ'];
        }

        $sql = "
            INSERT INTO TAIKHOAN_QUYEN (idTK, idQuyen, isActive, thoiGianBatDau)
            VALUES ('$id_tai_khoan', '$id_quyen', 1, NOW())
            ON DUPLICATE KEY UPDATE
                isActive = 1,
                thoiGianBatDau = NOW()
        ";

        mysqli_query($conn, $sql);
        return ['status' => true, 'message' => 'Gán quyền thành công'];
    }

    function admin_cap_nhat_quyen_tai_khoan($conn, $id_nguoi_thuc_hien, $id_tai_khoan, $danh_sach_ma_quyen) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'system.assign_role')) {
            return ['status' => false, 'message' => 'Không đủ quyền thao tác'];
        }

        mysqli_begin_transaction($conn);

        try {
            mysqli_query($conn, "
                UPDATE TAIKHOAN_QUYEN
                SET isActive = 0
                WHERE idTK = '$id_tai_khoan'
            ");

            foreach ($danh_sach_ma_quyen as $ma_quyen) {
                $id_quyen = lay_id_quyen_theo_ma($conn, $ma_quyen);
                if ($id_quyen) {
                    mysqli_query($conn, "
                        INSERT INTO TAIKHOAN_QUYEN (idTK, idQuyen, isActive, thoiGianBatDau)
                        VALUES ('$id_tai_khoan', '$id_quyen', 1, NOW())
                        ON DUPLICATE KEY UPDATE
                            isActive = 1,
                            thoiGianBatDau = NOW()
                    ");
                }
            }

            mysqli_commit($conn);
            return ['status' => true, 'message' => 'Cập nhật quyền thành công'];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
?>