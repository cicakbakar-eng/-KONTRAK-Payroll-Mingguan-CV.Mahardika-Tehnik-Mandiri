<?php
require 'koneksi.php'; // koneksi DB

if (isset($_GET['id'])) {
    $id_karyawan = $_GET['id'];

    $sql = "DELETE FROM data_karyawan WHERE id_karyawan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_karyawan);

    if ($stmt->execute()) {
        echo "<script>alert('Data karyawan berhasil dihapus!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data karyawan!'); window.location='index.php';</script>";
    }
}
?>
