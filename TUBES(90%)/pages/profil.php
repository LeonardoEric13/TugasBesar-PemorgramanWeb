<?php
session_start();
require_once '../config/koneksi.php';


if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit();
}


$user_id = $_SESSION['user_id'];


if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $sql = "UPDATE users SET nama_lengkap = '$nama', email = '$email' WHERE id = '$user_id'";
    mysqli_query($conn, $sql);
    
    $_SESSION['nama'] = $nama;
    
    header("Location: ../dashboard.php");
    exit();
}


if (isset($_POST['hapus'])) {
    $sql_sewa = "DELETE FROM sewa WHERE user_id = '$user_id'";
    mysqli_query($conn, $sql_sewa);
    
    $sql = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $sql);
    
    session_destroy();
    header("Location: ../index.html");
    exit();
}


$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);


$page_title = "Profil";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Profil</title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body class="has-sidebar">
<div id="wrapper">
    <div id="header"><h1>Profil Saya</h1></div>
    <div id="nav"><ul><li><a href="../dashboard.php">Home</a></li></ul></div>
    <div id="content">
        <form action="" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menyimpan perubahan profil?');">
            <div><label>Nama:</label><input type="text" name="nama" value="<?php echo $user['nama_lengkap']; ?>" /></div>
            <div><label>Email:</label><input type="text" name="email" value="<?php echo $user['email']; ?>" /></div>
            <div><input type="submit" name="update" value="Simpan" /></div>
        </form>
        
        <hr />
        
        <form action="" method="post" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun? Data tidak dapat dikembalikan.');">
            <div><input type="submit" name="hapus" value="Hapus Akun" style="background-color:red; color:white;" /></div>
        </form>
    </div>
    <div id="footer"><p>&copy; 2025</p></div>
</div>
</body>
</html>
