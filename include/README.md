# HỆ THỐNG QUẢN LÝ NCKH - CƠ SỞ HẠ TẦNG

## 📁 CẤU TRÚC THỨ MỤC

```
NCKH/
├── config.php              ✅ Cấu hình hệ thống
├── index.php               ✅ File routing chính
├── include/
│   ├── connect.php        ✅ Kết nối database PDO
│   ├── database.php       ✅ 7 hàm CRUD cơ bản
│   ├── function.php       ✅ 20+ hàm tiện ích
│   ├── session.php        ✅ Quản lý session & auth
│   └── index.php          ✅ Bảo vệ thư mục
└── modules/               📂 Các module khác
```

---

## 🔧 CÁC FILE VỪA TẠO

### 1️⃣ **connect.php** - Kết nối Database
**Chức năng:**
- Kết nối MySQL sử dụng PDO
- Set UTF-8 charset
- Error handling
- Biến global `$conn`

**Sử dụng:**
```php
// File này được include tự động trong index.php
// Biến $conn có thể dùng ở mọi nơi
```

---

### 2️⃣ **database.php** - CRUD Operations

#### **db_insert()** - Thêm mới dữ liệu
```php
// Ví dụ: Thêm tài khoản mới
$userId = db_insert('taikhoan', [
    'tenDangNhap' => 'nguyenvana',
    'matKhau' => password_hash('123456', PASSWORD_DEFAULT),
    'email' => 'nguyenvana@example.com',
    'hoTen' => 'Nguyễn Văn A',
    'idLoaiTK' => 3,
    'trangThai' => 'ACTIVE'
]);

echo "ID vừa tạo: " . $userId;
```

#### **db_update()** - Cập nhật dữ liệu
```php
// Ví dụ: Cập nhật thông tin user
$success = db_update(
    'taikhoan',
    ['hoTen' => 'Nguyễn Văn B', 'email' => 'nguyenvanb@example.com'],
    'idTK = :id',
    ['id' => 5]
);

if ($success) {
    echo "Cập nhật thành công!";
}
```

#### **db_delete()** - Xóa dữ liệu
```php
// Ví dụ: Xóa user
$success = db_delete('taikhoan', 'idTK = :id', ['id' => 10]);
```

#### **db_get_one()** - Lấy 1 record
```php
// Ví dụ: Lấy thông tin user theo ID
$user = db_get_one('taikhoan', 'idTK = :id', ['id' => 5]);
echo $user['hoTen'];

// Ví dụ: Lấy user theo email
$user = db_get_one('taikhoan', 'email = :email', ['email' => 'admin@example.com']);
```

#### **db_get_all()** - Lấy nhiều records
```php
// Ví dụ: Lấy tất cả sinh viên
$sinhviens = db_get_all('taikhoan', 'idLoaiTK = :type', ['type' => 3], 'ngayTao DESC');

foreach ($sinhviens as $sv) {
    echo $sv['hoTen'] . '<br>';
}

// Ví dụ: Lấy tất cả đề tài đang ACTIVE
$detais = db_get_all('detai', 'trangThai = :status', ['status' => 'ACTIVE']);
```

#### **db_query()** - Query tùy chỉnh
```php
// Ví dụ: Join nhiều bảng
$sql = "SELECT dt.*, tk.hoTen as tenGV 
        FROM detai dt 
        LEFT JOIN taikhoan tk ON dt.idGV = tk.idTK 
        WHERE dt.trangThai = :status";

$results = db_query($sql, ['status' => 'ACTIVE']);
```

#### **db_count()** - Đếm số lượng
```php
// Ví dụ: Đếm số sinh viên
$total = db_count('taikhoan', 'idLoaiTK = :type', ['type' => 3]);
echo "Tổng số sinh viên: " . $total;
```

---

### 3️⃣ **session.php** - Quản lý Session

#### **Kiểm tra đăng nhập**
```php
if (isLoggedIn()) {
    echo "User đã đăng nhập";
}

// Yêu cầu phải login
requireLogin(); // Redirect nếu chưa login
```

#### **Lấy thông tin user**
```php
$userId = getUserId();        // ID user
$userName = getUserName();    // Tên user
$userEmail = getUserEmail();  // Email
$userType = getUserType();    // Loại tài khoản (1,2,3)
```

