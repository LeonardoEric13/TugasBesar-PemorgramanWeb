<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: ../auth/login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: riwayat.php");
    exit();
}

// Perbaiki: pakai 'user_id' sesuai yang di-set di login.php
$uid = $_SESSION['user_id'];
$id_sewa = mysqli_real_escape_string($conn, $_GET["id"]);

$sql = "DELETE FROM sewa WHERE id='$id_sewa' AND user_id='$uid'";
mysqli_query($conn, $sql);

header("Location: riwayat.php");
exit();
?>