<?php
require_once __DIR__ . '/base.php';

/**
 * Hàm kiểm tra sinh viên đã có nhóm trong sự kiện này chưa
 * truyền vào $id_tk (tài khoản sinh viên) và $id_sk (sự kiện) để kiểm tra
 */
function kiem_tra_sv_co_nhom($conn, $id_tk, $id_sk) {
    
    $sql = "SELECT tv.idtk 
            FROM thanhviennhom tv 
            JOIN nhom n ON tv.idnhom = n.idnhom 
            WHERE tv.idtk = ? AND n.idSK = ? AND tv.trangthai = 1"; // trangthai=1 là đã tham gia
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_tk, $id_sk);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    return mysqli_stmt_num_rows($stmt) > 0;
}

/**
 * 1. TẠO NHÓM THI MỚI
 * truyền vào $id_nhom_truong (tài khoản của người tạo nhóm), $id_sk (sự kiện), $ten_nhom, $mo_ta, $so_luong_max
 */
function tao_nhom_moi($conn, $id_nhom_truong, $id_sk, $ten_nhom, $mo_ta, $so_luong_max = 5) {
    if (kiem_tra_sv_co_nhom($conn, $id_nhom_truong, $id_sk)) {
        return ['status' => false, 'message' => 'Bạn đã tham gia một nhóm khác trong cuộc thi này'];
    }

    if (empty(trim($ten_nhom))) return ['status' => false, 'message' => 'Tên nhóm không được để trống'];

    mysqli_begin_transaction($conn);
    try {
        $ma_nhom = 'GRP_' . time() . rand(10, 99);

        $res_nhom = _insert_info($conn, 'nhom', 
            ['idSK', 'idnhomtruong', 'manhom', 'ngaytao', 'isActive'],
            [$id_sk, $id_nhom_truong, $ma_nhom, date('Y-m-d H:i:s'), 1]
        );
        
        if (!$res_nhom) throw new Exception('Lỗi tạo bảng nhóm gốc');
        $id_nhom = mysqli_insert_id($conn);
        $res_info = _insert_info($conn, 'thongtinnhom',
            ['idnhom', 'tennhom', 'mota', 'soluongtoida', 'dangtuyen'],
            [$id_nhom, $ten_nhom, $mo_ta, $so_luong_max, 1] // 1 = Đang tuyển
        );
        if (!$res_info) throw new Exception('Lỗi tạo thông tin nhóm');
        $res_mem = _insert_info($conn, 'thanhviennhom',
            ['idnhom', 'idtk', 'idvaitronhom', 'trangthai', 'ngaythamgia'],
            [$id_nhom, $id_nhom_truong, 1, 1, date('Y-m-d H:i:s')]
        );
        if (!$res_mem) throw new Exception('Lỗi thêm trưởng nhóm');

        mysqli_commit($conn);
        return ['status' => true, 'message' => 'Tạo nhóm thành công', 'idNhom' => $id_nhom];

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['status' => false, 'message' => $e->getMessage()];
    }
}

/**
 * 2. GỬI YÊU CẦU / MỜI THAM GIA
 * truyền vào $id_nhom, $id_tk (tài khoản người bị mời hoặc người xin vào), $chieu_moi (0 = Nhóm mời SV, 1 = SV xin vào nhóm), $loi_nhan (tùy chọn)
 * @param int $chieu_moi: 0 = Nhóm mời SV, 1 = SV xin vào nhóm
 */
function gui_yeu_cau_nhom($conn, $id_nhom, $id_tk, $chieu_moi, $loi_nhan = '') {
    $kt_thanhvien = _select_info($conn, 'thanhviennhom', [], [
        'WHERE' => ['idnhom', '=', $id_nhom, 'AND', 'idtk', '=', $id_tk, '']
    ]);
    if (!empty($kt_thanhvien)) return ['status' => false, 'message' => 'Thành viên này đã ở trong nhóm'];

    $kt_yeucau = _select_info($conn, 'yeucau_thamgia', [], [
        'WHERE' => [
            'idNhom', '=', $id_nhom, 'AND', 
            'idTK', '=', $id_tk, 'AND',
            'trangThai', '=', 0, '' // 0 = Chờ duyệt
        ]
    ]);
    if (!empty($kt_yeucau)) return ['status' => false, 'message' => 'Đang có yêu cầu chờ xử lý'];
    $res = _insert_info($conn, 'yeucau_thamgia',
        ['idNhom', 'idTK', 'ChieuMoi', 'loiNhan', 'trangThai', 'ngayGui'],
        [$id_nhom, $id_tk, $chieu_moi, $loi_nhan, 0, date('Y-m-d H:i:s')]
    );

    return $res 
        ? ['status' => true, 'message' => 'Gửi yêu cầu thành công']
        : ['status' => false, 'message' => 'Lỗi hệ thống'];
}

