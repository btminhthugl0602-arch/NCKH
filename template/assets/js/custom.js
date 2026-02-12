/**
 * Custom JavaScript for NCKH System
 */

/**
 * Toast Notification Function
 */
function showToast(message, type = 'info') {
    // Định nghĩa màu sắc cho từng loại
    const colors = {
        'success': 'bg-gradient-success',
        'danger': 'bg-gradient-danger',
        'warning': 'bg-gradient-warning',
        'info': 'bg-gradient-info',
        'primary': 'bg-gradient-primary'
    };
    
    const bgClass = colors[type] || colors['info'];
    
    const toastHTML = `
        <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="material-symbols-rounded me-2">
                        ${type === 'success' ? 'check_circle' : type === 'danger' ? 'error' : type === 'warning' ? 'warning' : 'info'}
                    </i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Tạo hoặc lấy toast container
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Thêm toast vào container
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Hiển thị toast
    const toastElement = toastContainer.lastElementChild;
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
    });
    toast.show();
    
    // Xóa toast sau khi ẩn
    toastElement.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

/**
 * Sidebar Toggle Functionality - FIXED VERSION
 */
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidenav-main');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const body = document.body;
    
    if (!sidebar || !toggleBtn) {
        return;
    }
    
    // Kiểm tra trạng thái từ localStorage
    const sidebarHidden = localStorage.getItem('sidebarHidden') === 'true';
    
    if (sidebarHidden) {
        sidebar.style.transform = 'translateX(-100%)';
        body.classList.add('sidebar-hidden');
    } else {
        sidebar.style.transform = 'translateX(0)';
        body.classList.remove('sidebar-hidden');
    }
    
    // Toggle khi click
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const isCurrentlyHidden = sidebar.style.transform === 'translateX(-100%)';
        
        if (isCurrentlyHidden) {
            // Hiện sidebar
            sidebar.style.transform = 'translateX(0)';
            body.classList.remove('sidebar-hidden');
            localStorage.setItem('sidebarHidden', 'false');
        } else {
            // Ẩn sidebar
            sidebar.style.transform = 'translateX(-100%)';
            body.classList.add('sidebar-hidden');
            localStorage.setItem('sidebarHidden', 'true');
        }
    });
    
    // Responsive - tự động ẩn trên mobile
    function handleResize() {
        if (window.innerWidth < 1200) {
            sidebar.style.transform = 'translateX(-100%)';
            body.classList.add('sidebar-hidden');
        } else {
            const shouldBeHidden = localStorage.getItem('sidebarHidden') === 'true';
            if (shouldBeHidden) {
                sidebar.style.transform = 'translateX(-100%)';
                body.classList.add('sidebar-hidden');
            } else {
                sidebar.style.transform = 'translateX(0)';
                body.classList.remove('sidebar-hidden');
            }
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize(); // Gọi ngay khi load
});

/**
 * Auto-dismiss alerts
 */
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});

/**
 * Form Validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[method="POST"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('Vui lòng điền đầy đủ thông tin bắt buộc!', 'danger');
            }
        });
    });
});

/**
 * Card Animations
 */
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card[data-animation="true"]');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

/**
 * Confirm Delete
 */
function confirmDelete(message) {
    return confirm(message || 'Bạn có chắc chắn muốn xóa?');
}

/**
 * Date Time Validation for Event Form
 */
document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.querySelector('form[name="create_event"]');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            const ngayMoDangKy = new Date(document.getElementById('ngayMoDangKy').value);
            const ngayDongDangKy = new Date(document.getElementById('ngayDongDangKy').value);
            const ngayBatDau = new Date(document.getElementById('ngayBatDau').value);
            const ngayKetThuc = new Date(document.getElementById('ngayKetThuc').value);
            
            if (ngayDongDangKy <= ngayMoDangKy) {
                e.preventDefault();
                showToast('Ngày đóng đăng ký phải sau ngày mở đăng ký!', 'danger');
                return;
            }
            
            if (ngayBatDau <= ngayDongDangKy) {
                e.preventDefault();
                showToast('Ngày bắt đầu phải sau ngày đóng đăng ký!', 'danger');
                return;
            }
            
            if (ngayKetThuc <= ngayBatDau) {
                e.preventDefault();
                showToast('Ngày kết thúc phải sau ngày bắt đầu!', 'danger');
                return;
            }
        });
    }
});

/**
 * Search Form Enhancement
 */
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        // Auto focus khi có tham số search
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search')) {
            searchInput.focus();
        }
        
        // Clear button
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.parentElement.classList.add('has-value');
            } else {
                this.parentElement.classList.remove('has-value');
            }
        });
    }
});

/**
 * Loading Indicator
 */
function showLoading() {
    const loadingHTML = `
        <div id="loading-overlay" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        ">
            <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

/**
 * Copy to Clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Đã sao chép vào clipboard!', 'success');
    }, function(err) {
        showToast('Không thể sao chép!', 'danger');
    });
}

/**
 * Smooth Scroll
 */
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && href !== '') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});
