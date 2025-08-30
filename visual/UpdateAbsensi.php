<?php
include '../functionPHP/connect.php';

$karyawan_id = intval($_POST['karyawan_id']);
$tanggal     = $_POST['tanggal'];
$field       = $_POST['field']; // kerja / lembur
$value       = floatval($_POST['value']);

// validasi field
if (!in_array($field, ['kerja','lembur'])) {
    die("invalid field");
}

// cek data absensi
$cek = $db->query("SELECT id FROM data_absensi WHERE karyawan_id=$karyawan_id AND tanggal='$tanggal'")->fetch_assoc();

if ($cek) {
    $db->query("UPDATE data_absensi SET $field=$value WHERE karyawan_id=$karyawan_id AND tanggal='$tanggal'");
} else {
    $db->query("INSERT INTO data_absensi (karyawan_id, tanggal, $field) VALUES ($karyawan_id, '$tanggal', $value)");
}

echo "success";
