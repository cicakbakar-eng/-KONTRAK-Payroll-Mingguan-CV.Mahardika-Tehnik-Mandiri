<?php
require '../vendor/autoload.php';
include '../functionPHP/connect.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$tanggal_awal  = $_GET['tanggal_awal'] ?? date('Y-m-d', strtotime('monday this week'));
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));

$periode = new DatePeriod(
    new DateTime($tanggal_awal),
    new DateInterval('P1D'),
    (new DateTime($tanggal_akhir))->modify('+1 day')
);
$periodeArr = iterator_to_array($periode);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// layout: 3 kolom kiri (No, Nama, Area Kerja) + (tanggal * 2) + 2 kolom TOTAL (Kerja, Lmbr)
$leftCols = 3;
$totalCols = $leftCols + count($periodeArr) * 2 + 2;
$lastCol = Coordinate::stringFromColumnIndex($totalCols);

// Judul utama
$sheet->mergeCells("A1:{$lastCol}1");
$sheet->setCellValue("A1", "ABSENSI KARYAWAN CV MAHARDIKA TEHNIK MANDIRI");
$sheet->getStyle("A1")->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);

// Header baris 2-3: No | Nama | Area Kerja  (merge 2 baris)
$sheet->mergeCells("A2:A3");
$sheet->setCellValue("A2", "No");
$sheet->mergeCells("B2:B3");
$sheet->setCellValue("B2", "Nama");
$sheet->mergeCells("C2:C3");
$sheet->setCellValue("C2", "Area Kerja");

// tanggal mulai dari kolom ke-4
$colIndex = $leftCols + 1; // 4
foreach ($periodeArr as $tgl) {
    $colLetter1 = Coordinate::stringFromColumnIndex($colIndex);
    $colLetter2 = Coordinate::stringFromColumnIndex($colIndex + 1);
    $sheet->mergeCells("{$colLetter1}2:{$colLetter2}2");
    $sheet->setCellValue("{$colLetter1}2", $tgl->format('d/m/Y'));
    $sheet->setCellValue("{$colLetter1}3", "KERJA");
    $sheet->setCellValue("{$colLetter2}3", "LMBR");
    $colIndex += 2;
}

// kolom TOTAL di paling kanan
$colLetterTotalKerja  = Coordinate::stringFromColumnIndex($colIndex);
$colLetterTotalLembur = Coordinate::stringFromColumnIndex($colIndex + 1);
$sheet->mergeCells("{$colLetterTotalKerja}2:{$colLetterTotalLembur}2");
$sheet->setCellValue("{$colLetterTotalKerja}2", "TOTAL");
$sheet->setCellValue("{$colLetterTotalKerja}3", "KERJA");
$sheet->setCellValue("{$colLetterTotalLembur}3", "LMBR");

// ambil daftar karyawan
$karyawan = $db->query("SELECT id, nama, area_kerja FROM data_karyawan ORDER BY area_kerja, nama")->fetch_all(MYSQLI_ASSOC);

// persiapkan subtotal per tanggal
$subtotal = [];
foreach ($periodeArr as $tgl) {
    $subtotal[$tgl->format('Y-m-d')] = ['kerja' => 0, 'lembur' => 0];
}

// isi data mulai baris 4
$row = 4;
$no = 1;
foreach ($karyawan as $k) {
    $sheet->setCellValueByColumnAndRow(1, $row, $no++);
    $sheet->setCellValueByColumnAndRow(2, $row, $k['nama']);
    $sheet->setCellValueByColumnAndRow(3, $row, $k['area_kerja']);

    $colIndex = $leftCols + 1;
    $totalKerja = 0;
    $totalLembur = 0;

    foreach ($periodeArr as $tgl) {
        $date = $tgl->format('Y-m-d');
        $cek = $db->query("SELECT kerja, lembur FROM data_absensi WHERE karyawan_id={$k['id']} AND tanggal='$date'")->fetch_assoc();
        $kerja = isset($cek['kerja']) ? (float)$cek['kerja'] : 0;
        $lembur = isset($cek['lembur']) ? (float)$cek['lembur'] : 0;

        $sheet->setCellValueByColumnAndRow($colIndex, $row, $kerja);
        $sheet->setCellValueByColumnAndRow($colIndex + 1, $row, $lembur);

        $subtotal[$date]['kerja'] += $kerja;
        $subtotal[$date]['lembur'] += $lembur;

        $totalKerja += $kerja;
        $totalLembur += $lembur;

        $colIndex += 2;
    }

    // tulis total per karyawan
    $sheet->setCellValue("{$colLetterTotalKerja}{$row}", $totalKerja);
    $sheet->setCellValue("{$colLetterTotalLembur}{$row}", $totalLembur);

    $row++;
}

// baris subtotal
$sheet->mergeCells("A{$row}:C{$row}");
$sheet->setCellValue("A{$row}", "SUB TOTAL");

$colIndex = $leftCols + 1;
foreach ($periodeArr as $tgl) {
    $date = $tgl->format('Y-m-d');
    $colLetter1 = Coordinate::stringFromColumnIndex($colIndex);
    $colLetter2 = Coordinate::stringFromColumnIndex($colIndex + 1);
    $sheet->setCellValue("{$colLetter1}{$row}", $subtotal[$date]['kerja']);
    $sheet->setCellValue("{$colLetter2}{$row}", $subtotal[$date]['lembur']);
    $colIndex += 2;
}

// total akhir semua kerja/lembur
$totalKerjaAll  = array_sum(array_column($subtotal, 'kerja'));
$totalLemburAll = array_sum(array_column($subtotal, 'lembur'));
$sheet->setCellValue("{$colLetterTotalKerja}{$row}", $totalKerjaAll);
$sheet->setCellValue("{$colLetterTotalLembur}{$row}", $totalLemburAll);

// STYLING
// header baris 2-3 (kuning)
$sheet->getStyle("A2:{$lastCol}3")->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFC000']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
]);

// judul baris 1
$sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
    'font' => ['bold' => true, 'size' => 12],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);

// isi data border + center (kecuali kolom Nama & Area yang align left)
$sheet->getStyle("A4:{$lastCol}" . ($row - 1))->applyFromArray([
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
]);

// Nama & Area kerjakan rata kiri
$sheet->getStyle("B4:B" . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("C4:C" . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// subtotal styling
$sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']]],
]);

// row height agar terlihat mirip
$sheet->getRowDimension(1)->setRowHeight(24);
$sheet->getRowDimension(2)->setRowHeight(20);
$sheet->getRowDimension(3)->setRowHeight(20);
$sheet->getRowDimension($row)->setRowHeight(20);

// nomor/angka format (satu desimal agar tampil mirip "1,0" / "1.0" tergantung locale)
for ($c = $leftCols + 1; $c <= $totalCols; $c++) {
    $colLetter = Coordinate::stringFromColumnIndex($c);
    $sheet->getStyle("{$colLetter}4:{$colLetter}" . ($row))->getNumberFormat()->setFormatCode('0.0');
}

// auto width
for ($c = 1; $c <= $totalCols; $c++) {
    $colName = Coordinate::stringFromColumnIndex($c);
    $sheet->getColumnDimension($colName)->setAutoSize(true);
}

// export
$writer = new Xlsx($spreadsheet);
$filename = "Absensi_{$tanggal_awal}_sampai_{$tanggal_akhir}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="'.$filename.'"');
$writer->save("php://output");
exit;
