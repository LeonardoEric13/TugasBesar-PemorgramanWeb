<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['uid'];

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    mysqli_query($conn, "UPDATE users SET nama_lengkap = '$nama', email = '$email' WHERE id = $user_id");
    $_SESSION['nama'] = $nama;
}

if (isset($_POST['hapus'])) {
    mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
    session_destroy();
    header("Location: ../index.html");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($query);

$page_title = "Profil";
include '../templates/pages/profil.html';
?>
