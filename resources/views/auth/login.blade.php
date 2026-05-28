<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Đăng nhập') }} - {{ config('app.name', 'QL Phòng Khám') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8 0%, #d7e3fc 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
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
        }
        .login-left::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg opacity="0.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="15" fill="%23ffffff"/><circle cx="80" cy="80" r="25" fill="%23ffffff"/><circle cx="85" cy="25" r="10" fill="%23ffffff"/></svg>') no-repeat center center;
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
        }
        .login-left h2 {
            font-weight: 800;
            margin-bottom: 15px;
            letter-spacing: 1px;
            font-size: 1.8rem;
        }
        .login-left p {
            font-size: 1.05rem;
            opacity: 0.85;
            line-height: 1.6;
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
            margin-bottom: 30px;
            font-size: 1.8rem;
        }
        .form-label {
            font-weight: 600;
            color: #475569;
            font-size: 0.95rem;
        }
        .form-control {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
            color: #334155;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            background: #ffffff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
        }
        .input-group-text {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #94a3b8;
            border-radius: 12px 0 0 12px;
            padding-left: 18px;
            transition: all 0.3s ease;
        }
        .form-control.border-start-0 {
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding-left: 0;
        }
        .input-group:focus-within .input-group-text {
            border-color: #38bdf8;
            color: #0284c7;
            background: #ffffff;
        }
        .input-group:focus-within .form-control {
            border-color: #38bdf8;
        }
        .btn-login {
            background: linear-gradient(to right, #0284c7, #0369a1);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            color: white;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.4);
            background: linear-gradient(to right, #0369a1, #075985);
        }
        .form-check-input {
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #0284c7;
            border-color: #0284c7;
        }
        .form-check-label {
            cursor: pointer;
        }
        .forgot-link {
            color: #0284c7;
            font-weight: 600;
            transition: color 0.2s;
        }
        .forgot-link:hover {
            color: #075985;
            text-decoration: underline !important;
        }
        
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                max-width: 450px;
                margin: 20px;
                min-height: auto;
            }
            .login-left, .login-right {
                width: 100%;
            }
            .login-left {
                padding: 40px 20px;
            }
            .login-left .logo-icon {
                width: 80px;
                height: 80px;
                font-size: 3.5rem;
            }
            .login-right {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-left">
            <a href="{{ url('/') }}" class="text-white text-decoration-none" style="position: absolute; top: 30px; left: 30px; font-weight: 600; z-index: 10;">
                <i class="fas fa-arrow-left me-2"></i> Về trang chủ
            </a>
            <div class="logo-icon mt-4">
                <i class="fas fa-heartbeat"></i>
            </div>
            <h2>Clinic Management System</h2>
            <p>Hệ thống Quản lý Phòng khám Đa khoa thông minh, tiện lợi và chuyên nghiệp.</p>
        </div>
        
        <div class="login-right">
            <h3>Chào mừng trở lại!</h3>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label for="username" class="form-label">{{ __('Tên đăng nhập') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input id="username" type="text" class="form-control border-start-0 @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus placeholder="Nhập tên đăng nhập của bạn">
                    </div>
                    @error('username')
                        <div class="text-danger mt-2 small fw-bold">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">{{ __('Mật khẩu') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Nhập mật khẩu của bạn">
                    </div>
                    @error('password')
                        <div class="text-danger mt-2 small fw-bold">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary fw-medium" for="remember">
                            {{ __('Ghi nhớ đăng nhập') }}
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none forgot-link" href="{{ route('password.request') }}">
                            {{ __('Quên mật khẩu?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="btn w-100 btn-login">
                    {{ __('ĐĂNG NHẬP') }} <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
