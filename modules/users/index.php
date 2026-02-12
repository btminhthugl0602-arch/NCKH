<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: " . _HOST_URL . "?module=auth&action=login");
    exit();
}
if (isset($_POST['create_user'])) {

    global $conn;

    $tenTK = mysqli_real_escape_string($conn, $_POST['tenTK']);
    $matKhau = password_hash($_POST['matKhau'], PASSWORD_DEFAULT);
    $idLoaiTK = (int)$_POST['idLoaiTK'];

    $sql = "
        INSERT INTO taikhoan
        (tenTK, matKhau, idLoaiTK, isActive)
        VALUES
        ('$tenTK', '$matKhau', $idLoaiTK, 1)
    ";

    mysqli_query($conn, $sql);

    header("Location: ?module=users");
    exit();
}
global $conn;

$keyword = $_GET['keyword'] ?? '';

$sql = "
    SELECT tk.idTK, tk.tenTK, tk.isActive, ltk.tenLoaiTK
    FROM taikhoan tk
    LEFT JOIN loaitaikhoan ltk
        ON tk.idLoaiTK = ltk.idLoaiTK
";

if (!empty($keyword)) {
    $keyword = mysqli_real_escape_string($conn, $keyword);
    $sql .= " WHERE tk.tenTK LIKE '%$keyword%' ";
}

$sql .= " ORDER BY tk.idTK DESC";

$result = mysqli_query($conn, $sql);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
$data = [
    'page_title' => 'Quản trị người dùng'
];

$active_page = 'users';

$keyword = $_GET['keyword'] ?? '';
ob_start();
layout('header', $data);
layout('sidebar', ['active_page' => $active_page]);

?>

  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NKDMSK6" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
  <?php layout('navbar', $data); ?>  
  <div class="container-fluid py-2">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Danh sách người dùng</h6>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="row mb-3 px-3">

    <!-- Ô tìm kiếm -->
    <div class="col-md-6">
        <form method="GET" action="">
            <input type="hidden" name="module" value="users">

            <div class="input-group input-group-outline">
                <input type="text"
                       name="keyword"
                       value="<?= htmlspecialchars($keyword) ?>"
                       class="form-control"
                       placeholder="Tìm kiếm theo tên đăng nhập...">

                <button class="btn bg-gradient-dark mb-0" type="submit">
                    <i class="material-symbols-rounded">search</i>
                </button>
            </div>
        </form>
    </div>

    <!-- Nút tạo tài khoản -->
    <div class="col-md-6 text-end">
        <button class="btn bg-gradient-primary"
        data-bs-toggle="modal"
        data-bs-target="#createUserModal">
    <i class="material-symbols-rounded">person_add</i>
    Tạo tài khoản
</button>
    </div>

</div>
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tên đăng nhập</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Mã sinh viên</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Trạng thái</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vai trò</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hoạt động</th>
                    </tr>
                  </thead>
                  <tbody>
<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): ?>
        <tr>
            <td>
                <h6 class="mb-0 text-sm">
                    <?= htmlspecialchars($user['tenTK']) ?>
                </h6>
            </td>

            <td>
                <?= $user['idTK'] ?>
            </td>

            <td class="text-center">
                <?php if ($user['isActive'] == 1): ?>
                    <span class="badge bg-gradient-success">
                        Hoạt động
                    </span>
                <?php else: ?>
                    <span class="badge bg-gradient-secondary">
                        Khóa
                    </span>
                <?php endif; ?>
            </td>

            <td class="text-center">
                <?= htmlspecialchars($user['tenLoaiTK']) ?>
            </td>

            <td class="text-center">
                <a href="?module=users&action=edit&id=<?= $user['idTK'] ?>">
                    Sửa
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center">
            Không có dữ liệu
        </td>
    </tr>
<?php endif; ?>
</tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="createUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Tạo tài khoản</h5>
          <button type="button" class="btn-close"
                  data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>Tên đăng nhập</label>
            <input type="text" name="tenTK"
                   class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Mật khẩu</label>
            <input type="password" name="matKhau"
                   class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Loại tài khoản</label>
            <select name="idLoaiTK" class="form-control">
              <option value="1">Quản trị viên</option>
              <option value="2">Giảng viên</option>
              <option value="3">Sinh viên</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button"
                  class="btn bg-gradient-secondary"
                  data-bs-dismiss="modal">
            Hủy
          </button>

          <button type="submit"
                  name="create_user"
                  class="btn bg-gradient-primary">
            Lưu
          </button>
        </div>
      </form>

    </div>
  </div>
  </main>
  <!--   Core JS Files   -->
  <script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/core/popper.min.js"></script>
  <script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/core/bootstrap.min.js"></script>
  <script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="<?php echo _HOST_URL_TEMPLATES; ?>/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"version":"2024.11.0","token":"1b7cbb72744b40c580f8633c6b62637e","server_timing":{"name":{"cfCacheStatus":true,"cfEdge":true,"cfExtPri":true,"cfL4":true,"cfOrigin":true,"cfSpeedBrain":true},"location_startswith":null}}' crossorigin="anonymous"></script>

</div>
<?php layout('footer'); ?>