<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: dashboard.php");
    exit();
}

$admin_id = $_SESSION['uid'];
$hapus_id = $_GET["id"];

if ($admin_id == $hapus_id) {
    header("Location: dashboard.php");
    exit();
}

$sql = "DELETE FROM users WHERE id='$hapus_id'";
mysqli_query($conn, $sql);

header("Location: dashboard.php");
exit();
?>
