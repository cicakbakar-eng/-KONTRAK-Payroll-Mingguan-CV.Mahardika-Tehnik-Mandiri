<?php
require 'koneksi.php'; // koneksi DB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'];
    $nama_karyawan = $_POST['nama_karyawan'];
    $area_kerja = $_POST['area_kerja'];
    $gaji_pokok = $_POST['gaji_pokok'];

    $sql = "UPDATE data_karyawan 
            SET nama_karyawan = ?, area_kerja = ?, gaji_pokok = ?
            WHERE id_karyawan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nama_karyawan, $area_kerja, $gaji_pokok, $id_karyawan);

    if ($stmt->execute()) {
        echo "<script>alert('Data karyawan berhasil diupdate!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data karyawan!'); window.location='index.php';</script>";
    }
}
?>
