<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_sewa_padel";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Error database");
}
?>