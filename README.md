# HỆ THỐNG QUẢN LÝ SỰ KIỆN - PHIÊN BẢN CẬP NHẬT

## CÁC TÍNH NĂNG ĐÃ SỬA VÀ THÊM MỚI

### 1. NÚT TẮT/MỞ SIDEBAR
✅ **ĐÃ SỬA**: Nút toggle sidebar giờ đã hoạt động hoàn hảo
- Nhấn vào icon 3 gạch ngang ở navbar để ẩn/hiện sidebar
- Tự động hiển thị sidebar trên màn hình desktop
- Responsive tốt trên mobile

**File đã sửa:**
- `template/layouts/navbar.php` - Thêm nút toggle
- `template/layouts/footer.php` - Thêm JavaScript xử lý toggle
- `template/layouts/header.php` - Đã có class `g-sidenav-show`

---

### 2. PHÂN QUYỀN THEO ROLE

#### A. ADMIN (role = 1)
✅ Quyền đầy đủ:
- Xem tất cả các trang
- Sidebar hiển thị đầy đủ: Trang chủ, Quản lý sự kiện, Người dùng, Nhóm, Hồ sơ, Đăng xuất, Trợ giúp
- Icon Edit (chiếc bút) trên các card sự kiện
- Mô tả: "Quản lý sự kiện"

#### B. USER & TEACHER (role = 2, 3)
✅ Quyền hạn chế:
- KHÔNG thể xem trang "Người dùng"
- Sidebar ẩn menu "Người dùng" và "Nhóm"
- Icon Visibility (con mắt) thay vì Edit trên các card sự kiện
- Mô tả: "Sự kiện" (không có chữ "Quản lý")

#### C. GUEST (role = 'guest')
✅ Quyền xem cơ bản:
- Chỉ xem được: Trang chủ và Sự kiện
- Sidebar chỉ hiển thị: Trang chủ, Sự kiện, Trợ giúp
- Navbar hiển thị: "Khách"
- KHÔNG có menu Cài đặt, Hồ sơ, Đăng xuất

**File đã sửa:**
- `template/layouts/sidebar.php` - Điều kiện hiển thị menu theo role
- `template/layouts/navbar.php` - Hiển thị "Khách" cho Guest
- `modules/event/index.php` - Icon visibility cho non-admin

---

### 3. ĐĂNG NHẬP GUEST

✅ **Cách sử dụng:**

1. **Tại trang Login:**
   - Nhấn "Đăng nhập với tư cách khách" → Đưa tới Trang chủ
   - Nhấn "Trang chủ" ở navbar → Đưa tới Trang chủ với quyền Guest
   - Nhấn "Sự kiện" ở navbar → Đưa tới trang Sự kiện với quyền Guest

2. **Khi đã là Guest:**
   - Có thể xem Trang chủ và Sự kiện
   - Sidebar chỉ hiển thị 3 menu: Trang chủ, Sự kiện, Trợ giúp
   - Navbar hiển thị "Khách"
   - Nhấn vào "Khách" để quay lại trang Login

**File đã sửa:**
- `modules/auth/login.php` - Xử lý đăng nhập Guest, thêm link điều hướng

---

## HƯỚNG DẪN CÀI ĐẶT

### 1. Chuẩn bị
```bash
# Import database
mysql -u root -p your_database < nckh.sql

# Hoặc sử dụng phpMyAdmin để import file nckh.sql
```

### 2. Cấu hình
Sửa file `config.php`:
```php
define('_HOST_DB', 'localhost');
define('_USER_DB', 'root');
define('_PASS_DB', '');
define('_DB_NAME', 'nckh');
```

### 3. Upload files
- Copy toàn bộ thư mục lên web server (htdocs/www)
- Đảm bảo chmod 755 cho các thư mục

### 4. Truy cập
```
http://localhost/NCKH/
hoặc
http://localhost:8080/NCKH/
```

---

## TÀI KHOẢN DEMO

### Admin
- Username: `admin`
- Password: `admin123`
- Role: 1

### Teacher
- Username: `teacher`
- Password: `teacher123`
- Role: 3

### User
- Username: `user`
- Password: `user123`
- Role: 2

### Guest
- Không cần đăng nhập
- Nhấn "Đăng nhập với tư cách khách" ở trang Login

---

## CẤU TRÚC FILE ĐÃ SỬA

```
NCKH_fixed/
├── modules/
│   ├── auth/
│   │   └── login.php                 ✅ Sửa: Thêm xử lý Guest
│   └── event/
│       └── index.php                 ✅ Sửa: Icon visibility cho non-admin
├── template/
│   └── layouts/
│       ├── header.php                ✅ Đã có g-sidenav-show
│       ├── sidebar.php               ✅ Sửa: Điều kiện role
│       ├── navbar.php                ✅ Sửa: Toggle button + hiển thị Guest
│       └── footer.php                ✅ Sửa: Script toggle sidebar
├── config.php
├── index.php
└── README.md                         ✅ File này
```

---

## KIỂM TRA TÍNH NĂNG

### ✅ Checklist
- [ ] Nút toggle sidebar hoạt động (nhấn icon 3 gạch)
- [ ] Admin thấy đầy đủ menu
- [ ] User/Teacher KHÔNG thấy menu "Người dùng"
- [ ] Guest chỉ thấy 3 menu: Trang chủ, Sự kiện, Trợ giúp
- [ ] Icon edit hiển thị cho Admin
- [ ] Icon visibility (mắt) hiển thị cho User/Teacher
- [ ] Link "Đăng nhập với tư cách khách" hoạt động
- [ ] Navbar hiển thị "Khách" khi ở chế độ Guest
- [ ] Responsive tốt trên mobile

---

## LƯU Ý

1. **Sidebar Toggle**: Nếu sidebar không ẩn/hiện, kiểm tra:
   - File `footer.php` có script toggle
   - Class `g-sidenav-show` trong `<body>`
   - File JavaScript `material-dashboard.min.js` đã load

2. **Guest Mode**: 
   - Session được set với role = 'guest'
   - Không cần database
   - Có thể logout bằng cách nhấn vào "Khách" trên navbar

3. **Phân quyền**:
   - Role 1 = Admin
   - Role 2 = User
   - Role 3 = Teacher
   - Role 'guest' = Khách

---

## HỖ TRỢ

Nếu gặp vấn đề, kiểm tra:
1. PHP version >= 7.4
2. MySQL đã chạy
3. Database đã import đúng
4. Config database đúng
5. Quyền thư mục 755

---

**Phiên bản:** 2.0 - Đã sửa lỗi và thêm tính năng
**Ngày cập nhật:** 12/02/2024
