<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_sewa = $_GET['id'];
$uid = $_SESSION['uid'];

// LOGIC 1: READ Data Lama
$q_data = mysqli_query($conn, "SELECT * FROM sewa WHERE id = '$id_sewa' AND user_id = '$uid'");
$data = mysqli_fetch_assoc($q_data);

// Jika data tidak ditemukan
if (mysqli_num_rows($q_data) < 1) {
    header("Location: riwayat.php");
    exit();
}

$error_message = '';

// LOGIC 2: UPDATE Data
if (isset($_POST['update'])) {
    $lap = $_POST['lapangan'];
    $tgl = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $dur = $_POST['durasi'];
    $raket = empty($_POST['jml_raket']) ? 0 : $_POST['jml_raket'];

    // Hitung Ulang Harga (Logika Backend)
    $q_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$lap'");
    $d_lap = mysqli_fetch_assoc($q_lap);
    $harga = $d_lap['harga_dasar'];
    $ket = "Edited";

    $hari = date('N', strtotime($tgl));
    if ($hari >= 6) { 
        $harga += 50000; 
        $ket .= " Weekend"; 
    }
    
    $jam_int = (int)substr($jam, 0, 2);
    if ($jam_int >= 17) { 
        $harga += 20000; 
        $ket .= " Night"; 
    }

    $total = ($harga * $dur) + ($raket * 15000);

    // Query Update
    $sql = "UPDATE sewa SET 
            lapangan_id='$lap', tanggal_main='$tgl', jam_mulai='$jam', 
            durasi_jam='$dur', jumlah_raket='$raket', total_bayar='$total', 
            keterangan='$ket' 
            WHERE id='$id_sewa'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script type='text/javascript'>alert('Data Berhasil Diubah'); window.location='riwayat.php';</script>";
        exit();
    } else {
        $error_message = "Gagal mengubah data. Silakan coba lagi.";
    }
}

// Ambil data lapangan untuk dropdown
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
$lapangan_list = [];
while ($row = mysqli_fetch_assoc($query_lapangan)) {
    $lapangan_list[] = $row;
}

$page_title = "Edit Sewa";

// Include template HTML
include '../templates/pages/edit_sewa.html';
?>