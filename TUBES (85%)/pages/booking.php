<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$success_message = '';
$error_message = '';

// Proses form booking
if (isset($_POST['simpan'])) {
    $uid = $_SESSION['uid'];
    $lap = mysqli_real_escape_string($conn, $_POST['lapangan']);
    $tgl = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $dur = mysqli_real_escape_string($conn, $_POST['durasi']);
    $raket = empty($_POST['jml_raket']) ? 0 : mysqli_real_escape_string($conn, $_POST['jml_raket']);

    // Validasi input
    if (empty($lap) || empty($tgl) || empty($jam) || empty($dur)) {
        $error_message = "Semua field wajib diisi!";
    } else {
        // Hitung di PHP (Backend Validation)
        $q_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$lap'");
        
        if (mysqli_num_rows($q_lap) > 0) {
            $d_lap = mysqli_fetch_assoc($q_lap);
            $harga = $d_lap['harga_dasar'];
            $ket = "Normal";

            // Cek weekend
            $hari = date('N', strtotime($tgl));
            if ($hari >= 6) { 
                $harga += 50000; 
                $ket = "Weekend"; 
            }
            
            // Cek jam malam
            $jam_int = (int)substr($jam, 0, 2);
            if ($jam_int >= 17) { 
                $harga += 20000; 
                $ket .= " Night"; 
            }

            // Hitung total
            $total = ($harga * $dur) + ($raket * 15000);

            // Insert ke database
            $sql = "INSERT INTO sewa (user_id, lapangan_id, tanggal_main, jam_mulai, durasi_jam, jumlah_raket, total_bayar, keterangan) 
                    VALUES ('$uid', '$lap', '$tgl', '$jam', '$dur', '$raket', '$total', '$ket')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script type='text/javascript'>alert('Booking Sukses'); window.location='riwayat.php';</script>";
                exit();
            } else {
                $error_message = "Gagal melakukan booking. Silakan coba lagi.";
            }
        } else {
            $error_message = "Lapangan tidak ditemukan.";
        }
    }
}

// Ambil data lapangan untuk dropdown
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
$lapangan_list = [];
while ($row = mysqli_fetch_assoc($query_lapangan)) {
    $lapangan_list[] = $row;
}

$page_title = "Booking Sewa";

// Include template HTML
include '../templates/pages/booking.html';
?>