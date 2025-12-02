<?php
session_start();
require_once '../config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login'])) {
    header("Location: ../dashboard.php");
    exit();
}

$error_message = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        
        if (password_verify($pass, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['uid'] = $user['id'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error_message = "Password salah.";
        }
    } else {
        $error_message = "Email tidak terdaftar.";
    }
}

$page_title = "Login";

// Include template HTML
include '../templates/auth/login.html';
?>