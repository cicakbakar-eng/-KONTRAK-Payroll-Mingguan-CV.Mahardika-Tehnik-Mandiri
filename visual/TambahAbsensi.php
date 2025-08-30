<?php
include '../functionPHP/connect.php'; // koneksi pakai $db

// Ambil daftar karyawan untuk dropdown
$sql = "SELECT id, nama, area_kerja FROM data_karyawan";
$karyawan = $db->query($sql);

// Proses simpan absensi baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_POST['karyawan_id'];
    $tanggal     = $_POST['tanggal'];
    $kerja       = $_POST['kerja'];
    $lembur      = $_POST['lembur'];

    $stmt = $db->prepare("INSERT INTO data_absensi (karyawan_id, tanggal, kerja, lembur) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $karyawan_id, $tanggal, $kerja, $lembur);

    if ($stmt->execute()) {
        header("Location: DASHBOARD.php?status=success");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>❌ Error: " . $db->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Absensi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      font-family: "Segoe UI", sans-serif;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0px 4px 20px rgba(0,0,0,0.1);
    }
    .btn-primary {
      background-color: #4a69bd;
      border: none;
    }
    .btn-primary:hover {
      background-color: #1e3799;
    }
    .form-label {
      font-weight: 600;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card p-4">
          <h3 class="text-center mb-4">📝 Tambah Absensi</h3>

          <?php if (!empty($pesan)) echo $pesan; ?>

          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Karyawan</label>
              <select name="karyawan_id" class="form-select" required>
                <option value="">-- pilih karyawan --</option>
                <?php while ($row = $karyawan->fetch_assoc()): ?>
                  <option value="<?= $row['id']; ?>">
                    <?= $row['nama']; ?> (<?= $row['area_kerja']; ?>)
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Kerja (jam)</label>
              <input type="number" name="kerja" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Lembur (jam)</label>
              <input type="number" name="lembur" class="form-control" min="0" required>
            </div>

            <div class="d-flex justify-content-between">
              <a href="DASHBOARD.php" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
