<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_sewa = $_GET['id'];
$uid = $_SESSION['uid'];

// LOGIC 1: READ Data Lama (Sesuai Minggu 14 Slide 1042)
// Ambil data untuk ditampilkan di form agar user tidak mengetik ulang dari nol
$q_data = mysqli_query($conn, "SELECT * FROM sewa WHERE id = '$id_sewa' AND user_id = '$uid'");
$data = mysqli_fetch_assoc($q_data);

// Jika data tidak ditemukan (misal user iseng ganti ID di URL)
if (mysqli_num_rows($q_data) < 1) {
    header("Location: riwayat.php");
    exit();
}

// LOGIC 2: UPDATE Data (Sesuai Minggu 14 Slide 1088)
if (isset($_POST['update'])) {
    $lap = $_POST['lapangan'];
    $tgl = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $dur = $_POST['durasi'];
    $raket = empty($_POST['jml_raket']) ? 0 : $_POST['jml_raket'];

    // Hitung Ulang Harga (Logika Backend)
    $q_lap = mysqli_query($conn, "SELECT * FROM lapangan WHERE id='$lap'");
    $d_lap = mysqli_fetch_assoc($q_lap);
    $harga = $d_lap['harga_dasar'];
    $ket = "Edited";

    $hari = date('N', strtotime($tgl));
    if ($hari >= 6) { $harga += 50000; $ket .= " Weekend"; }
    
    $jam_int = (int)substr($jam, 0, 2);
    if ($jam_int >= 17) { $harga += 20000; $ket .= " Night"; }

    $total = ($harga * $dur) + ($raket * 15000);

    // Query Update
    $sql = "UPDATE sewa SET 
            lapangan_id='$lap', tanggal_main='$tgl', jam_mulai='$jam', 
            durasi_jam='$dur', jumlah_raket='$raket', total_bayar='$total', 
            keterangan='$ket' 
            WHERE id='$id_sewa'";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script type='text/javascript'>alert('Data Berhasil Diubah'); window.location='riwayat.php';</script>";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Edit Sewa</title>
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
        <div class="info-box">
            <span id="info_harga">Ubah data di bawah ini. Harga akan dihitung ulang.</span>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal()">
                    <?php
                    $q = mysqli_query($conn, "SELECT * FROM lapangan");
                    while ($r = mysqli_fetch_assoc($q)) {
                        // Menandai lapangan yang dipilih sebelumnya (selected)
                        $sel = ($r['id'] == $data['lapangan_id']) ? 'selected="selected"' : '';
                        echo '<option value="'.$r['id'].'" data-harga="'.$r['harga_dasar'].'" '.$sel.'>'.$r['nama_lapangan'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label>Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" value="<?php echo $data['tanggal_main']; ?>" onchange="hitungTotal()" />
            </div>
            <div>
                <label>Jam Mulai:</label>
                <input type="time" name="jam" id="jam" value="<?php echo $data['jam_mulai']; ?>" onchange="hitungTotal()" />
            </div>
            <div>
                <label>Durasi (Jam):</label>
                <input type="text" name="durasi" id="durasi" value="<?php echo $data['durasi_jam']; ?>" onkeyup="hitungTotal()" />
            </div>
            <div>
                <label>Sewa Raket:</label>
                <input type="text" name="jml_raket" id="jml_raket" value="<?php echo $data['jumlah_raket']; ?>" onkeyup="hitungTotal()" />
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