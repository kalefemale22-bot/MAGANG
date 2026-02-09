<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "sma 6"; // Nama database harus persis seperti di gambarmu

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>