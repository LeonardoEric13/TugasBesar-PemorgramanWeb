<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}

$uid = $_SESSION['uid'];

// LOGIC DELETE (Sesuai Minggu 14 Slide 1096)
// Langkah 1: Hapus data di tabel 'sewa' dulu karena ada Foreign Key
$sql_delete_sewa = "DELETE FROM sewa WHERE user_id = '$uid'";
mysqli_query($conn, $sql_delete_sewa);

// Langkah 2: Hapus data user
$sql_delete_user = "DELETE FROM users WHERE id = '$uid'";
$hapus = mysqli_query($conn, $sql_delete_user);

if ($hapus) {
    // Langkah 3: Hapus session dan logout (Minggu 14 Slide 982)
    session_destroy();
    echo "<script type='text/javascript'>
            alert('Akun Anda berhasil dihapus.'); 
            window.location='../index.html';
          </script>";
} else {
    echo "<script type='text/javascript'>
            alert('Gagal menghapus akun.'); 
            window.location='profil.php';
          </script>";
}
?>