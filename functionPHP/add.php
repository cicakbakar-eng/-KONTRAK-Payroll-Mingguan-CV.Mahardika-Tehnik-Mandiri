<?php

include 'config.php'; // pastikan file ini punya $conn

    // query insert
    $sql = "INSERT INTO data_karyawan (id_karyawan, nama_karyawan, area_kerja, gaji_pokok) 
            VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // binding parameter
    $stmt->bind_param("sssd", $id_karyawan, $nama_karyawan, $area_kerja, $gaji_pokok);

    // eksekusi query
    if ($stmt->execute()) {
        return true; // berhasil
    } else {
        return false; // gagal
    }
?>
Cara Pakai Fungsi Ini
Misalnya dari form HTML:

php
Copy
Edit
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'tambahKaryawan.php';

    $id_karyawan   = $_POST['id_karyawan'];
    $nama_karyawan = $_POST['nama_karyawan'];
    $area_kerja    = $_POST['area_kerja'];
    $gaji_pokok    = $_POST['gaji_pokok'];

    if (tambahKaryawan($id_karyawan, $nama_karyawan, $area_kerja, $gaji_pokok)) {
        echo "<script>alert('Karyawan berhasil ditambahkan!'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan karyawan!');</script>";
    }
}
?>