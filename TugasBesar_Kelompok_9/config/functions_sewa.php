<?php

define('JAM_BUKA', 8);
define('JAM_TUTUP', 24);
define('MAX_LAPANGAN_PER_TIPE', 4);
define('HARGA_RAKET', 25000);

function validasiJamOperasional($jam_mulai, $durasi) {
    $jam_mulai_int = (int)substr($jam_mulai, 0, 2);
    $jam_selesai_int = $jam_mulai_int + $durasi;
    
    if ($jam_mulai_int < JAM_BUKA) {
        return array('valid' => false, 'pesan' => 'Maaf, lapangan baru buka jam 08:00 pagi.');
    }
    
    if ($jam_mulai_int >= JAM_TUTUP) {
        return array('valid' => false, 'pesan' => 'Maaf, lapangan sudah tutup jam 24:00.');
    }
    
    if ($jam_selesai_int > JAM_TUTUP) {
        return array('valid' => false, 'pesan' => 'Durasi melebihi jam operasional. Maksimal sampai jam 24:00.');
    }
    
    return array('valid' => true, 'pesan' => '');
}

function cekKetersediaanLapangan($conn, $lapangan_id, $tanggal, $jam_mulai, $durasi, $exclude_id = null) {
    $jam_mulai_int = (int)substr($jam_mulai, 0, 2);
    $jam_selesai_int = $jam_mulai_int + $durasi;
    
    $query = "SELECT nomor_lapangan, jam_mulai, durasi_jam FROM sewa 
              WHERE lapangan_id = '$lapangan_id' 
              AND tanggal_main = '$tanggal'
              AND keterangan_harga != 'Dibatalkan'";
    
    if ($exclude_id != null) {
        $query .= " AND id != '$exclude_id'";
    }
    
    $result = mysqli_query($conn, $query);
    $nomor_terpakai = array();
    
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $booking_jam_mulai = (int)substr($row['jam_mulai'], 0, 2);
        $booking_jam_selesai = $booking_jam_mulai + (int)$row['durasi_jam'];
        
        if ($jam_mulai_int < $booking_jam_selesai && $booking_jam_mulai < $jam_selesai_int) {
            $nomor_terpakai[] = $row['nomor_lapangan'];
        }
    }
    
    $nomor_tersedia = array();
    for ($i = 1; $i <= MAX_LAPANGAN_PER_TIPE; $i++) {
        if (!in_array($i, $nomor_terpakai)) {
            $nomor_tersedia[] = $i;
        }
    }
    
    return $nomor_tersedia;
}

function hitungTotalSewa($conn, $lapangan_id, $durasi, $jml_raket) {
    $query = "SELECT harga_dasar, nama_lapangan FROM lapangan WHERE id='$lapangan_id'";
    $result = mysqli_query($conn, $query);
    $lap = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    $harga_per_jam = $lap['harga_dasar'];
    $harga_lapangan = $harga_per_jam * $durasi;
    $harga_raket = $jml_raket * HARGA_RAKET;
    $total = $harga_lapangan + $harga_raket;
    
    return array(
        'nama_lapangan' => $lap['nama_lapangan'],
        'harga_per_jam' => $harga_per_jam,
        'total' => $total
    );
}
?>