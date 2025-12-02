<?php
session_start();
include '../config/koneksi.php';
$id = $_GET['id'];
$uid = $_SESSION['uid'];
mysqli_query($conn, "DELETE FROM sewa WHERE id='$id' AND user_id='$uid'");
header("Location: riwayat.php");
?>