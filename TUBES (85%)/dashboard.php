<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$page_title = "Dashboard";

// Include template HTML
include 'templates/dashboard.html';
?>