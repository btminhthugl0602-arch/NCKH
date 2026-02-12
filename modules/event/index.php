<?php
if (!defined('_AUTHEN')) {
    die('Truy cập không hợp lệ');
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: " . _HOST_URL . "?module=auth&action=login");
    exit();
}

$data = [
    'page_title' => 'Quản lý sự kiện'
];

$active_page = 'event';
if (isset($_POST['create_event'])) {

    $event_name = trim($_POST['event_name']);

    if (!empty($event_name)) {

        $event_name = mysqli_real_escape_string($conn, $event_name);

        $sql = "INSERT INTO sukien 
        (tenSK, nguoiTao, ngayMoDangKy, ngayDongDangKy, ngayBatDau, isActive)
        VALUES 
        ('$event_name', 
        ".$_SESSION['user_id'].", 
        NOW(), 
        NOW(), 
        NOW(), 
        1)";
        mysqli_query($conn, $sql);

        $new_event_id = mysqli_insert_id($conn);

        header("Location: " . _HOST_URL . "?module=event");
exit();
    }
}
layout('header', $data);
layout('sidebar', $data);
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

<?php layout('navbar', $data); ?>

<div class="container-fluid py-4">

    <!-- Header + Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Quản lý sự kiện</h4>

        <button class="btn bg-gradient-dark" 
        data-bs-toggle="modal" 
        data-bs-target="#createEventModal">
    <i class="material-symbols-rounded">add</i>
    Tạo sự kiện
</button>
    </div>

    <!-- Search -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET">
                <input type="hidden" name="module" value="event">
                <input type="hidden" name="action" value="index">

                <div class="input-group input-group-outline">
                    <label class="form-label">Tìm kiếm sự kiện...</label>
                    <input type="text" name="keyword" class="form-control"
                        value="<?= isset($_GET['keyword']) ? $_GET['keyword'] : '' ?>">
                </div>
            </form>
        </div>
    </div>

    <!-- Event List -->
    <div class="row">

        <?php
        // Demo dữ liệu (sau này lấy từ database)
        $events = [];

$sql = "SELECT * FROM sukien ORDER BY idSK DESC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $events[] = $row;
}

        foreach ($events as $event):
        ?>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="mb-2"><?= $event['tenSK'] ?></h6>

<p class="text-sm text-muted mb-3">
    Ngày bắt đầu: <?= !empty($event['ngayBatDau']) 
    ? date('d/m/Y', strtotime($event['ngayBatDau'])) 
    : 'Chưa cấu hình'; ?>
</p>

<a href="<?= _HOST_URL ?>?module=event&action=edit&id=<?= $event['idSK'] ?>" 
   class="btn btn-sm bg-gradient-info w-100">
    Xem chi tiết
</a>
                </div>
            </div>
        </div>

        <?php endforeach; ?>

    </div>

</div>
</main>
<!-- Modal Create Event -->
<div class="modal fade" id="createEventModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST">

        <div class="modal-header">
          <h5 class="modal-title">Tạo sự kiện mới</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="input-group input-group-outline">
            <label class="form-label">Tên sự kiện</label>
            <input type="text" name="event_name" class="form-control" required>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Hủy
          </button>

          <button type="submit" name="create_event" class="btn bg-gradient-dark">
            Tạo
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
<!-- Chart Scripts -->
<script src="<?= _HOST_URL_TEMPLATES ?>/assets/js/plugins/chartjs.min.js"></script>
<script>
  var ctx = document.getElementById("chart-bars").getContext("2d");

  new Chart(ctx, {
    type: "bar",
    data: {
      labels: ["M", "T", "W", "T", "F", "S", "S"],
      datasets: [{
        label: "Views",
        tension: 0.4,
        borderWidth: 0,
        borderRadius: 4,
        borderSkipped: false,
        backgroundColor: "#43A047",
        data: [50, 45, 22, 28, 50, 60, 76],
        barThickness: 'flex'
      }, ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [5, 5],
            color: '#e5e5e5'
          },
          ticks: {
            suggestedMin: 0,
            suggestedMax: 500,
            beginAtZero: true,
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2
            },
            color: "#737373"
          },
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            color: '#737373',
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2
            },
          }
        },
      },
    },
  });


  var ctx2 = document.getElementById("chart-line").getContext("2d");

  new Chart(ctx2, {
    type: "line",
    data: {
      labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
      datasets: [{
        label: "Sales",
        tension: 0,
        borderWidth: 2,
        pointRadius: 3,
        pointBackgroundColor: "#43A047",
        pointBorderColor: "transparent",
        borderColor: "#43A047",
        backgroundColor: "transparent",
        fill: true,
        data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220],
        maxBarThickness: 6

      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          callbacks: {
            title: function(context) {
              const fullMonths = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
              ];
              return fullMonths[context[0].dataIndex];
            }
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [4, 4],
            color: '#e5e5e5'
          },
          ticks: {
            display: true,
            color: '#737373',
            padding: 10,
            font: {
              size: 12,
              lineHeight: 2
            },
          }
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [5, 5]
          },
          ticks: {
            display: true,
            color: '#737373',
            padding: 10,
            font: {
              size: 12,
              lineHeight: 2
            },
          }
        },
      },
    },
  });

  var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

  new Chart(ctx3, {
    type: "line",
    data: {
      labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
      datasets: [{
        label: "Tasks",
        tension: 0,
        borderWidth: 2,
        pointRadius: 3,
        pointBackgroundColor: "#43A047",
        pointBorderColor: "transparent",
        borderColor: "#43A047",
        backgroundColor: "transparent",
        fill: true,
        data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
        maxBarThickness: 6

      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      interaction: {
        intersect: false,
        mode: 'index',
      },
      scales: {
        y: {
          grid: {
            drawBorder: false,
            display: true,
            drawOnChartArea: true,
            drawTicks: false,
            borderDash: [4, 4],
            color: '#e5e5e5'
          },
          ticks: {
            display: true,
            padding: 10,
            color: '#737373',
            font: {
              size: 14,
              lineHeight: 2
            },
          }
        },
        x: {
          grid: {
            drawBorder: false,
            display: false,
            drawOnChartArea: false,
            drawTicks: false,
            borderDash: [4, 4]
          },
          ticks: {
            display: true,
            color: '#737373',
            padding: 10,
            font: {
              size: 14,
              lineHeight: 2
            },
          }
        },
      },
    },
  });
</script>
<?php layout('footer'); ?>

