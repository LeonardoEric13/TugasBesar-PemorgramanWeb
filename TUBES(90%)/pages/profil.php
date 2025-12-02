<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Perbaiki: pakai 'user_id' sesuai yang di-set di login.php
$user_id = $_SESSION['user_id'];

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $sql = "UPDATE users SET nama_lengkap = '$nama', email = '$email' WHERE id = '$user_id'";
    mysqli_query($conn, $sql);
    
    $_SESSION['nama'] = $nama;
}

if (isset($_POST['hapus'])) {
    // Hapus dulu semua data sewa milik user
    $sql_sewa = "DELETE FROM sewa WHERE user_id = '$user_id'";
    mysqli_query($conn, $sql_sewa);
    
    // Baru hapus user
    $sql = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $sql);
    
    session_destroy();
    header("Location: ../index.html");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);

$page_title = "Profil";
include '../templates/pages/profil.html';
?>
