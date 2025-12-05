<?php
session_start();
include '../config/koneksi.php';

$error_message = "";

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = trim($_POST['password']);

    $sql    = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['login']   = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama_lengkap'];
            $_SESSION['email']   = $user['email'];

            header("Location: ../dashboard.php");
            exit();
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Email tidak terdaftar!";
    }
}

$page_title = "Login";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="id" lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/style.css" />
</head>
<body>
<div id="wrapper">
    <div id="header">
        <h1>Login Member</h1>
    </div>

    <div id="content" style="float:none; width:auto; text-align:center;">
        <?php if (!empty($error_message)): ?>
            <div class="error-box">
                <p style="color: red;"><?php echo $error_message; ?></p>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div>
                <label>Email:</label>
                <input type="email" name="email" required="required" />
            </div>
            <div>
                <label>Password:</label>
                <input type="password" name="password" required="required" />
            </div>
            <div>
                <input type="submit" name="login" value="Masuk" style="width:100px;" />
            </div>
        </form>

        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
        <p><a href="../index.html">Kembali ke Halaman Depan</a></p>
    </div>

    <div id="footer">
        <p>&copy; 2025 Padel Center Yogyakarta</p>
    </div>
</div>
</body>
</html>
