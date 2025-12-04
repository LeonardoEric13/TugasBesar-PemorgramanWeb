<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$page_title = "Edit Transaksi";
$error_message = '';

if (!isset($_GET['id'])) {
    header("Location: riwayat.php");
    exit();
}

$id_sewa = mysqli_real_escape_string($conn, $_GET['id']);

// Ambil data sewa
$query = mysqli_query($conn, "SELECT * FROM sewa WHERE id='$id_sewa' AND user_id='$uid'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    header("Location: riwayat.php");
    exit();
}

// Ambil daftar lapangan
$query_lapangan = mysqli_query($conn, "SELECT * FROM lapangan");
$lapangan_list = array();
while ($row = mysqli_fetch_assoc($query_lapangan)) {
    $lapangan_list[] = $row;
}

if (isset($_POST['update'])) {
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
    
    $sql = "UPDATE sewa SET lapangan_id='$lapangan_id', tanggal_main='$tanggal', jam_mulai='$jam', 
            durasi_jam='$durasi', jumlah_raket='$jml_raket', total_bayar='$total' WHERE id='$id_sewa' AND user_id='$uid'";
    mysqli_query($conn, $sql);
    
    header("Location: riwayat.php");
    exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
    <script type="text/javascript" src="../assets/script.js"></script>
</head>
<body>

<div id="wrapper">
    <div id="header">
        <h1>Edit Transaksi</h1>
    </div>

    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="riwayat.php">Kembali</a></li>
        </ul>
    </div>

    <div id="content">
        <?php if (!empty($error_message)): ?>
            <div class="error-box">
                <p><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <span id="info_harga">Ubah data di bawah ini. Harga akan dihitung ulang.</span>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal()" required>
                    <?php foreach ($lapangan_list as $lapangan): ?>
                        <?php 
                        $selected = ($lapangan['id'] == $data['lapangan_id']) ? 'selected="selected"' : ''; 
                        ?>
                        <option value="<?php echo $lapangan['id']; ?>" 
                                data-harga="<?php echo $lapangan['harga_dasar']; ?>" 
                                <?php echo $selected; ?>>
                            <?php echo $lapangan['nama_lapangan']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" 
                       value="<?php echo $data['tanggal_main']; ?>" 
                       onchange="hitungTotal()" required />
            </div>
            
            <div>
                <label>Jam Mulai:</label>
                <input type="time" name="jam" id="jam" 
                       value="<?php echo $data['jam_mulai']; ?>" 
                       onchange="hitungTotal()" required />
            </div>
            
            <div>
                <label>Durasi (Jam):</label>
                <input type="number" name="durasi" id="durasi" min="1"
                       value="<?php echo $data['durasi_jam']; ?>" 
                       onkeyup="hitungTotal()" required />
            </div>
            
            <div>
                <label>Sewa Raket:</label>
                <input type="number" name="jml_raket" id="jml_raket" min="0"
                       value="<?php echo $data['jumlah_raket']; ?>" 
                       onkeyup="hitungTotal()" />
            </div>
            
            <div>
                <input type="submit" name="update" value="Simpan Perubahan" />
            </div>
        </form>
    </div>

    <div id="footer">
        <p>&copy; 2025 Padel Center</p>
    </div>
</div>

</body>
</html>