<?php 
session_start();
if($_SESSION['level'] != "guru"){
    header("location:login.html?pesan=belum_login");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Guru | SMAN 6</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f4f4f4;">
    <div style="background: #002d5a; color: white; padding: 20px; text-align: center;">
        <h1>Panel Guru Elang Muda</h1>
        <p>Selamat Bekerja, Pak/Bu <?php echo $_SESSION['nama']; ?>!</p>
    </div>
    
    <div class="info-section">
        <h2>Menu Akademik</h2>
        <div class="cards">
            <div class="card"><h3>Input Nilai Siswa</h3></div>
            <div class="card"><h3>Jadwal Mengajar</h3></div>
            <div class="card"><h3>E-Learning</h3></div>
        </div>
        <br>
        <a href="logout.php" class="btn-submit" style="text-decoration:none; background:red; padding: 10px 20px;">Keluar</a>
    </div>
</body>
</html>