<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

// Perbaiki: pakai 'user_id' sesuai yang di-set di login.php
$uid = $_SESSION['user_id'];
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lap = mysqli_real_escape_string($conn, $_POST["lapangan"]);
    $tgl = mysqli_real_escape_string($conn, $_POST["tanggal"]);
    $jam = mysqli_real_escape_string($conn, $_POST["jam"]);
    $dur = (int)$_POST["durasi"];
    $raket = (int)$_POST["jml_raket"];

    $sql_lap = "SELECT * FROM lapangan WHERE id = '$lap' LIMIT 1";
    $q_lap = mysqli_query($conn, $sql_lap);

    if ($q_lap && mysqli_num_rows($q_lap) > 0) {
        $d_lap = mysqli_fetch_assoc($q_lap);

        $harga = $d_lap["harga_dasar"];
        $ket = "Normal";

        $hari = date("N", strtotime($tgl));
        if ($hari >= 6) {
            $harga += 50000;
            $ket = "Weekend";
        }

        $jam_int = (int)substr($jam, 0, 2);
        if ($jam_int >= 17) {
            $harga += 20000;
            $ket .= " Night";
        }

        $total = ($harga * $dur) + ($raket * 15000);

        $sql = "INSERT INTO sewa (user_id, lapangan_id, tanggal_main, jam_mulai, durasi_jam, jumlah_raket, total_bayar, keterangan)
                VALUES ('$uid', '$lap', '$tgl', '$jam', '$dur', '$raket', '$total', '$ket')";

        if (mysqli_query($conn, $sql)) {
            $success_message = "Booking berhasil disimpan.";
        } else {
            $error_message = "Gagal menyimpan booking.";
        }
    } else {
        $error_message = "Lapangan tidak ditemukan.";
    }
}

// Query untuk ambil data lapangan
$sql_lapangan = "SELECT * FROM lapangan";
$lapangan_list = [];
$result_lapangan = mysqli_query($conn, $sql_lapangan);
if ($result_lapangan) {
    while ($row = mysqli_fetch_assoc($result_lapangan)) {
        $lapangan_list[] = $row;
    }
}

$page_title = "Booking";
include '../templates/pages/booking.html';
?>
