<?php
session_start();

include 'config/koneksi.php';

// Cek sudah login atau belum
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit();
}

$nama_user  = $_SESSION['nama'];
$page_title = "Dashboard";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="assets/style.css" />
</head>
<body class="has-sidebar">

<div id="wrapper">
    <div id="header">
        <h1>Dashboard Member</h1>
    </div>

    <div id="nav">
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="pages/booking.php">Sewa Lapangan</a></li>
            <li><a href="pages/riwayat.php">Riwayat</a></li>
            <li><a href="pages/profil.php">Profil</a></li>
        </ul>

        <div id="logout-section">
            <a href="auth/logout.php"
               onclick="return confirm('Apakah Anda yakin ingin keluar?');">Logout</a>
        </div>
    </div>

    <div id="content">
        <h2>Halo, <?php echo htmlspecialchars($nama_user); ?></h2>
        <div class="info-box">
            <p>Selamat datang di sistem. Silakan pilih menu di samping untuk melakukan transaksi.</p>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 Padel Center</p>
    </div>
</div>

</body>
</html>

