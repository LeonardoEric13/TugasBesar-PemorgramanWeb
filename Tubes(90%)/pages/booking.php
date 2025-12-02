<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['uid'];
$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lap = isset($_POST["lapangan_id"]) ? $_POST["lapangan_id"] : "";
    $tgl = isset($_POST["tanggal_main"]) ? $_POST["tanggal_main"] : "";
    $jam = isset($_POST["jam_mulai"]) ? $_POST["jam_mulai"] : "";
    $dur = isset($_POST["durasi_jam"]) ? (int)$_POST["durasi_jam"] : 0;
    $raket = isset($_POST["jumlah_raket"]) ? (int)$_POST["jumlah_raket"] : 0;

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

$page_title = "Booking";
include 'booking.html';
?>
