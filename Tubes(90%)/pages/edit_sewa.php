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

if (!isset($_GET["id"])) {
    header("Location: riwayat.php");
    exit();
}

$id_sewa = $_GET["id"];

$sql_sewa = "SELECT * FROM sewa WHERE id = '$id_sewa' AND user_id = '$uid' LIMIT 1";
$q_sewa = mysqli_query($conn, $sql_sewa);

if (!$q_sewa || mysqli_num_rows($q_sewa) == 0) {
    header("Location: riwayat.php");
    exit();
}

$data_sewa = mysqli_fetch_assoc($q_sewa);

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
            $ket .= " Weekend";
        }

        $jam_int = (int)substr($jam, 0, 2);
        if ($jam_int >= 17) {
            $harga += 20000;
            $ket .= " Night";
        }

        $total = ($harga * $dur) + ($raket * 15000);

        $sql = "UPDATE sewa SET 
                lapangan_id='$lap',
                tanggal_main='$tgl',
                jam_mulai='$jam',
                durasi_jam='$dur',
                jumlah_raket='$raket',
                total_bayar='$total',
                keterangan='$ket'
                WHERE id='$id_sewa' AND user_id='$uid'";

        if (mysqli_query($conn, $sql)) {
            $success_message = "Data booking berhasil diperbarui.";
            $sql_sewa = "SELECT * FROM sewa WHERE id = '$id_sewa' AND user_id = '$uid' LIMIT 1";
            $q_sewa = mysqli_query($conn, $sql_sewa);
            if ($q_sewa && mysqli_num_rows($q_sewa) > 0) {
                $data_sewa = mysqli_fetch_assoc($q_sewa);
            }
        } else {
            $error_message = "Gagal memperbarui data booking.";
        }
    } else {
        $error_message = "Lapangan tidak ditemukan.";
    }
}

$page_title = "Edit Sewa";
include 'edit_sewa.html';
?>