/**
 * 3. PHÊ DUYỆT YÊU CẦU (Cho cả 2 chiều)
 * @param int $id_nguoi_duyet: ID người đang thực hiện hành động này (để check quyền)
 * @param int $trang_thai_moi: 1 = Chấp nhận, 2 = Từ chối
 */
function duyet_yeu_cau_nhom($conn, $id_nguoi_duyet, $id_yeu_cau, $trang_thai_moi) {
    // Lấy thông tin yêu cầu
    $yc = truy_van_mot_ban_ghi($conn, 'yeucau_thamgia', 'idYeuCau', $id_yeu_cau);
    if (!$yc) return ['status' => false, 'message' => 'Yêu cầu không tồn tại'];

    if ($yc['trangThai'] != 0) return ['status' => false, 'message' => 'Yêu cầu này đã được xử lý'];

    if ($yc['ChieuMoi'] == 1) {
        $nhom = truy_van_mot_ban_ghi($conn, 'nhom', 'idnhom', $yc['idNhom']);
        if ($nhom['idnhomtruong'] != $id_nguoi_duyet) {
            return ['status' => false, 'message' => 'Chỉ trưởng nhóm mới được duyệt đơn xin gia nhập'];
        }
    }
    else {
        if ($yc['idTK'] != $id_nguoi_duyet) {
            return ['status' => false, 'message' => 'Bạn không phải người được mời'];
        }
    }

    mysqli_begin_transaction($conn);
    try {
        // Bước 1: Update trạng thái yêu cầu
        $cond = ['idYeuCau' => ['=', $id_yeu_cau, '']];
        _update_info($conn, 'yeucau_thamgia', 
            ['trangThai', 'ngayPhanHoi'], 
            [$trang_thai_moi, date('Y-m-d H:i:s')], 
            $cond
        );

        // Bước 2: Nếu CHẤP NHẬN (1) -> Thêm vào bảng thành viên
        if ($trang_thai_moi == 1) {
            // Check lại xem nhóm full chưa (Optional nhưng nên làm)
            
            // Insert thành viên mới (Vai trò 2 = Thành viên)
            $res_add = _insert_info($conn, 'thanhviennhom',
                ['idnhom', 'idtk', 'idvaitronhom', 'trangthai', 'ngaythamgia'],
                [$yc['idNhom'], $yc['idTK'], 2, 1, date('Y-m-d H:i:s')]
            );
            if (!$res_add) throw new Exception('Lỗi thêm thành viên vào nhóm');
        }

        mysqli_commit($conn);
        $msg = ($trang_thai_moi == 1) ? "Đã chấp nhận yêu cầu" : "Đã từ chối yêu cầu";
        return ['status' => true, 'message' => $msg];

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['status' => false, 'message' => $e->getMessage()];
    }
}

/**
 * 4. RỜI NHÓM / XÓA THÀNH VIÊN
 */
<?php
require_once __DIR__ . '/base.php';

/**
 * Hàm kiểm tra sinh viên đã có nhóm trong sự kiện này chưa
 * Logic mới: Kiểm tra dòng có isActive = 1
 */
function kiem_tra_sv_co_nhom($conn, $id_tk, $id_sk) {
    $sql = "SELECT tv.idtk 
            FROM thanhviennhom tv 
            JOIN nhom n ON tv.idnhom = n.idnhom 
            WHERE tv.idtk = ? AND n.idSK = ? AND tv.isActive = 1"; // Chỉ check thành viên đang hoạt động
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_tk, $id_sk);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    return mysqli_stmt_num_rows($stmt) > 0;
}

