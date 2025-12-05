<?php
session_start();

require_once '../config/koneksi.php';
require_once '../config/functions_sewa.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$id_sewa = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : 0;
$error_message = "";

$query = "SELECT * FROM sewa WHERE id='$id_sewa' AND user_id='$uid'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_array($result, MYSQLI_ASSOC);

if (!$data) {
    header("Location: riwayat.php");
    exit();
}

$query_lapangan = "SELECT * FROM lapangan";
$result_lapangan = mysqli_query($conn, $query_lapangan);

if (isset($_POST['update'])) {
    
    $lapangan_id = mysqli_real_escape_string($conn, $_POST['lapangan']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam = mysqli_real_escape_string($conn, $_POST['jam']);
    $durasi = (int)$_POST['durasi'];
    $jml_raket = (int)$_POST['jml_raket'];
    
    if (empty($lapangan_id) || empty($tanggal) || empty($jam) || $durasi < 1) {
        $error_message = "Semua field wajib diisi dengan benar!";
    } else {
        
        $today = date('Y-m-d');
        if ($tanggal < $today) {
            $error_message = "Tanggal tidak boleh di masa lalu!";
        } else {
            
            $validasi_jam = validasiJamOperasional($jam, $durasi);
            
            if (!$validasi_jam['valid']) {
                $error_message = $validasi_jam['pesan'];
            } else {
                
                $nomor_tersedia = cekKetersediaanLapangan($conn, $lapangan_id, $tanggal, $jam, $durasi, $id_sewa);
                
                if (empty($nomor_tersedia)) {
                    $q_nama = "SELECT nama_lapangan FROM lapangan WHERE id='$lapangan_id'";
                    $r_nama = mysqli_query($conn, $q_nama);
                    $data_lap = mysqli_fetch_array($r_nama, MYSQLI_ASSOC);
                    
                    $error_message = "Maaf, semua lapangan " . $data_lap['nama_lapangan'] . " sudah penuh pada waktu tersebut.";
                } else {
                    
                    $nomor_lapangan = in_array($data['nomor_lapangan'], $nomor_tersedia) 
                                      ? $data['nomor_lapangan'] 
                                      : $nomor_tersedia[array_rand($nomor_tersedia)];
                    
                    $harga = hitungTotalSewa($conn, $lapangan_id, $durasi, $jml_raket);
                    
                    $sql = "UPDATE sewa SET 
                            lapangan_id='$lapangan_id',
                            nomor_lapangan='$nomor_lapangan',
                            tanggal_main='$tanggal',
                            jam_mulai='$jam',
                            durasi_jam='$durasi',
                            jumlah_raket='$jml_raket',
                            harga_per_jam_final='{$harga['harga_per_jam']}',
                            total_bayar='{$harga['total']}'
                            WHERE id='$id_sewa' AND user_id='$uid'";
                    
                    if (mysqli_query($conn, $sql)) {
                        $_SESSION['booking_success'] = "Booking berhasil diupdate!";
                        header("Location: riwayat.php");
                        exit();
                    } else {
                        $error_message = "Gagal menyimpan perubahan.";
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
    <title>Edit Transaksi</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body class="has-sidebar">
<div id="wrapper">
    <div id="header"><h1>Edit Transaksi</h1></div>
    
    <div id="nav">
        <ul>
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="riwayat.php">Kembali</a></li>
        </ul>
        <div id="logout-section">
            <a href="../auth/logout.php" onclick="return confirm('Yakin keluar?');">Logout</a>
        </div>
    </div>
    
    <div id="content">
        <?php if ($error_message != ""): ?>
            <div class="error-box"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <div class="info-box">
            <span id="info_harga">Ubah data di bawah. Harga akan dihitung ulang.</span>
            <div id="availability_info" class="availability-info"></div>
        </div>
        
        <form action="" method="post">
            <div>
                <label>Lapangan:</label>
                <select name="lapangan" id="lapangan" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <?php 
                    while ($lap = mysqli_fetch_array($result_lapangan, MYSQLI_ASSOC)):
                        $selected = ($lap['id'] == $data['lapangan_id']) ? 'selected="selected"' : '';
                    ?>
                        <option value="<?php echo $lap['id']; ?>" 
                                data-harga="<?php echo $lap['harga_dasar']; ?>"
                                data-nama="<?php echo $lap['nama_lapangan']; ?>"
                                <?php echo $selected; ?>>
                            <?php echo $lap['nama_lapangan']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label>Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" 
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo $data['tanggal_main']; ?>"
                       onchange="hitungTotal(); cekKetersediaan();"
                       required="required" />
            </div>
            
            <div>
                <label>Jam Mulai:</label>
                <select name="jam" id="jam" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <?php 
                    for ($h = JAM_BUKA; $h < JAM_TUTUP; $h++): 
                        $jam_value = sprintf('%02d:00', $h);
                        $jam_existing = substr($data['jam_mulai'], 0, 5);
                        $selected_jam = ($jam_value == $jam_existing) ? 'selected="selected"' : '';
                    ?>
                        <option value="<?php echo $jam_value; ?>" <?php echo $selected_jam; ?>>
                            <?php echo $jam_value; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label>Durasi (Jam):</label>
                <select name="durasi" id="durasi" onchange="hitungTotal(); cekKetersediaan();" required="required">
                    <?php for ($d = 1; $d <= 8; $d++): 
                        $selected_durasi = ($d == $data['durasi_jam']) ? 'selected="selected"' : '';
                    ?>
                        <option value="<?php echo $d; ?>" <?php echo $selected_durasi; ?>>
                            <?php echo $d; ?> Jam
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div>
                <label>Sewa Raket:</label>
                <input type="number" name="jml_raket" id="jml_raket" 
                       value="<?php echo $data['jumlah_raket']; ?>" 
                       min="0" max="10" onchange="hitungTotal()" />
            </div>
            
            <div>
                <input type="submit" name="update" id="btn_submit" value="Simpan Perubahan" />
            </div>
        </form>
    </div>
    
    <div id="footer"><p>&copy; 2025</p></div>
</div>

<script type="text/javascript">
var JAM_BUKA = <?php echo JAM_BUKA; ?>;
var JAM_TUTUP = <?php echo JAM_TUTUP; ?>;
var currentSewaId = <?php echo $id_sewa; ?>;

function hitungTotal() {
    var lapangan = document.getElementById('lapangan');
    var durasi = parseInt(document.getElementById('durasi').value) || 1;
    var jml_raket = parseInt(document.getElementById('jml_raket').value) || 0;
    var jam = document.getElementById('jam').value;
    var info = document.getElementById('info_harga');
    
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
    xhr.send('lapangan=' + lapangan + '&tanggal=' + tanggal + '&jam=' + jam + '&durasi=' + durasi + '&exclude_id=' + currentSewaId);
}

hitungTotal();
cekKetersediaan();
</script>
</body>
</html>