<?php
    require_once __DIR__ . '/base.php'; // Đảm bảo require file base

    //hàm tạo quy chế mới nhưng chưa gắn điều kiện nào
    //truyền vào $id_nguoi_thuc_hien để check quyền, $id_su_kien để liên kết (dù DB không có cột này), tên quy chế và mô tả
    function tao_quy_che(
        $conn,
        $id_nguoi_thuc_hien,
        $id_su_kien, // Lưu ý: Tham số này sẽ không được dùng để insert vào bảng quyche vì DB không có cột này
        $ten_quy_che,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền'];
        }

        // CSDL: Bảng 'quyche' chỉ có (tenQuyChe, moTa). KHÔNG CÓ 'idSK'.
        // Ta dùng hàm _insert_info chuẩn của base.php thay vì mysqli_query thủ công
        $result = _insert_info($conn, 'quyche', 
            ['tenQuyChe', 'moTa'], 
            [$ten_quy_che, $mo_ta]
        );

        if (!$result) {
            return ['status' => false, 'message' => 'Không tạo được quy chế'];
        }

        return [
            'status' => true,
            'idQuyChe' => mysqli_insert_id($conn),
            'message' => 'Đã tạo quy chế'
        ];
    }
    //hàm tao điều kiện đơn, chỉ tạo điều kiện đơn lẻ thông qua thao tác người dùng
    //  truyền vào $id_nguoi_thuc_hien để check quyền, tên điều kiện, id thuộc tính cần kiểm tra, id toán tử so sánh, giá trị so sánh và mô tả
    function tao_dieu_kien_don(
        $conn,
        $id_nguoi_thuc_hien,
        $ten_dieu_kien,
        $id_thuoc_tinh,
        $id_toan_tu,
        $gia_tri_so_sanh,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền'];
        }

        mysqli_begin_transaction($conn);
        try {
            $res1 = _insert_info($conn, 'dieukien', 
                ['loaiDieuKien', 'tenDieuKien', 'moTa'], 
                ['DON', $ten_dieu_kien, $mo_ta]
            );
            
            if (!$res1) throw new Exception("Lỗi tạo bảng điều kiện cha");
            $id_dieu_kien = mysqli_insert_id($conn);

            $res2 = _insert_info($conn, 'dieukien_don',
                ['idDieuKien', 'idThuocTinhKiemTra', 'idToanTu', 'giaTriSoSanh'],
                [$id_dieu_kien, $id_thuoc_tinh, $id_toan_tu, $gia_tri_so_sanh]
            );

            if (!$res2) throw new Exception("Lỗi tạo bảng điều kiện con");

            mysqli_commit($conn);
            return ['status' => true, 'idDieuKien' => $id_dieu_kien];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return ['status' => false, 'message' => 'Lỗi tạo điều kiện'];
        }
    }
    //Từ các điều kiện đơn, người dùng kết nối để thành tổ hợp
    //truyền vào $id_nguoi_thuc_hien để check quyền, id điều kiện trái, id toán tử logic (AND/OR), id điều kiện phải, tên tổ hợp và mô tả
    function tao_to_hop_dieu_kien(
        $conn,
        $id_nguoi_thuc_hien,
        $id_dieu_kien_trai,
        $id_toan_tu_logic,
        $id_dieu_kien_phai,
        $ten_to_hop,
        $mo_ta = ''
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền'];
        }

        mysqli_begin_transaction($conn);
        try {
            $res1 = _insert_info($conn, 'dieukien', 
                ['loaiDieuKien', 'tenDieuKien', 'moTa'], 
                ['TOHOP', $ten_to_hop, $mo_ta]
            );
            
            if (!$res1) throw new Exception("Lỗi tạo bảng điều kiện cha");
            $id_to_hop = mysqli_insert_id($conn);

            $res2 = _insert_info($conn, 'tohop_dieukien',
                ['idDieuKien', 'idDieuKienTrai', 'idDieuKienPhai', 'idToanTu'],
                [$id_to_hop, $id_dieu_kien_trai, $id_dieu_kien_phai, $id_toan_tu_logic]
            );

            if (!$res2) throw new Exception("Lỗi tạo bảng tổ hợp");

            mysqli_commit($conn);
            return ['status' => true, 'idDieuKien' => $id_to_hop];

        } catch (Exception $e) {
            mysqli_rollback($conn);
            return ['status' => false, 'message' => 'Lỗi tạo tổ hợp'];
        }
    }

    //Sau khi có điều kiện đơn hoặc tổ hợp, gán điều kiện đó cho quy chế
    //truyền vào $id_nguoi_thuc_hien để check quyền, $id_quy_che để xác định quy chế cần gán, $id_dieu_kien_cuoi là id điều kiện đơn hoặc tổ hợp đã tạo ở 2 hàm trên
    function gan_dieu_kien_cho_quy_che(
        $conn,
        $id_nguoi_thuc_hien,
        $id_quy_che,
        $id_dieu_kien_cuoi
    ) {
        if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_thuc_hien, 'event.manage')) {
            return ['status' => false, 'message' => 'Không đủ quyền'];
        }

        $conditions = [
            'WHERE' => ['idQuyChe', '=', $id_quy_che, '']
        ];
        
        $exists = !empty(_select_info($conn, 'quyche_dieukien', [], $conditions));
        
        if ($exists) {
            $update_cond = ['idQuyChe' => ['=', $id_quy_che, '']];
            _update_info($conn, 'quyche_dieukien', ['idDieuKienCuoi'], [$id_dieu_kien_cuoi], $update_cond);
        } else {
            _insert_info($conn, 'quyche_dieukien', 
                ['idQuyChe', 'idDieuKienCuoi'], 
                [$id_quy_che, $id_dieu_kien_cuoi]
            );
        }

        return ['status' => true, 'message' => 'Đã gán điều kiện cho quy chế'];
    }   
    //kiểm tra điều kiện để xác định xem sản phẩm có đủ điều kiện tham gia vòng thi hay không
    //truyền vào $id_dieu_kien là id điều kiện gốc (có thể là đơn hoặc tổ hợp), $du_lieu_dau_vao là mảng dữ liệu của sản phẩm cần kiểm tra (key là idThuocTinh, value là giá trị thực tế)
    //Khi kiểm tra thì cần đệ quy để kiểm tra từng điều kiện con bên trong tổ hợp, nếu là điều kiện đơn thì kiểm tra trực tiếp
    function kiem_tra_dieu_kien($conn, $id_dieu_kien, $du_lieu_dau_vao) {
        $dk = truy_van_mot_ban_ghi($conn, 'dieukien', 'idDieuKien', $id_dieu_kien);
        if (!$dk) return false;

        if ($dk['loaiDieuKien'] == 'DON') {
            return kiem_tra_dieu_kien_don($conn, $id_dieu_kien, $du_lieu_dau_vao);
        }
        if ($dk['loaiDieuKien'] == 'TOHOP') {
            return kiem_tra_to_hop_dieu_kien($conn, $id_dieu_kien, $du_lieu_dau_vao);
        }
        return false;
    }

    function kiem_tra_dieu_kien_don($conn, $id_dieu_kien, $du_lieu) {
        $dk = truy_van_mot_ban_ghi($conn, 'dieukien_don', 'idDieuKien', $id_dieu_kien);
        if (!$dk) return false;

        $gia_tri_thuc_te = $du_lieu[$dk['idThuocTinhKiemTra']] ?? null;
        $gia_tri_so_sanh = $dk['giaTriSoSanh'];

        switch ($dk['idToanTu']) {
            case 1: return $gia_tri_thuc_te == $gia_tri_so_sanh;
            case 2: return $gia_tri_thuc_te > $gia_tri_so_sanh;
            case 3: return $gia_tri_thuc_te < $gia_tri_so_sanh;
            case 4: return $gia_tri_thuc_te >= $gia_tri_so_sanh;
            case 5: return $gia_tri_thuc_te <= $gia_tri_so_sanh;
            case 6: return $gia_tri_thuc_te != $gia_tri_so_sanh;
            default: return false;
        }
    }

    function kiem_tra_to_hop_dieu_kien($conn, $id_dieu_kien, $du_lieu) {
        $to_hop = truy_van_mot_ban_ghi($conn, 'tohop_dieukien', 'idDieuKien', $id_dieu_kien);
        if (!$to_hop) return false;

        $ket_qua_trai = kiem_tra_dieu_kien($conn, $to_hop['idDieuKienTrai'], $du_lieu);
        $ket_qua_phai = kiem_tra_dieu_kien($conn, $to_hop['idDieuKienPhai'], $du_lieu);

        if ($to_hop['idToanTu'] == 1) return $ket_qua_trai && $ket_qua_phai;
        if ($to_hop['idToanTu'] == 2) return $ket_qua_trai || $ket_qua_phai;

        return false;
    }
?>