#### **Kiểm tra quyền**
```php
if (isAdmin()) {
    echo "User là Admin";
}

if (isGiangVien()) {
    echo "User là Giảng viên";
}

if (isSinhVien()) {
    echo "User là Sinh viên";
}

// Yêu cầu quyền Admin
requireAdmin(); // Redirect nếu không phải admin
```

#### **Đăng nhập**
```php
// Sau khi verify user từ database
$user = db_get_one('taikhoan', 'email = :email', ['email' => $email]);

if ($user && password_verify($password, $user['matKhau'])) {
    setUserSession($user); // Set session
    redirect(url('dashboard', 'index'));
}
```

#### **Đăng xuất**
```php
logout(); // Auto redirect về login
```

---

### 4️⃣ **function.php** - Hàm Tiện Ích

#### **URL & Redirect**
```php
// Tạo URL
$url = url('auth', 'login'); 
// => http://localhost/NCKH?module=auth&action=login

$url = url('detai', 'view', ['id' => 5]);
// => http://localhost/NCKH?module=detai&action=view&id=5

// Redirect
redirect(url('dashboard', 'index'));
```

#### **Flash Messages**
```php
// Set message
setFlash('msg', 'Đăng ký thành công!', 'success');
setFlash('error', 'Email đã tồn tại', 'danger');

// Hiển thị message (trong view)
echo showFlash('msg');
// => <div class="alert alert-success">Đăng ký thành công!</div>
```

#### **Validation**
```php
$errors = [];

// Required
if ($error = validateRequired($hoTen, 'Họ tên')) {
    $errors[] = $error;
}

// Email
if ($error = validateEmail($email)) {
    $errors[] = $error;
}

// Length
if ($error = validateLength($password, 6, 32, 'Mật khẩu')) {
    $errors[] = $error;
}

// Phone
if ($error = validatePhone($dienThoai)) {
    $errors[] = $error;
}

if (empty($errors)) {
    // OK - tiếp tục xử lý
}
```

#### **Upload Image**
```php
if (isset($_FILES['avatar'])) {
    $result = uploadImage($_FILES['avatar'], 'uploads/avatars/');
    
    if ($result['success']) {
        $avatarPath = $result['path'];
        // Lưu path vào database
    } else {
        echo $result['error'];
    }
}
```

#### **Format Data**
```php
// Format ngày
echo formatDate('2024-02-09 14:30:00'); 
// => 09/02/2024 14:30

echo formatDate('2024-02-09', 'd/m/Y'); 
// => 09/02/2024

// Format tiền
echo formatMoney(1000000); 
// => 1.000.000 đ

// Escape HTML
echo e($userInput);

// Truncate
echo truncate($longText, 50);
// => First 50 chars...
```

#### **Status Badge**
```php
echo getStatusBadge('ACTIVE');
// => <span class="badge bg-success">Đang hoạt động</span>

echo getStatusBadge('PENDING');
// => <span class="badge bg-warning">Chờ duyệt</span>
```

#### **CSRF Protection**
```php
// Trong form
<form method="POST">
    <?php echo csrfField(); ?>
    <!-- form fields -->
</form>

// Khi xử lý form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'])) {
        // OK - xử lý form
    } else {
        die('CSRF token invalid');
    }
}
```

#### **Debug**
```php
dd($user); // Dump and die
```

---

## 🧪 CÁCH TEST

### Test 1: Kết nối Database
Thêm vào file `index.php`:
```php
var_dump($conn); // Phải hiển thị object PDO
```

### Test 2: CRUD Functions
Tạo file `test.php`:
```php
<?php
require_once 'config.php';
require_once 'include/connect.php';
require_once 'include/database.php';

// Test Insert
$id = db_insert('taikhoan', [
    'tenDangNhap' => 'test_user',
    'matKhau' => password_hash('123456', PASSWORD_DEFAULT),
    'email' => 'test@example.com',
    'hoTen' => 'Test User',
    'idLoaiTK' => 3,
    'trangThai' => 'ACTIVE'
]);
echo "Inserted ID: $id<br>";

// Test Select
$user = db_get_one('taikhoan', 'idTK = :id', ['id' => $id]);
echo "User: " . $user['hoTen'] . "<br>";

// Test Update
db_update('taikhoan', ['hoTen' => 'Updated Name'], 'idTK = :id', ['id' => $id]);

// Test Count
$total = db_count('taikhoan');
echo "Total users: $total<br>";
```

