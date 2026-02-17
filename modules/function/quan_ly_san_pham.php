<?php
require_once __DIR__ . '/base.php';

/**
 * 1. Đăng ký sản phẩm dự thi (Đề tài)
 */
function dang_ky_san_pham($conn, $id_nhom, $id_sk, $id_chu_de, $ten_san_pham) {
    // Validate: Nhóm trưởng mới được đăng ký (Check ở frontend hoặc check lại ở đây)
    // Validate: Mỗi nhóm chỉ 1 sản phẩm trong 1 sự kiện
    $check = _select_info($conn, 'sanpham', [], [
        'WHERE' => ['idNhom', '=', $id_nhom, 'AND', 'idSK', '=', $id_sk, '']
    ]);
    if (!empty($check)) return ['status' => false, 'message' => 'Nhóm đã đăng ký sản phẩm rồi'];

    // CSDL: sanpham(idNhom, idSK, idChuDeSK, tensanpham, TrangThai, isActive)
    // Lưu ý: idChuDeSK là ID trong bảng trung gian CHUDE_SUKIEN, không phải bảng CHUDE gốc
    // Ta cần lấy idChuDeSK từ idChuDe và idSK
    
    $chude_sk = _select_info($conn, 'chude_sukien', [], [
        'WHERE' => ['idSK', '=', $id_sk, 'AND', 'idchude', '=', $id_chu_de, '']
    ]);
    
    if (empty($chude_sk)) return ['status' => false, 'message' => 'Chủ đề không thuộc sự kiện này'];
    $id_chu_de_sk = $chude_sk[0]['idChuDeSK'];

    $result = _insert_info($conn, 'sanpham',
        ['idNhom', 'idSK', 'idChuDeSK', 'tensanpham', 'TrangThai', 'isActive'],
        [$id_nhom, $id_sk, $id_chu_de_sk, $ten_san_pham, 'Chờ duyệt', 1]
    );

    return $result 
        ? ['status' => true, 'message' => 'Đăng ký đề tài thành công', 'idSanPham' => mysqli_insert_id($conn)]
        : ['status' => false, 'message' => 'Lỗi hệ thống'];
}

/**
 * 2. Nộp tài liệu (Full sản phẩm)
 */
function nop_tai_lieu_san_pham($conn, $id_nguoi_nop, $id_san_pham, $link_tai_lieu, $loai_tai_lieu = 1) {
    // Validate: Người nộp phải thuộc nhóm sở hữu sản phẩm (Bỏ qua để code gọn, frontend check session)
    
    // CSDL: sanpham(idloaitailieu, moTataiLieu, TrangThai)
    // moTataiLieu lưu link file/drive
    
    $conditions = ['idSanPham' => ['=', $id_san_pham, '']];
    $result = _update_info($conn, 'sanpham', 
        ['idloaitailieu', 'moTataiLieu', 'TrangThai'], 
        [$loai_tai_lieu, $link_tai_lieu, 'Đã nộp'], 
        $conditions
    );

    return $result 
        ? ['status' => true, 'message' => 'Nộp tài liệu thành công']
        : ['status' => false, 'message' => 'Lỗi nộp bài'];
}

/**
 * 3. Duyệt sản phẩm (Dành cho BTC)
 */
function duyet_san_pham($conn, $id_nguoi_duyet, $id_san_pham, $trang_thai_moi) { // trang_thai: 'Đã duyệt', 'Bị loại'
    if (!kiem_tra_quyen_he_thong($conn, $id_nguoi_duyet, 'event.manage')) {
        return ['status' => false, 'message' => 'Không có quyền'];
    }

    $conditions = ['idSanPham' => ['=', $id_san_pham, '']];
    $result = _update_info($conn, 'sanpham', ['TrangThai'], [$trang_thai_moi], $conditions);

    return ['status' => true, 'message' => 'Đã cập nhật trạng thái sản phẩm'];
}
?>