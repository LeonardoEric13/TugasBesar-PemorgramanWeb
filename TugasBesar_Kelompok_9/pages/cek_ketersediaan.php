<?php
session_start();
require_once '../config/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login'])) {
    echo json_encode(['error' => 'Unauthorized', 'tersedia' => 0]);
    exit();
}

define('MAX_LAPANGAN_PER_TIPE', 4);

$lapangan_id = mysqli_real_escape_string($conn, $_POST['lapangan'] ?? '');
$tanggal = mysqli_real_escape_string($conn, $_POST['tanggal'] ?? '');
$jam = mysqli_real_escape_string($conn, $_POST['jam'] ?? '');
$durasi = (int)($_POST['durasi'] ?? 1);
$exclude_id = mysqli_real_escape_string($conn, $_POST['exclude_id'] ?? '0');

if (empty($lapangan_id) || empty($tanggal) || empty($jam)) {
    echo json_encode(['error' => 'Parameter tidak lengkap', 'tersedia' => 0]);
    exit();
}

$jam_mulai_int = (int)substr($jam, 0, 2);
$jam_selesai_int = $jam_mulai_int + $durasi;

$exclude_clause = ($exclude_id != '0') ? "AND id != '$exclude_id'" : "";

$query = "SELECT nomor_lapangan, jam_mulai, durasi_jam FROM sewa 
          WHERE lapangan_id = '$lapangan_id' 
          AND tanggal_main = '$tanggal'
          AND keterangan_harga != 'Dibatalkan'
          $exclude_clause";
$result = mysqli_query($conn, $query);

$nomor_terpakai = array();

while ($row = mysqli_fetch_assoc($result)) {
    $booking_jam_mulai = (int)substr($row['jam_mulai'], 0, 2);
    $booking_jam_selesai = $booking_jam_mulai + (int)$row['durasi_jam'];
    
    if ($jam_mulai_int < $booking_jam_selesai && $booking_jam_mulai < $jam_selesai_int) {
        $nomor_terpakai[] = $row['nomor_lapangan'];
    }
}

$jumlah_tersedia = MAX_LAPANGAN_PER_TIPE - count(array_unique($nomor_terpakai));

echo json_encode([
    'tersedia' => $jumlah_tersedia,
    'terpakai' => count(array_unique($nomor_terpakai)),
    'total' => MAX_LAPANGAN_PER_TIPE
]);
?>