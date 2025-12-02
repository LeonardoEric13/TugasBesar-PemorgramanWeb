<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

$query = "SELECT sewa.*, lapangan.nama_lapangan
          FROM sewa
          JOIN lapangan ON sewa.lapangan_id = lapangan.id
          WHERE user_id = '$uid'
          ORDER BY sewa.tanggal_main DESC";

$result = mysqli_query($conn, $query);
$riwayat_list = array();

while ($row = mysqli_fetch_assoc($result)) {
    $riwayat_list[] = $row;
}

$page_title = "Riwayat Booking";

include '../templates/pages/riwayat.html';
?>
