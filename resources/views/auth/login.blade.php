<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD SMP</title>
    <meta name="description" content="Login ke Sistem Informasi Akademik SMP">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0F172A;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(30, 64, 175, 0.2) 0%, transparent 50%),
                        radial-gradient(circle at 70% 60%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 50% 90%, rgba(6, 182, 212, 0.1) 0%, transparent 50%);
            animation: bgPulse 8s ease-in-out infinite alternate;
        }

        @keyframes bgPulse {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(-2%, -2%) scale(1.05); }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(59, 130, 246, 0.3);
            border-radius: 50%;
            animation: float linear infinite;
        }

        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            margin: 20px;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            animation: cardIn 0.6s ease;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(30px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #1E40AF, #7C3AED);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: white;
            box-shadow: 0 8px 30px rgba(30, 64, 175, 0.4);
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { box-shadow: 0 8px 30px rgba(30, 64, 175, 0.4); }
            50% { box-shadow: 0 8px 40px rgba(30, 64, 175, 0.6); }
        }

        .login-header h1 {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #3B82F6, #06B6D4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #64748B;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #94A3B8;
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 16px;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #1E293B;
            border: 1px solid #334155;
            border-radius: 12px;
            color: #F1F5F9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .form-input:focus + i,
        .form-input:focus ~ i {
            color: #3B82F6;
        }

        .form-input::placeholder {
            color: #475569;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #94A3B8;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            accent-color: #3B82F6;
            width: 16px;
            height: 16px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1E40AF, #3B82F6);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(30, 64, 175, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: #F87171;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .demo-info {
            margin-top: 28px;
            padding: 20px;
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(34, 211, 238, 0.15);
            border-radius: 14px;
        }

        .demo-info h3 {
            font-size: 13px;
            font-weight: 700;
            color: #22D3EE;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-accounts {
            display: grid;
            gap: 6px;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            font-size: 11.5px;
            color: #94A3B8;
            padding: 4px 0;
        }

        .demo-account span:first-child {
            color: #CBD5E1;
            font-weight: 500;
        }

        .demo-account code {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(30, 41, 59, 0.8);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10.5px;
        }
    </style>
</head>
<body>
    <div class="particles">
        <script>
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = (Math.random() * 10 + 8) + 's';
                p.style.animationDelay = Math.random() * 5 + 's';
                p.style.width = p.style.height = (Math.random() * 4 + 2) + 'px';
                document.querySelector('.particles').appendChild(p);
            }
        </script>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <h1>SIAKAD SMP</h1>
                <p>Sistem Informasi Akademik</p>
            </div>

            @if($errors->any())
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="form-input" placeholder="Masukkan email" value="{{ old('email') }}" required autofocus>
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" class="form-input" placeholder="Masukkan password" required>
                        <i class="bi bi-lock-fill"></i>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <div class="demo-info">
                <h3><i class="bi bi-info-circle-fill"></i> Akun Demo</h3>
                <div class="demo-accounts">
                    <div class="demo-account">
                        <span>Admin</span>
                        <span><code>admin@siakad.com</code> / <code>admin123</code></span>
                    </div>
                    <div class="demo-account">
                        <span>Kepala Sekolah</span>
                        <span><code>kepsek@siakad.com</code> / <code>kepsek123</code></span>
                    </div>
                    <div class="demo-account">
                        <span>Guru</span>
                        <span><code>siti@siakad.com</code> / <code>guru123</code></span>
                    </div>
                    <div class="demo-account">
                        <span>Orang Tua</span>
                        <span><code>parent1@siakad.com</code> / <code>parent123</code></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
