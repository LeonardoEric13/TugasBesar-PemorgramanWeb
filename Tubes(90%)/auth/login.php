<?php
// Mulai session untuk menyimpan data login
session_start();

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $pass  = isset($_POST["password"]) ? $_POST["password"] : "";

    $sql   = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $query = mysqli_query($conn, $sql);

    if ($query && mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        if (password_verify($pass, $user["password"])) {
            $_SESSION["login"] = true;
            $_SESSION["uid"]   = $user["id"];
            $_SESSION["nama"]  = $user["nama_lengkap"];

            header("Location: ../dashboard.php");
            exit();
        } else {
            $error_message = "Password salah.";
        }
    } else {
        $error_message = "Email tidak terdaftar.";
    }
}

$page_title = "Login";

include '../templates/auth/login.html';
?>
