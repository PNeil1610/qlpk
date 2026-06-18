<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Đăng nhập') }} - {{ config('app.name', 'QL Phòng Khám') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * { box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d7e3fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', 'Nunito', sans-serif;
            margin: 0;
        }
        .login-wrapper {
            width: 100%;
            max-width: 900px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            min-height: 550px;
        }
        .login-left {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            padding: 50px 40px;
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-left::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg opacity="0.08" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="15" fill="%23ffffff"/><circle cx="80" cy="80" r="25" fill="%23ffffff"/><circle cx="85" cy="25" r="10" fill="%23ffffff"/></svg>') no-repeat center center;
            background-size: cover;
            pointer-events: none;
        }
        .login-left .logo-icon {
            font-size: 4.5rem;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.2);
            width: 100px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            z-index: 1;
        }
        .login-left h2 {
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: 1px;
            font-size: 1.8rem;
            z-index: 1;
        }
        .login-left p {
            font-size: 1.05rem;
            opacity: 0.85;
            line-height: 1.6;
            z-index: 1;
        }
        .login-left .security-badges {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            z-index: 1;
        }
        .login-left .badge-item {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .login-right {
            width: 55%;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }
        .login-right h3 {
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .login-right .subtitle {
            color: #64748b;
            margin-bottom: 35px;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        /* Keycloak SSO Button */
        .btn-keycloak {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: none;
            border-radius: 14px;
            padding: 16px 24px;
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            color: white;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            width: 100%;
            cursor: pointer;
        }
        .btn-keycloak:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(2, 132, 199, 0.45);
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
            color: white;
        }
        .btn-keycloak:active {
            transform: translateY(-1px);
        }
        .btn-keycloak .kc-icon {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,0.25);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        /* Feature list */
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0 0 35px 0;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            color: #475569;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list li .feat-icon {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }
        /* Alert styles */
        .alert-keycloak {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 14px 18px;
            color: #0369a1;
            font-size: 0.88rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
        .divider span {
            padding: 0 12px;
        }
        /* Loading spinner overlay */
        .btn-keycloak.loading {
            pointer-events: none;
            opacity: 0.85;
        }
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
                min-height: auto;
            }
            .login-left, .login-right { width: 100%; }
            .login-left { padding: 40px 20px; }
            .login-left .logo-icon { width: 80px; height: 80px; font-size: 3.5rem; }
            .login-right { padding: 35px 25px; }
        }

        /* Pulse animation for the shield icon */
        @keyframes pulse-shield {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        .pulse { animation: pulse-shield 2.5s ease-in-out infinite; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        {{-- Cột trái: Branding --}}
        <div class="login-left">
            <div class="logo-icon mt-4 pulse">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h2>Clinic Management System</h2>
            <p>Hệ thống Quản lý Phòng khám Đa khoa thông minh, tiện lợi và chuyên nghiệp.</p>

            <div class="security-badges">
                <div class="badge-item"><i class="fas fa-shield-halved me-1"></i> SSO</div>
                <div class="badge-item"><i class="fas fa-lock me-1"></i> OIDC</div>
                <div class="badge-item"><i class="fas fa-key me-1"></i> OAuth2</div>
            </div>
        </div>
        
        {{-- Cột phải: Đăng nhập qua Keycloak --}}
        <div class="login-right">
            <h3>Welcome!</h3>
            <!-- <p class="subtitle">
                Hệ thống sử dụng <strong>Keycloak</strong> làm cổng xác thực tập trung.<br>
                Bấm nút bên dưới để đăng nhập an toàn.
            </p> -->

            {{-- Hiển thị lỗi nếu có --}}
            @if ($errors->any())
                <div class="alert alert-danger rounded-3 mb-4 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger rounded-3 mb-4 small">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Thông tin Keycloak --}}
            <div class="alert-keycloak">
                <i class="fas fa-info-circle mt-1 flex-shrink-0"></i>
                <div>
                    Đăng nhập, tạo tài khoản và đặt lại mật khẩu được quản lý tập trung bởi <strong>Keycloak Identity Server</strong>.
                </div>
            </div>

            {{-- Nút đăng nhập Keycloak --}}
            <a href="{{ route('keycloak.redirect') }}" class="btn-keycloak" id="loginBtn">
                <div class="kc-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <span>Đăng nhập qua Keycloak</span>
                <i class="fas fa-arrow-right ms-auto"></i>
            </a>

            <div class="divider mt-4 mb-0">
                <span>Thông tin bảo mật</span>
            </div>

            <ul class="feature-list mt-3">
                <li>
                    <div class="feat-icon" style="background:#eff6ff; color:#3b82f6;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span>Mật khẩu được Keycloak mã hóa và bảo vệ</span>
                </li>
                <li>
                    <div class="feat-icon" style="background:#f0fdf4; color:#22c55e;">
                        <i class="fas fa-arrows-rotate"></i>
                    </div>
                    <span>Đăng nhập một lần (SSO) cho toàn hệ thống</span>
                </li>
                <li>
                    <div class="feat-icon" style="background:#fff7ed; color:#f97316;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <span>Quên mật khẩu? Tự phục hồi qua email</span>
                </li>
            </ul>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thêm hiệu ứng loading khi nhấn nút đăng nhập
        document.getElementById('loginBtn').addEventListener('click', function() {
            this.classList.add('loading');
            this.innerHTML = '<div class="kc-icon"><i class="fas fa-spinner fa-spin"></i></div><span>Đang chuyển hướng...</span>';
        });
    </script>
</body>
</html>
