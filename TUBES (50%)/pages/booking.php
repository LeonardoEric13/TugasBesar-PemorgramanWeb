<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['login'])) header("Location: ../auth/login.php");

// LOGIKA SIMPAN BELUM DIBUAT (PROGRESS)
if (isset($_POST['simpan'])) {
    // Kita hanya tes apakah data terkirim, belum simpan ke DB
    echo "<script type='text/javascript'>alert('Booking Sukses'); window.location='riwayat.php';</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title>Booking Sewa</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
    </head>
<body>
<div id="wrapper">
    <div id="header"><h1>Form Booking</h1></div>
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>
    <div id="content">
        <div class="info-box">
            <p>Silakan isi form di bawah ini.</p>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan">
                    <option value="">-- Pilih --</option>
                    <?php
                    // Menampilkan data dari DB sudah bisa (Read Master Data)
                    $q = mysqli_query($conn, "SELECT * FROM lapangan");
                    while ($r = mysqli_fetch_assoc($q)) {
                        echo '<option value="'.$r['id'].'">'.$r['nama_lapangan'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Tanggal:</label>
                <input type="text" name="tanggal" value="YYYY-MM-DD" /> </div>
            <div>
                <label>Jam Mulai:</label>
                <input type="text" name="jam" value="HH:MM" />
            </div>
            <div>
                <label>Durasi (Jam):</label>
                <input type="text" name="durasi" />
            </div>
            <div>
                <input type="submit" name="simpan" value="Booking" />
            </div>
        </form>
    </div>
    <div id="footer"><p>&copy; 2025 Lapangan Padel Terjosjis di Jogja</p></div>
</div>
</body>
</html>