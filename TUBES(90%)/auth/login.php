<?php
session_start();
include '../config/koneksi.php';

$error_message = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    // Tambah trim() untuk hapus spasi
    $password = trim($_POST['password']);
    
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password dengan password_verify()
        if (password_verify($password, $user['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Email tidak terdaftar!";
    }
}

$page_title = "Login";
include '../templates/auth/login.html';
?>
