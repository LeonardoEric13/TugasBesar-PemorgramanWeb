<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="assets/style.css" />
</head>
<body>

<div id="wrapper">
    <div id="header">
        <h1>Dashboard Member</h1>
    </div>

    <div id="nav">
        <ul>
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="pages/booking.php">Sewa Lapangan</a></li>
            <li><a href="pages/profil.php">Profil</a></li>
            <li><a href="auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div id="content">
        <h2>Halo, <?php echo $_SESSION['nama']; ?></h2>
        <div class="info-box">
            <p>Selamat datang di sistem. Silakan pilih menu di samping untuk melakukan transaksi.</p>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 Lapangan Padel Terjosjis di Jogja</p>
    </div>
</div>

</body>
</html>