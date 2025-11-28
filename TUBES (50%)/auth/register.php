<?php
include '../config/koneksi.php';
$info = "";

if (isset($_POST['daftar'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (nama_lengkap, email, password) VALUES ('$nama', '$email', '$pass')";
    if (mysqli_query($conn, $sql)) {
        $info = "Registrasi Berhasil. Silakan Login.";
    } else {
        $info = "Gagal: Email mungkin sudah dipakai.";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Daftar</title><link rel="stylesheet" type="text/css" href="../assets/style.css" /></head>
<body>
<div id="wrapper">
    <div id="header"><h1>Registrasi</h1></div>
    <div id="content" style="float:none; width:auto; text-align:center;">
        <p style="color:blue;"><?php echo $info; ?></p>
        <form action="" method="post">
            <div><label>Nama Lengkap:</label><input type="text" name="nama" /></div>
            <div><label>Email:</label><input type="text" name="email" /></div>
            <div><label>Password:</label><input type="password" name="password" /></div>
            <div><input type="submit" name="daftar" value="Daftar" style="width:100px;" /></div>
        </form>
        <p><a href="login.php">Sudah punya akun? Login</a></p>
    </div>
    <div id="footer"><p>&copy; 2025 Lapangan Padel Terjosjis di Jogja</p></div>
</div>
</body>
</html>
