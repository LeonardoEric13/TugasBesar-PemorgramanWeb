<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$page_title = "Booking Lapangan";

// Ambil daftar lapangan
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
$lapangan_list = array();
while ($row = mysqli_fetch_assoc($query_lapangan)) {
    $lapangan_list[] = $row;
}

if (isset($_POST['simpan'])) {
    $lapangan_id = mysqli_real_escape_string($conn, $_POST['lapangan']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $durasi = (int)$_POST['durasi'];
    $jml_raket = (int)$_POST['jml_raket'];
    
    // Hitung total
    $q_lap = mysqli_query($conn, "SELECT harga_dasar FROM lapangan WHERE id='$lapangan_id'");
    $lap = mysqli_fetch_assoc($q_lap);
    $harga_lap = $lap['harga_dasar'] * $durasi;
    $harga_raket = $jml_raket * 25000;
    $total = $harga_lap + $harga_raket;
    
    $sql = "INSERT INTO sewa (user_id, lapangan_id, tanggal_main, jam_mulai, durasi_jam, jumlah_raket, total_bayar, keterangan) 
            VALUES ('$uid', '$lapangan_id', '$tanggal', '$jam', '$durasi', '$jml_raket', '$total', 'Menunggu')";
    mysqli_query($conn, $sql);
    
    header("Location: riwayat.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
    <script type="text/javascript" src="../assets/script.js"></script>
</head>
<body class="has-sidebar">
<div id="wrapper">
    <div id="header"><h1>Form Booking</h1></div>
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="riwayat.php">Riwayat</a></li>
        </ul>

        <div id="logout-section">
            <a href="auth/logout.php"
               onclick="return confirm('Apakah Anda yakin ingin keluar?');">Logout</a>
        </div>
    </div>
    <div id="content">
        <div class="info-box">
            <span id="info_harga">Silakan isi data untuk hitung harga.</span>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal()">
                    <option value="">Pilih Lapangan</option>
                    <?php foreach ($lapangan_list as $lapangan): ?>
                        <option value="<?php echo $lapangan['id']; ?>" 
                                data-harga="<?php echo $lapangan['harga_dasar']; ?>">
                            <?php echo $lapangan['nama_lapangan']; ?>
                        </option>
                    <?php endforeach; ?>
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

