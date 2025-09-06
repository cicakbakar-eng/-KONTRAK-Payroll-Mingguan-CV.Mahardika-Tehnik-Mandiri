<?php
include '../functionPHP/connect.php';

// Ambil tanggal awal dari POST (default senin minggu ini)
$tanggal_awal = $_POST['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week'));
$tanggal_akhir = date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));

// Tampil Periode Tanggal
$periode = new DatePeriod(
    new DateTime($tanggal_awal),
    new DateInterval('P1D'),
    (new DateTime($tanggal_akhir))->modify('+1 day')
);

$periodeArr = iterator_to_array($periode);

// Ambil data karyawan
$karyawan = $db->query("SELECT * FROM data_karyawan ORDER BY area_kerja, nama")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Mingguan</title>
  <link rel="icon" type="image/x-icon" href="../resource/SiReGaR.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; font-size: 14px; }
    .table thead th { background: #ffc107; text-align: center; border: 1px solid #000; }
    .table tbody td { text-align: center; border: 1px solid #000; }
    .sub-total { background: #ffc107; font-weight: bold; }
    .sidebar { height: 100vh; background: #343a40; padding-top: 20px; position: fixed; top: 0; left: 0; width: 200px; }
    .sidebar a { display: block; color: #fff; padding: 12px 20px; text-decoration: none; margin-bottom: 5px; border-radius: 5px; }
    .sidebar a:hover { background: #495057; }
    .content { margin-left: 220px; padding: 20px; }
    input.absensi-input { width: 60px; text-align: center; }
  </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
  <div class="logo text-center mb-4">
    <img src="../resource/SiReGaR.png" alt="Logo" style="width:100px; border-radius:10px;">
    <h5 class="text-white mt-2">CV.MAHARDIKA TEKNIK MANDIRI</h5>
  </div>
  <a href="TambahKaryawan.php">Tambah Karyawan</a>
  <a href="UpdateKaryawan.php">Data Manajemen Karyawan</a>
  <a href="UpdateKaryawan.php">Absensi YASIR</a>
  <a href="UpdateKaryawan.php">Detail Gaji & Potongan Karyawan</a>
  <a href="UpdateKaryawan.php">Data KASBON Karyawan</a>
  <a href="UpdateKaryawan.php">Slip Gaji Karyawan</a>
</div>

<div class="content">
  <h5 class="text-center mb-3">
    ABSENSI KARYAWAN MINGGUAN CV. MAHARDIKA TEHNIK MANDIRI<br>
    <small>Periode <?= date("d M Y", strtotime($tanggal_awal)) ?> - <?= date("d M Y", strtotime($tanggal_akhir)) ?></small>
  </h5>

  <form method="POST" class="mb-3 text-center">
    <label for="tanggal_awal">Pilih Tanggal Awal:</label>
    <input type="date" name="tanggal_awal" value="<?= $tanggal_awal ?>" required>
    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th colspan="3" rowspan="2">ABSENSI KARYAWAN CV MAHARDIKA TEHNIK MANDIRI</th>
          <th colspan="<?= count($periodeArr)*2 ?>">MINGGUAN</th>
        </tr>
        <tr>
          <?php foreach ($periodeArr as $tgl): ?>
            <th colspan="2"><?= $tgl->format("d/m/Y") ?></th>
          <?php endforeach; ?>
        </tr>
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Area Kerja</th>
          <?php foreach ($periodeArr as $tgl): ?>
            <th>KERJA</th>
            <th>LMBR</th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php $no=1; foreach ($karyawan as $k): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= $k['nama'] ?></td>
            <td><?= $k['area_kerja'] ?></td>
            <?php foreach ($periodeArr as $tgl): 
              $date = $tgl->format("Y-m-d");
              $cek = $db->query("SELECT kerja, lembur FROM data_absensi WHERE karyawan_id={$k['id']} AND tanggal='$date'")->fetch_assoc();
              $kerja = $cek['kerja'] ?? 0;
              $lembur = $cek['lembur'] ?? 0;
            ?>
              <td>
                <input type="number" value="<?= $kerja ?>" class="absensi-input"
                       data-karyawan="<?= $k['id'] ?>" data-tanggal="<?= $date ?>" data-field="kerja">
              </td>
              <td>
                <input type="number" value="<?= $lembur ?>" class="absensi-input"
                       data-karyawan="<?= $k['id'] ?>" data-tanggal="<?= $date ?>" data-field="lembur">
              </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>

        <tr class="sub-total">
          <td colspan="3">SUB TOTAL</td>
          <?php foreach ($periodeArr as $tgl): 
            $date = $tgl->format("Y-m-d"); ?>
            <td class="subtotal-kerja" data-tanggal="<?= $date ?>">0</td>
            <td class="subtotal-lembur" data-tanggal="<?= $date ?>">0</td>
          <?php endforeach; ?>
        </tr>
      </tbody>
    </table>

    <!-- Export Button -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body">
        <h5 class="card-title mb-3">Export Data Absensi</h5>
        <form method="GET" action="exporttoexcel.php" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="<?= $tanggal_awal ?>" required>
          </div>
          <div class="col-md-4">
            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?= $tanggal_akhir ?>" required>
          </div>
          <div class="col-md-4">
            <button class="btn btn-success w-100" type="submit">Export Excel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Login -->
<div class="modal fade" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Login Admin</h5>
      </div>
      <div class="modal-body">
        <div id="login-error" class="alert alert-danger d-none"></div>
        <form id="loginForm">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).on("change", ".absensi-input", function() {
  let data = {
    karyawan_id: $(this).data("karyawan"),
    tanggal: $(this).data("tanggal"),
    field: $(this).data("field"),
    value: parseFloat($(this).val()) || 0
  };

  $.post("UpdateAbsensi.php", data, function(res){
    console.log("Update sukses:", res);
  });

  hitungSubtotal();
});

function hitungSubtotal() {
  let subtotalKerja = {}, subtotalLembur = {};

  $(".absensi-input").each(function() {
    let tgl = $(this).data("tanggal");
    let field = $(this).data("field");
    let val = parseFloat($(this).val()) || 0;

    if (field === "kerja") {
      subtotalKerja[tgl] = (subtotalKerja[tgl] || 0) + val;
    } else {
      subtotalLembur[tgl] = (subtotalLembur[tgl] || 0) + val;
    }
  });

  $(".subtotal-kerja").each(function() {
    let tgl = $(this).data("tanggal");
    $(this).text(subtotalKerja[tgl] || 0);
  });

  $(".subtotal-lembur").each(function() {
    let tgl = $(this).data("tanggal");
    $(this).text(subtotalLembur[tgl] || 0);
  });
}

$(document).ready(function(){
  hitungSubtotal();
  $("#loginModal").modal("show");

  const adminUser = "ADMINMAHARDIKA";
  const adminPass = "mahardikaTehnikmandiri";

  $("#loginForm").on("submit", function(e) {
    e.preventDefault();
    let user = $("input[name='username']").val();
    let pass = $("input[name='password']").val();

    if (user === adminUser && pass === adminPass) {
      $("#loginModal").modal("hide");
    } else {
      $("#login-error").removeClass("d-none").text("Username atau Password salah!");
    }
  });
});
</script>
</body>
</html>
