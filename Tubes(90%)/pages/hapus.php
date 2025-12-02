<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: riwayat.php");
    exit();
}

$uid = $_SESSION['uid'];
$id_sewa = $_GET["id"];

$sql = "DELETE FROM sewa WHERE id='$id_sewa' AND user_id='$uid'";
mysqli_query($conn, $sql);

header("Location: riwayat.php");
exit();
?>