/**
 * 1. TẠO NHÓM THI MỚI
 */
function tao_nhom_moi($conn, $id_nhom_truong, $id_sk, $ten_nhom, $mo_ta, $so_luong_max = 5) {
    if (kiem_tra_sv_co_nhom($conn, $id_nhom_truong, $id_sk)) {
        return ['status' => false, 'message' => 'Bạn đã tham gia một nhóm khác trong cuộc thi này'];
    }

    if (empty(trim($ten_nhom))) return ['status' => false, 'message' => 'Tên nhóm không được để trống'];

    mysqli_begin_transaction($conn);
    try {
        $ma_nhom = 'GRP_' . time() . rand(10, 99);

        // Tạo nhóm
        $res_nhom = _insert_info($conn, 'nhom', 
            ['idSK', 'idnhomtruong', 'manhom', 'ngaytao', 'isActive'],
            [$id_sk, $id_nhom_truong, $ma_nhom, date('Y-m-d H:i:s'), 1]
        );
        
        if (!$res_nhom) throw new Exception('Lỗi tạo bảng nhóm gốc');
        $id_nhom = mysqli_insert_id($conn);

        // Tạo thông tin nhóm
        $res_info = _insert_info($conn, 'thongtinnhom',
            ['idnhom', 'tennhom', 'mota', 'soluongtoida', 'dangtuyen'],
            [$id_nhom, $ten_nhom, $mo_ta, $so_luong_max, 1]
        );
        if (!$res_info) throw new Exception('Lỗi tạo thông tin nhóm');

        // Thêm Trưởng nhóm (isActive = 1)
        // Lưu ý: Cột trangthai vẫn giữ là 1 (Đã tham gia) để đúng quy trình
        $res_mem = _insert_info($conn, 'thanhviennhom',
            ['idnhom', 'idtk', 'idvaitronhom', 'trangthai', 'ngaythamgia', 'isActive'],
            [$id_nhom, $id_nhom_truong, 1, 1, date('Y-m-d H:i:s'), 1]
        );
        if (!$res_mem) throw new Exception('Lỗi thêm trưởng nhóm');

        mysqli_commit($conn);
        return ['status' => true, 'message' => 'Tạo nhóm thành công', 'idNhom' => $id_nhom];

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['status' => false, 'message' => $e->getMessage()];
    }
}

/**
 * 2. GỬI YÊU CẦU / MỜI THAM GIA
 */
function gui_yeu_cau_nhom($conn, $id_nhom, $id_tk, $chieu_moi, $loi_nhan = '') {
    // Check xem đã trong nhóm chưa (và đang Active)
    $kt_thanhvien = _select_info($conn, 'thanhviennhom', [], [
        'WHERE' => ['idnhom', '=', $id_nhom, 'AND', 'idtk', '=', $id_tk, 'AND', 'isActive', '=', 1, '']
    ]);
    if (!empty($kt_thanhvien)) return ['status' => false, 'message' => 'Thành viên này đã ở trong nhóm'];

    // Check yêu cầu chờ duyệt
    $kt_yeucau = _select_info($conn, 'yeucau_thamgia', [], [
        'WHERE' => [
            'idNhom', '=', $id_nhom, 'AND', 
            'idTK', '=', $id_tk, 'AND',
            'trangThai', '=', 0, ''
        ]
    ]);
    if (!empty($kt_yeucau)) return ['status' => false, 'message' => 'Đang có yêu cầu chờ xử lý'];

    $res = _insert_info($conn, 'yeucau_thamgia',
        ['idNhom', 'idTK', 'ChieuMoi', 'loiNhan', 'trangThai', 'ngayGui'],
        [$id_nhom, $id_tk, $chieu_moi, $loi_nhan, 0, date('Y-m-d H:i:s')]
    );

    return $res 
        ? ['status' => true, 'message' => 'Gửi yêu cầu thành công']
        : ['status' => false, 'message' => 'Lỗi hệ thống'];
}

/**
 * 3. PHÊ DUYỆT YÊU CẦU
 */
