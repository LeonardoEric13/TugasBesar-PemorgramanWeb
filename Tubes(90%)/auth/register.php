<?php

$error_message   = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama  = isset($_POST["nama_lengkap"]) ? $_POST["nama_lengkap"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $pass  = isset($_POST["password"]) ? $_POST["password"] : "";

    $sql_cek   = "SELECT * FROM users WHERE email = '$email'";
    $result_cek = mysqli_query($conn, $sql_cek);

    if ($result_cek && mysqli_num_rows($result_cek) > 0) {

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

include '../templates/auth/register.html';
?>
