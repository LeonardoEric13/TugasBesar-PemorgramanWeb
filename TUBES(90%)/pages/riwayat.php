<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];

$query = "SELECT sewa.*, lapangan.nama_lapangan
          FROM sewa
          JOIN lapangan ON sewa.lapangan_id = lapangan.id
          WHERE user_id = '$uid'
          ORDER BY sewa.tanggal_main DESC";

$result = mysqli_query($conn, $query);
$riwayat_list = array();

while ($row = mysqli_fetch_assoc($result)) {
    $riwayat_list[] = $row;
}

$page_title = "Riwayat Booking";
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
        <h1>Riwayat Booking</h1>
    </div>
    
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="booking.php">Sewa Lapangan</a></li>
            <li><a href="profil.php">Profil</a></li>
        </ul>

        <div id="logout-section">
            <a href="../auth/logout.php"
               onclick="return confirm('Apakah Anda yakin ingin keluar?');">Logout</a>
        </div>
    </div>
    
    <div id="content">
        <?php if (empty($riwayat_list)): ?>
            <div class="info-box">
                <p>Belum ada riwayat booking. <a href="booking.php">Booking sekarang</a></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam Mulai</th>
                        <th>Durasi</th>
                        <th>Total Bayar</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($riwayat_list as $booking): ?>
                        <tr>
                            <td><?php echo $booking['nama_lapangan']; ?></td>
                            <td><?php echo $booking['tanggal_main']; ?></td>
                            <td><?php echo $booking['jam_mulai']; ?></td>
                            <td><?php echo $booking['durasi_jam']; ?> Jam</td>
                            <td>Rp <?php echo number_format($booking['total_bayar'], 0, ',', '.'); ?></td>
                            <td><?php echo $booking['keterangan']; ?></td>
                            <td>
                                <a href="edit_sewa.php?id=<?php echo $booking['id']; ?>">Edit</a> | 
                                <a href="hapus.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Yakin ingin membatalkan booking ini?')">Batal</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div id="footer">
        <p>&copy; 2025 Padel Center</p>
    </div>
</div>
</body>
</html>
