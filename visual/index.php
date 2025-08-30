<?php
require '../functionPHP/connect.php'; // koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // enkripsi password

    // Cek apakah username sudah ada
    $stmt = $db->prepare("SELECT * FROM data_akun WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Nama Akun Sudah Terdaftar! Silakan gunakan nama akun lain.";
    } else {
        // Simpan user baru
        $stmt = $db->prepare("INSERT INTO data_akun (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        if ($stmt->execute()) {
            header("Location: LoginPage.php?success=1");
            exit;
        } else {
            $error = "Gagal mendaftar, coba lagi!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Akun</title>
  <link rel="icon" type="image/x-icon" href="../resource/SiReGaR.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 380px; border-radius: 15px;">
            <h3 class="text-center mb-3">
  <img src="../resource/SiReGaR.png" alt="Logo" class="img-fluid" style="max-width:120px;">
</h3>
<h4 class="text-center mb-3">Website Manajemen Karyawan CV.Mahardika Tehnik Mandiri</h4>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Daftar</button>
        <div class="text-center mt-3">
          Sudah punya akun? <a href="LoginPage.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
