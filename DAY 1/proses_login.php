<?php
session_start();
include 'koneksi.php';

// Mengambil data dari form login.html
$username = $_POST['username'];
$password = $_POST['password'];

// Mencari user di tabel users
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($query);

if($cek > 0) {
    $data = mysqli_fetch_assoc($query);
    
    // Menyimpan data login di session
    $_SESSION['username'] = $username;
    $_SESSION['nama'] = $data['nama_lengkap'];
    $_SESSION['level'] = $data['level']; // Menyimpan level (admin/guru)
    $_SESSION['status'] = "login";

    if($data['level'] == "admin") {
        header("location:admin_dashboard.php");
    } else if($data['level'] == "guru") {
        header("location:guru_dashboard.php");
    }
} else {
    // Jika gagal, balikkan ke halaman login
    header("location:login.php?pesan=gagal");
}
?>