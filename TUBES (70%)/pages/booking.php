<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['login'])) header("Location: ../auth/login.php");

if (isset($_POST['simpan'])) {
    $uid = $_SESSION['uid'];
    $lap = $_POST['lapangan'];
    $tgl = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $dur = $_POST['durasi'];
    $raket = empty($_POST['jml_raket']) ? 0 : $_POST['jml_raket'];

    // Hitung di PHP (Backend Validation)
    $q_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$lap'");
    $d_lap = mysqli_fetch_assoc($q_lap);
    $harga = $d_lap['harga_dasar'];
    $ket = "Normal";

    $hari = date('N', strtotime($tgl));
    if ($hari >= 6) { $harga += 50000; $ket = "Weekend"; }
    
    $jam_int = (int)substr($jam, 0, 2);
    if ($jam_int >= 17) { $harga += 20000; $ket .= " Night"; }

    $total = ($harga * $dur) + ($raket * 15000);

    $sql = "INSERT INTO sewa (user_id, lapangan_id, tanggal_main, jam_mulai, durasi_jam, jumlah_raket, total_bayar, keterangan) 
            VALUES ('$uid', '$lap', '$tgl', '$jam', '$dur', '$raket', '$total', '$ket')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script type='text/javascript'>alert('Booking Sukses'); window.location='riwayat.php';</script>";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Booking Sewa</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
    <script type="text/javascript" src="../assets/script.js"></script>
</head>
<body>
<div id="wrapper">
    <div id="header"><h1>Form Booking</h1></div>
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="riwayat.php">Riwayat</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>
    <div id="content">
        <div class="info-box">
            <span id="info_harga">Silakan isi data untuk hitung harga.</span>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal()">
                    <option value="">-- Pilih --</option>
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM lapangan");
                    while ($r = mysqli_fetch_assoc($q)) {
                        echo '<option value="'.$r['id'].'" data-harga="'.$r['harga_dasar'].'">'.$r['nama_lapangan'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" onchange="hitungTotal()" />
            </div>
            <div>
                <label>Jam Mulai:</label>
                <input type="time" name="jam" id="jam" onchange="hitungTotal()" />
            </div>
            <div>
                <label>Durasi (Jam):</label>
                <input type="text" name="durasi" id="durasi" onkeyup="hitungTotal()" />
            </div>
            <div>
                <label>Sewa Raket (Opsional):</label>
                <input type="text" name="jml_raket" id="jml_raket" onkeyup="hitungTotal()" />
            </div>
            <div>
                <input type="submit" name="simpan" value="Sewa Sekarang" />
            </div>
        </form>
    </div>
    <div id="footer"><p>&copy; 2025</p></div>
</div>
</body>
</html>