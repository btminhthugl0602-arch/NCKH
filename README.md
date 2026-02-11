# HỆ THỐNG QUẢN LÝ SỰ KIỆN

Hệ thống quản lý sự kiện được xây dựng bằng PHP với giao diện Material Dashboard.

## CÁC TÍNH NĂNG ĐÃ HOÀN THIỆN

### 1. Đăng nhập
- ✅ Form đăng nhập với xác thực username/email và mật khẩu
- ✅ Hiển thị thông báo lỗi khi đăng nhập sai
- ✅ Kiểm tra trạng thái tài khoản (active/inactive)
- ✅ Chuyển hướng dựa theo quyền người dùng
- ✅ Giao diện responsive đẹp mắt với hiệu ứng gradient
- ✅ Animation và floating icons
- ✅ Tối ưu cho mobile

### 2. Toggle Sidebar
- ✅ Nút toggle sidebar hiển thị ở tất cả màn hình (không chỉ mobile)
- ✅ Toggle mượt mà với animation
- ✅ Tự động ẩn sidebar khi click bên ngoài (trên mobile)
- ✅ Lưu trạng thái sidebar

### 3. Cải thiện giao diện
- ✅ Giao diện đăng nhập hiện đại với gradient và animations
- ✅ Responsive tốt trên tất cả thiết bị
- ✅ Sidebar và navbar tiếng Việt
- ✅ Custom CSS với nhiều cải tiến
- ✅ Custom JavaScript với các utility functions
- ✅ Toast notifications
- ✅ Form validation
- ✅ Smooth scrolling

## CẤU TRÚC THỨ MỤC

```
web_hoan_chinh/
├── config.php              # File cấu hình database
├── index.php               # File index chính
├── routes.php              # File routing
├── modules/                # Các module chức năng
│   ├── auth/              
│   │   └── login.php      # Trang đăng nhập (đã cải tiến)
│   ├── dashboard/         
│   │   └── index.php      # Trang chủ
│   └── ...
├── template/              
│   ├── assets/
│   │   ├── css/
│   │   │   ├── material-dashboard.css
│   │   │   └── custom.css         # CSS tùy chỉnh mới
│   │   └── js/
│   │       ├── material-dashboard.min.js
│   │       └── custom.js          # JavaScript tùy chỉnh mới
│   └── layouts/
│       ├── header.php             # Header (đã cập nhật)
│       ├── footer.php             # Footer (đã cập nhật)
│       ├── sidebar.php            # Sidebar (đã cải tiến)
│       └── navbar.php             # Navbar (đã thêm toggle)
└── README.md              # File này
```

## CÀI ĐẶT

### Yêu cầu hệ thống
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Apache/Nginx web server

### Các bước cài đặt

1. **Upload code lên server**
   ```bash
   # Upload toàn bộ thư mục web_hoan_chinh lên server
   ```

2. **Cấu hình database**
   - Tạo database mới
   - Import file SQL (nếu có)
   - Cập nhật thông tin database trong `config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'your_database');
   ```

3. **Cấu hình URL**
   - Cập nhật `_HOST_URL` trong `config.php` phù hợp với domain của bạn

4. **Set quyền thư mục**
   ```bash
   chmod -R 755 web_hoan_chinh/
   ```

## SỬ DỤNG

### Đăng nhập
1. Truy cập trang đăng nhập: `your-domain.com/?module=auth&action=login`
2. Nhập username/email và mật khẩu
3. Hệ thống sẽ kiểm tra và chuyển hướng tới dashboard

### Sử dụng Sidebar Toggle
- Click vào icon 3 gạch ngang ở góc trên bên trái navbar
- Sidebar sẽ thu/mở ra
- Trên mobile, sidebar tự động ẩn khi click ra ngoài

### Tính năng khác
- **Toast Notifications**: Sử dụng `showToast(message, type)` trong JavaScript
- **Loading Spinner**: Sử dụng `showLoading(button)` cho các nút submit
- **AJAX Helper**: Sử dụng `ajax(url, options)` để gọi API

## TÍNH NĂNG MỚI

### CSS Custom (`custom.css`)
- Sidebar toggle animation
- Responsive improvements
- Card hover effects
- Form validation styles
- Alert styles
- Badge styles
- Toast notifications
- Modal improvements
- Print styles
- Utility classes

### JavaScript Custom (`custom.js`)
- Sidebar toggle functionality
- Form validation
- Tooltips initialization
- Smooth scroll
- Loading spinner helper
- Toast notification helper
- AJAX helper
- Date/Number formatters
- Debounce function
- Auto-hide alerts
- Input group outline handlers

## BROWSER SUPPORT

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)

## RESPONSIVE BREAKPOINTS

- Mobile: < 576px
- Tablet: 576px - 991px
- Desktop: 992px - 1199px
- Large Desktop: ≥ 1200px

## LƯU Ý

1. **Security**: 
   - Đã thêm xác thực `_AUTHEN` trong tất cả các file
   - Cần implement thêm CSRF protection
   - Nên hash password với `password_hash()` thay vì lưu plain text

2. **Database**:
   - Cần có bảng `users` với các trường: `user_id`, `user_name`, `user_email`, `user_password`, `user_status`, `user_tac_nhan`

3. **Sessions**:
   - Sessions được sử dụng để lưu thông tin đăng nhập
   - Cần start session trong file index.php hoặc config.php

## TROUBLESHOOTING

### Lỗi không đăng nhập được
- Kiểm tra kết nối database
- Kiểm tra tên bảng và các trường trong database
- Kiểm tra session có được start chưa

### Sidebar không toggle
- Kiểm tra file `custom.js` đã được include chưa
- Kiểm tra console browser xem có lỗi JavaScript không

### Giao diện bị vỡ
- Kiểm tra các file CSS đã được load chưa
- Clear cache browser
- Kiểm tra đường dẫn `_HOST_URL_TEMPLATES`

## HỖ TRỢ

Nếu gặp vấn đề, vui lòng:
1. Kiểm tra lại các file cấu hình
2. Xem error log của PHP
3. Kiểm tra console browser

## PHÁT TRIỂN TIẾP

Các tính năng cần bổ sung:
- [ ] Quên mật khẩu
- [ ] Đăng ký tài khoản mới
- [ ] Đổi mật khẩu
- [ ] Quản lý profile
- [ ] 2FA Authentication
- [ ] Password strength meter
- [ ] Remember me functionality
- [ ] Rate limiting cho login

## CREDITS

- Material Dashboard by Creative Tim
- Bootstrap 5
- Font Awesome Icons
- Google Material Icons

---

**Version**: 1.0.0  
**Last Updated**: 2026-02-11
