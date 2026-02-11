# CHANGELOG

Tất cả các thay đổi quan trọng của dự án sẽ được ghi lại trong file này.

## [1.0.0] - 2026-02-11

### Đã thêm (Added)

#### 1. Chức năng Đăng nhập hoàn chỉnh
- Form đăng nhập với validation đầy đủ
- Xác thực username hoặc email
- Kiểm tra mật khẩu
- Kiểm tra trạng thái tài khoản (active/inactive)
- Phân quyền và chuyển hướng theo role
- Hiển thị thông báo lỗi chi tiết
- Remember me checkbox
- Link quên mật khẩu

#### 2. Toggle Sidebar
- Nút toggle sidebar hiển thị ở tất cả kích thước màn hình
- Chuyển từ chỉ hiển thị trên mobile (d-xl-none) sang hiển thị toàn màn hình
- Animation mượt mà khi toggle
- Tự động ẩn sidebar trên mobile khi click bên ngoài
- Icon toggle 3 gạch ngang với hiệu ứng hover

#### 3. Giao diện đăng nhập cải tiến
- Background gradient hiện đại (purple gradient)
- Floating icons với animation
- Illustration panel bên trái với thông điệp chào mừng
- Card đăng nhập với backdrop blur effect
- Button gradient với hover effect
- Alert thông báo lỗi với màu sắc phù hợp
- Responsive hoàn hảo trên mobile
- Navbar cải tiến với menu rút gọn

#### 4. File CSS Custom (custom.css)
- Sidebar toggle styling
- Responsive improvements cho tất cả breakpoints
- Card hover effects
- Button animations
- Form input styles với focus state
- Breadcrumb styling
- Dropdown animation
- Loading spinner
- Alert improvements
- Badge styling
- Scrollbar custom
- Mobile menu improvements
- Status badges (active, inactive, pending)
- Gradient utilities
- Print styles
- Focus visible states

#### 5. File JavaScript Custom (custom.js)
- Sidebar toggle functionality
  - Toggle cho tất cả màn hình
  - Auto-hide trên mobile
  - Event listeners
- Form validation
- Tooltips initialization
- Smooth scroll
- Helper functions:
  - `showLoading(button)` - Hiển thị loading spinner
  - `showToast(message, type)` - Toast notifications
  - `confirmAction(message, callback)` - Confirm dialog
  - `formatNumber(number)` - Format số theo locale VN
  - `formatDate(date)` - Format ngày theo locale VN
  - `ajax(url, options)` - AJAX helper
  - `debounce(func, wait)` - Debounce function
- Auto-hide alerts sau 5 giây
- Input group outline label handlers
- Responsive table handling

#### 6. Cải tiến Layouts

**Header (header.php)**
- Thay đổi lang từ "en" sang "vi"
- Include file custom.css
- Thêm class g-sidenav-show vào body

**Sidebar (sidebar.php)**
- Tiếng Việt hóa tất cả menu items
- Cập nhật menu phù hợp với hệ thống quản lý sự kiện:
  - Trang chủ (Dashboard)
  - Sự kiện (Events)
  - Người dùng (Users)
  - Nhóm (Groups)
  - Hồ sơ (Profile)
  - Đăng xuất (Logout)
- Xóa các menu không cần thiết
- Cải thiện footer sidebar
- Cập nhật logo và branding

**Navbar (navbar.php)**
- Di chuyển nút toggle sidebar ra ngoài
- Thêm wrapper div cho toggle và breadcrumb
- Toggle hiển thị ở tất cả màn hình
- Tiếng Việt hóa placeholder tìm kiếm
- Tiếng Việt hóa thông báo
- Thêm text "Đăng xuất" cho icon account
- Cải thiện responsive

**Footer (footer.php)**
- Tiếng Việt hóa nội dung
- Đơn giản hóa, xóa fixed-plugin
- Include file custom.js
- Cập nhật copyright và links

#### 7. Documentation
- README.md với hướng dẫn chi tiết
- CHANGELOG.md để theo dõi thay đổi
- Inline comments trong code

### Đã thay đổi (Changed)

#### Đăng nhập
- **Trước**: Form đăng nhập cơ bản, chưa có xử lý
- **Sau**: Form hoàn chỉnh với validation, error handling, và phân quyền

#### Sidebar Toggle
- **Trước**: Chỉ hiển thị trên màn hình nhỏ (d-xl-none)
- **Sau**: Hiển thị ở tất cả kích thước màn hình

#### Giao diện đăng nhập
- **Trước**: Giao diện đơn giản, ít thu hút
- **Sau**: Giao diện hiện đại với gradient, animations, responsive tốt

#### Language
- **Trước**: Tiếng Anh
- **Sau**: Tiếng Việt trong tất cả giao diện

### Đã sửa (Fixed)

1. **Lỗi form đăng nhập**
   - Form không có thuộc tính method="POST"
   - Input không có name attributes
   - Button type là "button" thay vì "submit"
   - Xử lý đăng nhập ở cuối file thay vì đầu file

2. **Lỗi responsive**
   - Navbar menu không đẹp trên mobile
   - Sidebar không ẩn được trên mobile
   - Card đăng nhập không responsive tốt
   - Illustration panel không ẩn được trên mobile

3. **Lỗi UX**
   - Không có thông báo lỗi trực quan
   - Không giữ được giá trị đã nhập khi submit lỗi
   - Không có loading state khi submit

4. **Lỗi hiển thị**
   - Breadcrumb styling
   - Alert dismissible
   - Input focus states
   - Button hover effects

### Cải tiến kỹ thuật (Technical Improvements)

1. **Security**
   - Thêm `trim()` cho input
   - Escape HTML output với `htmlspecialchars()`
   - Kiểm tra `_AUTHEN` constant

2. **Performance**
   - Debounce cho search inputs
   - Lazy loading cho images (có thể implement)
   - Minified CSS/JS (sẵn sàng để minify)

3. **Code Quality**
   - Comments rõ ràng
   - Consistent formatting
   - Separated concerns (CSS, JS riêng)
   - Reusable functions

4. **Maintainability**
   - Modular structure
   - Custom files riêng biệt
   - Documentation đầy đủ

### Breaking Changes

Không có breaking changes trong version này vì đây là version đầu tiên hoàn thiện.

### Migration Guide

Từ version gốc sang version 1.0.0:

1. Backup toàn bộ dự án cũ
2. Copy các file mới:
   - `modules/auth/login.php`
   - `template/layouts/*.php`
   - `template/assets/css/custom.css`
   - `template/assets/js/custom.js`
3. Cập nhật database nếu cần
4. Test đăng nhập và các chức năng

### Known Issues

1. Password vẫn được lưu dạng plain text trong database (cần implement password_hash)
2. Chưa có CSRF protection
3. Chưa có rate limiting cho login attempts
4. Remember me chưa được implement backend

### Roadmap

#### Version 1.1.0 (Planned)
- [ ] Password hashing
- [ ] CSRF protection
- [ ] Remember me functionality
- [ ] Rate limiting

#### Version 1.2.0 (Planned)
- [ ] Quên mật khẩu
- [ ] Đăng ký tài khoản
- [ ] Email verification

#### Version 2.0.0 (Future)
- [ ] Two-factor authentication
- [ ] Social login
- [ ] Role-based access control (RBAC)

---

**Contributors**: Development Team  
**Date**: February 11, 2026
