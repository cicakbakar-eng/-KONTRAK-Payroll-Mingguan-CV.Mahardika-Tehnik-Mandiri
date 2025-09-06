<?php
include '../functionPHP/connect.php';

// Ambil semua data karyawan
$karyawan = $db->query("SELECT * FROM data_karyawan");

// Kalau form edit disubmit
if (isset($_POST['update'])) {
    $id   = $_POST['id'];
    $nama = $_POST['nama'];
    $area = $_POST['area'];

    $sql = "UPDATE data_karyawan SET nama=?, area_kerja=? WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssi", $nama, $area, $id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?updated=1");
        exit;
    } else {
        echo "Error: " . $db->error;
    }
}

// Kalau form delete disubmit
if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM data_karyawan WHERE id=?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
        exit;
    } else {
        echo "Error: " . $db->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Data Karyawan</title>
  <link rel="icon" type="image/x-icon" href="../resource/SiReGaR.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; font-size: 14px; }
    .sidebar {
      height: 100vh;
      background: #343a40;
      padding-top: 20px;
      position: fixed;
      top: 0;
      left: 0;
      width: 200px;
    }
    .sidebar a {
      display: block;
      color: #fff;
      padding: 12px 20px;
      text-decoration: none;
      margin-bottom: 5px;
      border-radius: 5px;
    }
    .sidebar a:hover { background: #495057; }
    .content {
      margin-left: 220px;
      padding: 20px;
    }
    .table {
      border-radius: 10px;
      overflow: hidden;
    }
    thead th {
      background: linear-gradient(135deg, #ffee00ff, #ffee00ff);
      color: #fff;
      text-transform: uppercase;
      font-size: 13px;
      letter-spacing: .5px;
    }
    tbody tr:hover {
      background: #f8f9fa !important;
      transition: 0.2s;
    }
    .btn {
      font-size: 13px;
      padding: 4px 8px;
    }
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
    <a href="UpdateKaryawan.php" class="bg-primary">Data Manajemen Karyawan</a>
    <a href="UpdateKaryawan.php">Absensi YASIR</a>
    <a href="UpdateKaryawan.php">Detail Gaji & Potongan Karyawan</a>
    <a href="UpdateKaryawan.php">Data KASBON Karyawan</a>
    <a href="UpdateKaryawan.php">Slip Gaji Karyawan</a>
    <a href="DASHBOARD.php">Dashboard Admin</a>
    <a href="#" id="logoutLink" class="text-danger">Logout</a>
  </div>

  <!-- Konten -->
  <div class="content">
    <h2 class="mb-4 fw-bold">Manajemen Data Karyawan</h2>

    <?php if (isset($_GET['updated'])): ?>
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        ✅ Data berhasil diupdate!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        🗑️ Data berhasil dihapus!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow-sm">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="text-center">
            <tr>
              <th style="width: 70px;">ID</th>
              <th>Nama Karyawan</th>
              <th>Area Kerja</th>
              <th style="width: 160px;">Aksi</th>
            </tr>
          </thead>
          <tbody class="table-light">
            <?php while ($row = $karyawan->fetch_assoc()): ?>
              <tr>
                <td class="text-center fw-bold"><?= $row['id']; ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['area_kerja']; ?></td>
                <td class="text-center">
                  <!-- Tombol Edit -->
                  <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">
                    <i class="bi bi-pencil-square"></i> Edit
                  </button>
                  <!-- Tombol Hapus -->
                  <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id']; ?>">
                    <i class="bi bi-trash3"></i> Hapus
                  </button>
                </td>
              </tr>

              <!-- Modal Edit -->
              <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <div class="mb-3">
                          <label class="form-label">Nama:</label>
                          <input type="text" name="nama" value="<?= $row['nama']; ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Area Kerja:</label>
                          <input type="text" name="area" value="<?= $row['area_kerja']; ?>" class="form-control" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="update" class="btn btn-success">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Modal Delete -->
              <div class="modal fade" id="deleteModal<?= $row['id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <p>Apakah Anda yakin ingin menghapus data karyawan <strong><?= $row['nama']; ?></strong> dari area <strong><?= $row['area_kerja']; ?></strong>?</p>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" name="delete" class="btn btn-danger">Ya, Hapus</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $("#logoutLink").on("click", function(e) {
      e.preventDefault();
      localStorage.removeItem("isLoggedIn");
      alert("Anda sudah logout. Silakan refresh untuk login kembali.");
    });
  </script>
</body>
</html>
