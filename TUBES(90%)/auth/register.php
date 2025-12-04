<?php
session_start();
require_once '../config/koneksi.php';

$error_message = '';
$success_message = '';
$page_title = 'Registrasi Member';

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek email sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $error_message = 'Email sudah terdaftar!';
    } else {
        $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES ('$nama', '$email', '$password')";
        if (mysqli_query($conn, $sql)) {
            $success_message = 'Registrasi berhasil! Silakan login.';
        } else {
            $error_message = 'Gagal mendaftar. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body>
<div id="wrapper">
    <div id="header">
        <h1>Registrasi Member</h1>
    </div>
    
    <div id="content" style="float:none; width:auto; text-align:center;">
        <?php if (!empty($success_message)): ?>
            <div class="success-box">
                <p><?php echo $success_message; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-box">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>
        
        <form action="" method="post">
            <div>
                <label>Nama Lengkap:</label>
                <input type="text" name="nama" required="required" />
            </div>
            <div>
                <label>Email:</label>
                <input type="email" name="email" required="required" />
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" minlength="6" required="required" />
            </div>
            <div>
                <input type="submit" name="daftar" value="Daftar" style="width:100px;" />
            </div>
        </form>
        
        <p><a href="login.php">Sudah punya akun? Login disini</a></p>
        <p><a href="../index.html">Kembali ke Halaman Depan</a></p>
    </div>
    
    <div id="footer">
        <p>&copy; 2025 Padel Center</p>
    </div>
</div>
</body>
</html>