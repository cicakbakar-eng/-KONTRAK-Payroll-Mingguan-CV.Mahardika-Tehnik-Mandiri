<?php
include '../functionPHP/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $area = $_POST['area'];

    $sql = "INSERT INTO data_karyawan (nama, area_kerja) VALUES (?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $nama, $area);

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
  <title>CV.MAHARDIKA TEKNIK MANDIRI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-primary text-white text-center">
          <h4 class="mb-0">CV.MAHARDIKA TEKNIK MANDIRI ADMIN BOARD</h4>
        </div>
        <div class="card-body p-4">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Area Kerja</label>
              <input type="text" name="area" class="form-control" required>
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
</div>

</body>
</html>
