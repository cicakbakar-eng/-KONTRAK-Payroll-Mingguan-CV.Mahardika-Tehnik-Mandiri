<?php
session_start();
require '../functionPHP/connect.php'; // koneksi ke database

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ambil data user dari database
    $stmt = $db->prepare("SELECT * FROM data_akun WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Validasi username & password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        header("Location: DASHBOARD.php"); // arahkan ke halaman payroll / dashboard
        exit;
    } else {
        $error = "password salah <br> HAYO LUPA PASSWORD.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="../resource/SiReGaR.png">
</head>
<body class="bg-light">
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-lg p-4" style="width: 350px; border-radius: 15px;">
      <h3 class="text-center mb-3">
  <img src="../resource/SiReGaR.png" alt="Logo" class="img-fluid" style="max-width:120px;">
</h3>
<h4 class="text-center mb-3">Gunakan Akun dan Password yang telah terdaftar</h4>
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
        <button type="submit" class="btn btn-primary w-100">Login</button>
        <div class="text-center mt-3">
          belum punya akun admin? <a href="index.php">Daftar</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