function duyet_yeu_cau_nhom($conn, $id_nguoi_duyet, $id_yeu_cau, $trang_thai_moi) {
    $yc = truy_van_mot_ban_ghi($conn, 'yeucau_thamgia', 'idYeuCau', $id_yeu_cau);
    if (!$yc) return ['status' => false, 'message' => 'Yêu cầu không tồn tại'];
    if ($yc['trangThai'] != 0) return ['status' => false, 'message' => 'Yêu cầu này đã được xử lý'];

    // Check quyền (Giữ nguyên)
    if ($yc['ChieuMoi'] == 1) {
        $nhom = truy_van_mot_ban_ghi($conn, 'nhom', 'idnhom', $yc['idNhom']);
        if ($nhom['idnhomtruong'] != $id_nguoi_duyet) {
            return ['status' => false, 'message' => 'Chỉ trưởng nhóm mới được duyệt đơn xin gia nhập'];
        }
    } else {
        if ($yc['idTK'] != $id_nguoi_duyet) {
            return ['status' => false, 'message' => 'Bạn không phải người được mời'];
        }
    }

    mysqli_begin_transaction($conn);
    try {
        $cond = ['idYeuCau' => ['=', $id_yeu_cau, '']];
        _update_info($conn, 'yeucau_thamgia', 
            ['trangThai', 'ngayPhanHoi'], 
            [$trang_thai_moi, date('Y-m-d H:i:s')], 
            $cond
        );

        if ($trang_thai_moi == 1) {
            // Thêm thành viên mới (isActive = 1)
            $res_add = _insert_info($conn, 'thanhviennhom',
                ['idnhom', 'idtk', 'idvaitronhom', 'trangthai', 'ngaythamgia', 'isActive'],
                [$yc['idNhom'], $yc['idTK'], 2, 1, date('Y-m-d H:i:s'), 1]
            );
            if (!$res_add) throw new Exception('Lỗi thêm thành viên vào nhóm');
        }

        mysqli_commit($conn);
        $msg = ($trang_thai_moi == 1) ? "Đã chấp nhận yêu cầu" : "Đã từ chối yêu cầu";
        return ['status' => true, 'message' => $msg];

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['status' => false, 'message' => $e->getMessage()];
    }
}

/**
 * 4. RỜI NHÓM / XÓA THÀNH VIÊN (Dùng isActive)
 * Thêm 1 trường isActive vào bảng thanhviennhom
 * truyền vào $id_nguoi_thuc_hien (để check quyền), $id_nhom, $id_tk_bi_xoa
 */
function roi_nhom($conn, $id_nguoi_thuc_hien, $id_nhom, $id_tk_bi_xoa) {
    // Check quyền
    if ($id_nguoi_thuc_hien != $id_tk_bi_xoa) {
        $nhom = truy_van_mot_ban_ghi($conn, 'nhom', 'idnhom', $id_nhom);
        if (!$nhom || $nhom['idnhomtruong'] != $id_nguoi_thuc_hien) {
            return ['status' => false, 'message' => 'Bạn không có quyền mời thành viên ra khỏi nhóm'];
        }
    }

    // Check trưởng nhóm
    $conditions_select = [
        'WHERE' => [
            'idnhom', '=', $id_nhom, 'AND', 
            'idtk', '=', $id_tk_bi_xoa, 'AND',
            'isActive', '=', 1, '' // Chỉ check người đang Active
        ]
    ];
    $tv = _select_info($conn, 'thanhviennhom', [], $conditions_select);
    
    if (!empty($tv) && $tv[0]['idvaitronhom'] == 1) {
        return ['status' => false, 'message' => 'Trưởng nhóm không thể rời đi. Hãy chuyển quyền trưởng nhóm trước.'];
    }

    // UPDATE isActive = 0 (Deactive)
    $conditions_update = [
        'idnhom' => ['=', $id_nhom, 'AND'],
        'idtk'   => ['=', $id_tk_bi_xoa, '']
    ];

    $result = _update_info($conn, 'thanhviennhom', 
        ['isActive'], 
        [0], 
        $conditions_update
    );
    
    if ($result) {
        return ['status' => true, 'message' => 'Đã rời khỏi nhóm'];
    }
    return ['status' => false, 'message' => 'Lỗi hệ thống'];
}

?>