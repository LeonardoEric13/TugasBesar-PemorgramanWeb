<?php
session_start();
include '../config/koneksi.php';
$uid = $_SESSION['uid'];

// LOGIKA UPDATE BELUM ADA
// if (isset($_POST['update'])) { ... }

// Tapi Read Data User sudah jalan
$d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$uid'"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Profil</title><link rel="stylesheet" type="text/css" href="../assets/style.css" /></head>
<body>
<div id="wrapper">
    <div id="header"><h1>Profil Saya</h1></div>
    <div id="nav"><ul><li><a href="../dashboard.php">Home</a></li></ul></div>
    <div id="content">
        <form action="" method="post">
            <div><label>Nama:</label><input type="text" name="nama" value="<?php echo $d['nama_lengkap']; ?>" disabled="disabled" /></div>
            <div><label>Email:</label><input type="text" name="email" value="<?php echo $d['email']; ?>" disabled="disabled" /></div>
        </form>
    </div>
    <div id="footer"><p>&copy; 2025 Lapangan Padel Terjosjis di Jogja</p></div>
</div>
</body>
</html>