<?php
session_start();
include '../config/koneksi.php';

$pesan = "";
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($q) > 0) {
        $row = mysqli_fetch_assoc($q);
        if (password_verify($pass, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['uid'] = $row['id'];
            $_SESSION['nama'] = $row['nama_lengkap'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            $pesan = "Password salah.";
        }
    } else {
        $pesan = "Email tidak terdaftar.";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body>
<div id="wrapper">
    <div id="header"><h1>Login</h1></div>
    <div id="content" style="float:none; width:auto; text-align:center;">
        <p class="error-msg"><?php echo $pesan; ?></p>
        
        <form action="" method="post">
            <div>
                <label>Email:</label>
                <input type="text" name="email" />
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" />
            </div>
            <div>
                <input type="submit" name="login" value="Masuk" style="width:100px;" />
            </div>
        </form>
        
        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
        <p><a href="../index.html">Kembali ke Depan</a></p>
    </div>
    <div id="footer"><p>&copy; 2025 Lapangan Padel Terjosjis di Jogja</p></div>
</div>
</body>
</html>