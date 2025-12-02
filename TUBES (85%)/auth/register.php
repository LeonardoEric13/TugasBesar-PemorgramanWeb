<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login'])) {
    header("Location: ../dashboard.php");
    exit();
}

$success_message = "";
$error_message = "";

if (isset($_POST['daftar'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $error_message = "Email sudah terdaftar. Gunakan email lain.";
    } else {
        $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES ('$nama', '$email', '$pass')";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Registrasi berhasil! Silakan login.";
        } else {
            $error_message = "Gagal melakukan registrasi. Silakan coba lagi.";
        }
    }
}

$page_title = "Registrasi";

// Include template HTML
include '../templates/auth/register.html';
?>