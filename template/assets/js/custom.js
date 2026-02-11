/**
 * Custom JavaScript for Event Management System
 */

document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initFormValidation();
    initTooltips();
    initSmoothScroll();
    handleInputGroupLabels();
    scheduleAlertAutoDismiss();
});

/* =========================================
   SIDEBAR TOGGLE
   ========================================= */
function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const body = document.body;
    const sidenav = document.getElementById('sidenav-main');
    const isDesktop = () => window.innerWidth >= 1200;

    // Create mobile overlay
    let overlay = document.getElementById('sidebarOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sidebarOverlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }

    // Determine initial state
    if (isDesktop()) {
        // Desktop: sidebar visible by default
        body.classList.add('g-sidenav-show');
        body.classList.remove('g-sidenav-hidden');
    } else {
        // Mobile: sidebar hidden by default
        body.classList.remove('g-sidenav-show');
        body.classList.add('g-sidenav-hidden');
    }

    // Toggle on button click
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Close on overlay click (mobile)
    overlay.addEventListener('click', function () {
        if (!isDesktop()) {
            hideSidebar();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function () {
        if (isDesktop()) {
            overlay.classList.remove('active');
            // Restore to show on desktop resize
            if (!body.classList.contains('g-sidenav-show') && !body.classList.contains('g-sidenav-hidden')) {
                body.classList.add('g-sidenav-show');
            }
        } else {
            if (body.classList.contains('g-sidenav-show')) {
                overlay.classList.add('active');
            }
        }
    });

    function toggleSidebar() {
        if (body.classList.contains('g-sidenav-show')) {
            hideSidebar();
        } else {
            showSidebar();
        }
    }

    function showSidebar() {
        body.classList.add('g-sidenav-show');
        body.classList.remove('g-sidenav-hidden');
        if (!isDesktop()) {
            overlay.classList.add('active');
        }
        // Animate hamburger to X
        animateToggleBtn(true);
    }

    function hideSidebar() {
        body.classList.remove('g-sidenav-show');
        body.classList.add('g-sidenav-hidden');
        overlay.classList.remove('active');
        // Animate X back to hamburger
        animateToggleBtn(false);
    }

    function animateToggleBtn(isOpen) {
        if (!toggleBtn) return;
        const lines = toggleBtn.querySelectorAll('.sidenav-toggler-line');
        if (lines.length < 3) return;
        if (isOpen) {
            // Transform to X
            lines[0].style.transform = 'translateY(6px) rotate(45deg)';
            lines[1].style.opacity = '0';
            lines[2].style.transform = 'translateY(-6px) rotate(-45deg)';
        } else {
            // Reset to hamburger
            lines[0].style.transform = '';
            lines[1].style.opacity = '';
            lines[2].style.transform = '';
        }
    }
}

/* =========================================
   FORM VALIDATION
   ========================================= */
function initFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

/* =========================================
   TOOLTIPS
   ========================================= */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/* =========================================
   SMOOTH SCROLL
   ========================================= */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
}

/* =========================================
   INPUT GROUP LABELS
   ========================================= */
function handleInputGroupLabels() {
    document.querySelectorAll('.input-group-outline input, .input-group-outline textarea').forEach(input => {
        if (input.value !== '') {
            input.parentElement.classList.add('is-filled');
        }
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('is-focused');
        });
        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('is-focused');
            if (this.value !== '') {
                this.parentElement.classList.add('is-filled');
            } else {
                this.parentElement.classList.remove('is-filled');
            }
        });
    });
}

/* =========================================
   AUTO-DISMISS ALERTS
   ========================================= */
function scheduleAlertAutoDismiss() {
    setTimeout(function () {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
}

/* =========================================
   RESPONSIVE TABLES
   ========================================= */
function handleResponsiveTables() {
    const tables = document.querySelectorAll('table:not(.table-responsive table)');
    tables.forEach(table => {
        if (!table.parentElement.classList.contains('table-responsive')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'table-responsive';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        }
    });
}
handleResponsiveTables();

/* =========================================
   UTILITY FUNCTIONS (exported globally)
   ========================================= */
function showLoading(button) {
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="loading"></span> Đang xử lý...';
    return function hideLoading() {
        button.disabled = false;
        button.innerHTML = originalText;
    };
}

function showToast(message, type = 'success') {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 3000 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}

function confirmAction(message, callback) {
    if (confirm(message)) callback();
}

function formatNumber(number) {
    return new Intl.NumberFormat('vi-VN').format(number);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('vi-VN').format(new Date(date));
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function ajax(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    };
    return fetch(url, { ...defaultOptions, ...options })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Đã xảy ra lỗi. Vui lòng thử lại!', 'danger');
            throw error;
        });
}

window.showLoading = showLoading;
window.showToast = showToast;
window.confirmAction = confirmAction;
window.formatNumber = formatNumber;
window.formatDate = formatDate;
window.ajax = ajax;