### Test 3: Session Functions
```php
<?php
session_start();
require_once 'config.php';
require_once 'include/session.php';

// Giả lập login
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin';
$_SESSION['user_type'] = 1;

echo "Logged in: " . (isLoggedIn() ? 'Yes' : 'No') . "<br>";
echo "User: " . getUserName() . "<br>";
echo "Is Admin: " . (isAdmin() ? 'Yes' : 'No') . "<br>";
```

### Test 4: Helper Functions
```php
<?php
session_start();
require_once 'config.php';
require_once 'include/function.php';

// Test URL
echo url('auth', 'login') . "<br>";

// Test Flash
setFlash('test', 'Test message', 'success');
echo showFlash('test');

// Test Validation
echo validateEmail('invalid') . "<br>";
echo validateRequired('', 'Tên') . "<br>";

// Test Format
echo formatDate('2024-02-09 14:30:00') . "<br>";
echo formatMoney(1000000) . "<br>";
```

---

## ✅ CHECKLIST HOÀN THÀNH

- [x] `connect.php` - Kết nối database PDO
- [x] `database.php` - 7 hàm CRUD
- [x] `session.php` - Quản lý session & auth
- [x] `function.php` - 20+ hàm tiện ích
- [x] `index.php` - Bảo vệ thư mục

---

## 📚 TÀI LIỆU THAM KHẢO

### Database Functions
1. `db_insert($table, $data)` - Thêm mới
2. `db_update($table, $data, $where, $params)` - Cập nhật
3. `db_delete($table, $where, $params)` - Xóa
4. `db_get_one($table, $where, $params)` - Lấy 1 record
5. `db_get_all($table, $where, $params, $orderBy)` - Lấy nhiều records
6. `db_query($sql, $params)` - Query tùy chỉnh
7. `db_count($table, $where, $params)` - Đếm

### Session Functions
1. `isLoggedIn()` - Kiểm tra đăng nhập
2. `requireLogin()` - Yêu cầu đăng nhập
3. `getUserId()` - Lấy ID user
4. `getUserName()` - Lấy tên user
5. `getUserEmail()` - Lấy email
6. `getUserType()` - Lấy loại tài khoản
7. `isAdmin()` - Kiểm tra Admin
8. `isGiangVien()` - Kiểm tra Giảng viên
9. `isSinhVien()` - Kiểm tra Sinh viên
10. `requireAdmin()` - Yêu cầu quyền Admin
11. `setUserSession($user)` - Set session
12. `logout()` - Đăng xuất

### Helper Functions
1. `redirect($url)` - Chuyển hướng
2. `url($module, $action, $params)` - Tạo URL
3. `setFlash($key, $msg, $type)` - Set flash message
4. `showFlash($key)` - Hiển thị flash
5. `validateRequired($value, $name)` - Validate required
6. `validateEmail($email)` - Validate email
7. `validateLength($value, $min, $max, $name)` - Validate length
8. `validatePhone($phone)` - Validate phone
9. `uploadImage($file, $dir)` - Upload ảnh
10. `formatDate($date, $format)` - Format ngày
11. `formatMoney($amount)` - Format tiền
12. `e($string)` - Escape HTML
13. `truncate($string, $length)` - Cắt chuỗi
14. `getStatusBadge($status)` - Status badge
15. `generateCsrfToken()` - Tạo CSRF token
16. `verifyCsrfToken($token)` - Verify CSRF
17. `csrfField()` - CSRF input field
18. `dd($data)` - Debug

---

## 🎯 TIẾP THEO

Sau khi hoàn thành phần cơ sở hạ tầng này, bạn có thể:

1. **PROMPT 2**: Tạo trang đăng nhập (Login/Register)
2. **PROMPT 3**: Tạo Dashboard chính
3. **PROMPT 4**: Quản lý Tài khoản
4. **PROMPT 5**: Quản lý Đề tài
5. **PROMPT 6**: Quản lý Hội đồng

---

**Created by:** Claude AI Assistant  
**Date:** February 09, 2026  
**Version:** 1.0
