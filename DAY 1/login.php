<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem | SMAN 6 Banjarmasin</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="https://i.ibb.co/0MHYfMx/download-13.jpg" alt="Logo SMAN 6" class="login-logo">
                <h2>Sistem Informasi Akademik</h2>
                
                <?php 
                if(isset($_GET['pesan']) && $_GET['pesan'] == 'gagal'){
                    echo "<p style='color:red; font-weight:bold; background:#ffe6e6; padding:5px; border-radius:5px;'>⚠️ Username/Password Salah!</p>";
                } else {
                    echo "<p>Silakan masuk dengan akun Anda</p>";
                }
                ?>
            </div>
            
            <form action="proses_login.php" method="POST">
                <div class="input-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                </div>
                
                <div class="input-group">
                    <label for="password">Password</label>
                    <div style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required style="width: 100%;">
                        <i class="fa-regular fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 13px; cursor: pointer; color: #666;"></i>
                    </div>
                </div>
                
                <div class="login-options">
                    <label><input type="checkbox"> Ingat Saya</label>
                    <a href="#">Lupa Password?</a>
                </div>
                
                <button type="submit" class="btn-submit">Masuk Ke Sistem</button>
            </form>
            
            <div class="login-footer">
                <a href="index.html">&larr; Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        // --- FITUR TOGGLE LIHAT PASSWORD ---
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>