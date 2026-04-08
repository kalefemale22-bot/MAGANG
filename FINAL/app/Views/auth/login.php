<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SMAN 6 Banjarmasin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(14, 165, 233, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(245, 158, 11, 0.08) 0%, transparent 50%);
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(2%, -2%) rotate(1deg); }
            66% { transform: translate(-1%, 1%) rotate(-1deg); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 2.5rem;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: cardSlideUp 0.6s ease;
        }

        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .school-logo {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2rem;
            color: #fff;
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        }

        .school-name {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            text-align: center;
            margin-bottom: 0.25rem;
        }

        .school-subtitle {
            font-size: 0.8rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 500;
        }

        .form-floating {
            margin-bottom: 1rem;
        }

        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .form-floating .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            border: none;
            border-radius: 0.75rem;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            background: linear-gradient(135deg, #4338ca, #4f46e5);
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-help {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .alert {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            border: none;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            padding: 0.25rem;
            cursor: pointer;
            z-index: 5;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="school-logo">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <h1 class="school-name">SMAN 6 Banjarmasin</h1>
            <p class="school-subtitle">Sistem Informasi Sekolah — T.A. 2025/2026</p>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger d-flex align-items-center">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success d-flex align-items-center">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/auth/login">
                <?= csrf_field() ?>

                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username"
                           placeholder="Username" value="<?= old('username') ?>" required autofocus>
                    <label for="username"><i class="bi bi-person me-1"></i> NUPTK / Nomor Siswa</label>
                </div>

                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock me-1"></i> Password</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>

                <button type="submit" class="btn btn-login mt-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="login-help">
                <p class="mb-1"><strong>Guru:</strong> Username = NUPTK</p>
                <p class="mb-1"><strong>Siswa:</strong> Username = Nomor Siswa</p>
                <p class="mb-0">Password default: <code>123123</code></p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</body>
</html>
