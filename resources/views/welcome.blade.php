<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Phòng khám</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .welcome-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .icon-circle {
            background: #e3f2fd;
            color: #0d6efd;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
    <div class="welcome-card">
        <div class="icon-circle">
            <i class="fas fa-hospital-user"></i>
        </div>
        <h2 class="fw-bold mb-2 text-primary">Phòng Khám Đa Khoa</h2>
        <p class="text-muted mb-4">Hệ thống quản lý thông minh. Vui lòng đăng nhập để sử dụng các tính năng.</p>
        
        @if (Route::has('login'))
            <div class="d-grid gap-3">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-tachometer-alt me-2"></i> Đi tới Trang quản lý
                    </a>
                    <a href="{{ route('logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                       class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập hệ thống
                    </a>
                @endauth
            </div>
        @endif
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
