<?php
session_start();
include '../config/koneksi.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Riwayat</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body>
<div id="wrapper">
    <div id="header"><h1>Riwayat Booking</h1></div>
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="booking.php">Booking</a></li>
        </ul>
    </div>
    <div id="content">
        <table>
            <thead>
                <tr>
                    <th>Lapangan</th>
                    <th>Waktu</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $uid = $_SESSION['uid'];
                $q = "SELECT sewa.*, lapangan.nama_lapangan 
                      FROM sewa 
                      JOIN lapangan ON sewa.lapangan_id = lapangan.id 
                      WHERE user_id = '$uid'";
                $res = mysqli_query($conn, $q);
                
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>".$row['nama_lapangan']."</td>";
                    echo "<td>".$row['tanggal_main']." (".$row['jam_mulai'].")</td>";
                    echo "<td>Rp ".$row['total_bayar']."</td>";
                    echo "<td>
                            <a href='edit_sewa.php?id=".$row['id']."'>Edit</a> | 
                            <a href='hapus.php?id=".$row['id']."'>Batal</a>
                          </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div id="footer"><p>&copy; 2025</p></div>
</div>
</body>
</html>