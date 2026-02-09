<?php 
session_start();
if($_SESSION['level'] != "admin"){
    header("location:login.html?pesan=belum_login");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin | SMAN 6</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f4f4f4;">
    <div style="background: #004080; color: white; padding: 20px; text-align: center;">
        <h1>Panel Kendali Admin</h1>
        <p>Selamat Datang, <?php echo $_SESSION['nama']; ?>!</p>
    </div>
    
    <div class="info-section">
        <h2>Menu Administrasi</h2>
        <div class="cards">
            <div class="card"><h3>Kelola Data Guru</h3></div>
            <div class="card"><h3>Kelola Berita Sekolah</h3></div>
            <div class="card"><h3>Pengaturan Sistem</h3></div>
        </div>
        <br>
        <a href="logout.php" class="btn-submit" style="text-decoration:none; background:red; padding: 10px 20px;">Keluar</a>
    </div>
</body>
</html>