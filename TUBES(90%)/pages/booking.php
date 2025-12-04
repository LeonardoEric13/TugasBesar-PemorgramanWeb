<?php

// 1. Memulai session - sesuai materi: session_start()
session_start();

// 2. Include file koneksi dan fungsi
// Sesuai materi: "File informasi koneksi harus disisipkan dengan benar"
require_once '../config/koneksi.php';
require_once '../config/functions_sewa.php';

// 3. Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$error_message = "";

// 4. Ambil daftar lapangan
// Sesuai materi: mysql_query() untuk eksekusi query SELECT
$query_lapangan = "SELECT * FROM lapangan";
$result_lapangan = mysqli_query($conn, $query_lapangan);

// 5. Proses form booking
// Sesuai materi: "Memasukan data dengan query INSERT"
if (isset($_POST['simpan'])) {
    
    // Ambil dan sanitasi input
    // Sesuai materi: mysqli_real_escape_string() untuk keamanan
    $lapangan_id = mysqli_real_escape_string($conn, $_POST['lapangan']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $durasi = (int)$_POST['durasi'];
    $jml_raket = (int)$_POST['jml_raket'];
    
    // Validasi input dasar
    if (empty($lapangan_id) || empty($tanggal) || empty($jam) || $durasi < 1) {
        $error_message = "Semua field wajib diisi dengan benar!";
    } else {
        
        // Validasi tanggal
        $today = date('Y-m-d');
        if ($tanggal < $today) {
            $error_message = "Tanggal tidak boleh di masa lalu!";
        } else {
            
            // Validasi jam operasional
            $validasi_jam = validasiJamOperasional($jam, $durasi);
            
            if (!$validasi_jam['valid']) {
                $error_message = $validasi_jam['pesan'];
            } else {
                
                // Cek ketersediaan lapangan
                $nomor_tersedia = cekKetersediaanLapangan($conn, $lapangan_id, $tanggal, $jam, $durasi);
                
                if (empty($nomor_tersedia)) {
                    // Ambil nama lapangan
                    $q_nama = "SELECT nama_lapangan FROM lapangan WHERE id='$lapangan_id'";
                    $r_nama = mysqli_query($conn, $q_nama);
                    $data_lap = mysqli_fetch_array($r_nama, MYSQLI_ASSOC);
                    
                    $error_message = "Maaf, semua lapangan " . $data_lap['nama_lapangan'] . " sudah penuh pada waktu tersebut.";
                } else {
                    
                    // Pilih nomor lapangan random
                    $nomor_lapangan = $nomor_tersedia[array_rand($nomor_tersedia)];
                    
                    // Hitung total
                    $harga = hitungTotalSewa($conn, $lapangan_id, $durasi, $jml_raket);
                    
                    // INSERT data
                    // Sesuai materi: query INSERT untuk memasukan data
                    $sql = "INSERT INTO sewa (user_id, lapangan_id, nomor_lapangan, tanggal_main, jam_mulai, durasi_jam, jumlah_raket, total_bayar, keterangan_harga) 
                            VALUES ('$uid', '$lapangan_id', '$nomor_lapangan', '$tanggal', '$jam', '$durasi', '$jml_raket', '{$harga['total']}', 'Menunggu')";
                    
                    // Eksekusi query
                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['booking_success'] = "Booking berhasil! Anda mendapat {$harga['nama_lapangan']} No. $nomor_lapangan";
                        header("Location: riwayat.php");
                        exit();
                    } else {
                        $error_message = "Gagal menyimpan booking. Silakan coba lagi.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Booking Lapangan</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
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
            <a href="auth/logout.php" onclick="return confirm('Yakin keluar?');">Logout</a>
        </div>
    </div>
    
    <div id="content">
        <?php if ($error_message != ""): ?>
            <div class="error-box"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <span id="info_harga">Silakan isi data untuk hitung harga.</span>
            <div id="availability_info" class="availability-info"></div>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <option value="">Pilih Lapangan</option>
                    <?php 
                    // Sesuai materi: mysqli_fetch_array() untuk loop data
                    while ($lap = mysqli_fetch_array($result_lapangan, MYSQLI_ASSOC)): 
                    ?>
                        <option value="<?php echo $lap['id']; ?>" 
                                data-harga="<?php echo $lap['harga_dasar']; ?>"
                                data-nama="<?php echo $lap['nama_lapangan']; ?>">
                            <?php echo $lap['nama_lapangan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label>Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" 
                       min="<?php echo date('Y-m-d'); ?>" 
                       value="<?php echo date('Y-m-d'); ?>"
                       onchange="hitungTotal(); cekKetersediaan();"
                       required="required" />
            </div>
            
            <div>
                <label>Jam Mulai:</label>
                <select name="jam" id="jam" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <option value="">Pilih Jam</option>
                    <?php for ($h = JAM_BUKA; $h < JAM_TUTUP; $h++): ?>
                        <option value="<?php echo sprintf('%02d:00', $h); ?>">
                            <?php echo sprintf('%02d:00', $h); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label>Durasi (Jam):</label>
                <select name="durasi" id="durasi" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <?php for ($d = 1; $d <= 8; $d++): ?>
                        <option value="<?php echo $d; ?>" <?php echo ($d == 1) ? 'selected="selected"' : ''; ?>>
                            <?php echo $d; ?> Jam
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label>Sewa Raket (Opsional):</label>
                <input type="number" name="jml_raket" id="jml_raket" value="0" min="0" max="10" onchange="hitungTotal()" />
            </div>
            
            <div>
                <input type="submit" name="simpan" id="btn_submit" value="Sewa Sekarang" />
            </div>
        </form>
    </div>
    
    <div id="footer"><p>&copy; 2025</p></div>
</div>

<script type="text/javascript">
var JAM_BUKA = <?php echo JAM_BUKA; ?>;
var JAM_TUTUP = <?php echo JAM_TUTUP; ?>;

function hitungTotal() {
    var lapangan = document.getElementById('lapangan');
    var durasi = parseInt(document.getElementById('durasi').value) || 1;
    var jml_raket = parseInt(document.getElementById('jml_raket').value) || 0;
    var jam = document.getElementById('jam').value;
    var info = document.getElementById('info_harga');
    
    if (lapangan.value === '') {
        info.innerHTML = 'Silakan pilih lapangan terlebih dahulu.';
        return;
    }
    
    // Validasi durasi tidak melewati jam tutup
    if (jam !== '') {
        var jamMulai = parseInt(jam.substring(0, 2));
        var jamSelesai = jamMulai + durasi;
        
        if (jamSelesai > JAM_TUTUP) {
            info.innerHTML = '<span style="color:red;">Durasi melebihi jam tutup (24:00). Maksimal ' + (JAM_TUTUP - jamMulai) + ' jam.</span>';
            return;
        }
    }
    
    var hargaLap = parseInt(lapangan.options[lapangan.selectedIndex].getAttribute('data-harga'));
    var namaLap = lapangan.options[lapangan.selectedIndex].getAttribute('data-nama');
    var totalLap = hargaLap * durasi;
    var totalRaket = jml_raket * 25000;
    var grandTotal = totalLap + totalRaket;
    
    info.innerHTML = '<strong>' + namaLap + '</strong><br/>' +
                     'Sewa Lapangan: Rp ' + formatRupiah(totalLap) + ' (' + durasi + ' jam x Rp ' + formatRupiah(hargaLap) + ')<br/>' +
                     'Sewa Raket: Rp ' + formatRupiah(totalRaket) + ' (' + jml_raket + ' raket)<br/>' +
                     '<strong>Total: Rp ' + formatRupiah(grandTotal) + '</strong>';
}

function formatRupiah(angka) {
    return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function cekKetersediaan() {
    var lapangan = document.getElementById('lapangan').value;
    var tanggal = document.getElementById('tanggal').value;
    var jam = document.getElementById('jam').value;
    var durasi = document.getElementById('durasi').value;
    var infoDiv = document.getElementById('availability_info');
    var btnSubmit = document.getElementById('btn_submit');
    
    if (lapangan === '' || tanggal === '' || jam === '' || durasi === '') {
        infoDiv.innerHTML = '';
        return;
    }
    
    // AJAX request untuk cek ketersediaan
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'cek_ketersediaan.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.tersedia > 0) {
                infoDiv.innerHTML = '<span class="availability-available">✓ Tersedia ' + response.tersedia + ' lapangan dari 4</span>';
                btnSubmit.disabled = false;
            } else {
                infoDiv.innerHTML = '<span class="availability-full">✗ Lapangan penuh pada waktu tersebut!</span>';
                btnSubmit.disabled = true;
            }
        }
    };
    xhr.send('lapangan=' + lapangan + '&tanggal=' + tanggal + '&jam=' + jam + '&durasi=' + durasi);
}

// Set default
hitungTotal();
</script>
</body>
</html>

