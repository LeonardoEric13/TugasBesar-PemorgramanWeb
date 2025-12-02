<?php
session_start();

include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$page_title = "Dashboard";

// Include template HTML
include 'templates/dashboard.html';
?>