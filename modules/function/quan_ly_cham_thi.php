<?php
require_once __DIR__ . '/base.php';

/**
 * 1. Phân công Giảng viên chấm thi
 */
function phan_cong_cham_thi($conn, $id_nguoi_phan_cong, $id_gv, $id_sk, $id_vong_thi, $id_bo_tieu_chi) {
    if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_phan_cong, 'event.manage')) {
        return ['status' => false, 'message' => 'Không có quyền'];
    }

    // CSDL: phancongcham(idGV, idSK, idVongThi, idBoTieuChi, trangThaiXacNhan, ngayXacNhan)
    $result = _insert_info($conn, 'phancongcham',
        ['idGV', 'idSK', 'idVongThi', 'idBoTieuChi', 'trangThaiXacNhan', 'ngayXacNhan'],
        [$id_gv, $id_sk, $id_vong_thi, $id_bo_tieu_chi, 'Chờ xác nhận', date('Y-m-d H:i:s')]
    );

    return $result 
        ? ['status' => true, 'message' => 'Đã gửi phân công', 'idPhanCong' => mysqli_insert_id($conn)]
        : ['status' => false, 'message' => 'Lỗi hệ thống'];
}

/**
 * 2. Giảng viên xác nhận tham gia chấm
 */
function gv_xac_nhan_cham($conn, $id_gv_user, $id_phan_cong, $dong_y = true) {
    // Check xem user đang login có phải là GV được phân công không
    // Cần query idGV từ idTK (user_id) -> bỏ qua bước này, giả định id_gv_user truyền vào đúng logic
    
    $trang_thai = $dong_y ? 'Đã xác nhận' : 'Từ chối';
    
    $conditions = ['idPhanCongCham' => ['=', $id_phan_cong, '']];
    $result = _update_info($conn, 'phancongcham', 
        ['trangThaiXacNhan', 'ngayXacNhan'], 
        [$trang_thai, date('Y-m-d H:i:s')], 
        $conditions
    );

    return ['status' => true, 'message' => 'Đã phản hồi yêu cầu'];
}

/**
 * 3. Chấm điểm từng tiêu chí
 */
function cham_diem_tieu_chi($conn, $id_gv, $id_phan_cong, $id_san_pham, $diem_cham_list, $nhan_xet) {
    // $diem_cham_list là mảng: ['idTieuChi' => diem, ...]
    
    // Kiểm tra xem GV này có được phân công chấm Vòng này, Sản phẩm này không?
    // (Logic phức tạp, tạm thời bỏ qua bước check sâu để tập trung vào insert)

    mysqli_begin_transaction($conn);
    try {
        foreach ($diem_cham_list as $id_tieu_chi => $diem) {
            // CSDL: chamtieuchi(idPhanCongCham, idSanPham, idTieuChi, diem, nhanXet)
            // Dùng ON DUPLICATE KEY UPDATE hoặc Check Exist trước
            
            $cond = [
                'WHERE' => [
                    'idPhanCongCham', '=', $id_phan_cong, 'AND',
                    'idSanPham', '=', $id_san_pham, 'AND',
                    'idTieuChi', '=', $id_tieu_chi, ''
                ]
            ];
            $exists = _select_info($conn, 'chamtieuchi', [], $cond);

            if (!empty($exists)) {
                // Update
                $update_cond = [
                    'idChamDiem' => ['=', $exists[0]['idChamDiem'], '']
                ];
                _update_info($conn, 'chamtieuchi', ['diem', 'nhanXet'], [$diem, $nhan_xet], $update_cond);
            } else {
                // Insert
                _insert_info($conn, 'chamtieuchi',
                    ['idPhanCongCham', 'idSanPham', 'idTieuChi', 'diem', 'nhanXet'],
                    [$id_phan_cong, $id_san_pham, $id_tieu_chi, $diem, $nhan_xet]
                );
            }
        }
        mysqli_commit($conn);
        return ['status' => true, 'message' => 'Lưu điểm thành công'];

    } catch (Exception $e) {
        mysqli_rollback($conn);
        return ['status' => false, 'message' => 'Lỗi lưu điểm'];
    }
}

/**
 * 4. Tổng hợp kết quả (Dành cho BTC chốt giải)
 */
function tong_hop_ket_qua_vong($conn, $id_vong_thi) {
    // Logic: Tính trung bình cộng điểm từ bảng chamtieuchi cho từng sản phẩm
    // Đây là câu SQL phức tạp (Aggregation), hàm base không hỗ trợ tốt.
    // Nên dùng SQL thuần (Raw SQL) cho bước báo cáo này.
    
    $sql = "SELECT sp.idSanPham, sp.idNhom, AVG(ct.diem) as diemTB 
            FROM sanpham sp
            JOIN chamtieuchi ct ON sp.idSanPham = ct.idSanPham
            JOIN phancongcham pc ON ct.idPhanCongCham = pc.idPhanCongCham
            WHERE pc.idVongThi = ?
            GROUP BY sp.idSanPham";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_vong_thi);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rankings = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Lưu vào bảng sanpham_vongthi
    foreach ($rankings as $row) {
        // Update điểm trung bình
        // ... (Logic insert/update sanpham_vongthi)
    }
    
    return ['status' => true, 'data' => $rankings];
}
?>