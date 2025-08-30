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
        header("Location: ".$_SERVER['PHP_SELF']."?updated=1");
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
        header("Location: ".$_SERVER['PHP_SELF']."?deleted=1");
        exit;
    } else {
        echo "Error: " . $db->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit & Hapus Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2 class="mb-4">Manajemen Data Karyawan</h2>

<?php if (isset($_GET['updated'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  Data berhasil diupdate!
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  Data berhasil dihapus!
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Area Kerja</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $karyawan->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['nama']; ?></td>
            <td><?= $row['area_kerja']; ?></td>
            <td>
                <!-- Tombol Edit -->
                <button class="btn btn-sm btn-warning" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editModal<?= $row['id']; ?>">
                    Edit
                </button>

                <!-- Tombol Hapus -->
                <button class="btn btn-sm btn-danger" 
                        data-bs-toggle="modal" 
                        data-bs-target="#deleteModal<?= $row['id']; ?>">
                    Hapus
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
<button class="btn btn-secondary mb-4" onclick="window.location.href='DASHBOARD.php'">Kembali ke Dashboard</button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
