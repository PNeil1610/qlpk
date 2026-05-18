<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=Nunito:400,600,700,800" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f4f8 0%, #d7e3fc 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 60px 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            width: 90%;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.5);
            backdrop-filter: blur(10px);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 10px;
            text-shadow: 2px 2px 10px rgba(2, 132, 199, 0.15);
        }

        .error-icon {
            font-size: 5rem;
            color: #38bdf8;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        .error-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .error-desc {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 35px;
            line-height: 1.6;
        }

        .btn-custom-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 10px 20px rgba(2, 132, 199, 0.25);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-custom-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(2, 132, 199, 0.35);
            color: white;
        }

        /* Decorative Background Elements */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.15), rgba(14, 165, 233, 0.05));
            z-index: 0;
            pointer-events: none;
        }
        .shape-1 {
            width: 500px;
            height: 500px;
            top: -150px;
            left: -150px;
        }
        .shape-2 {
            width: 600px;
            height: 600px;
            bottom: -200px;
            right: -100px;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 576px) {
            .error-code { font-size: 6rem; }
            .error-icon { font-size: 4rem; }
            .error-title { font-size: 1.5rem; }
            .error-container { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <!-- Decorative Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-stethoscope"></i>
        </div>
        <div class="error-code">404</div>
        <h1 class="error-title">Không Tìm Thấy Trang</h1>
        <p class="error-desc">
            Rất tiếc, đường dẫn bạn đang truy cập không tồn tại hoặc đã bị di dời khỏi Hệ thống Quản lý Phòng khám.
        </p>
        <a href="{{ url('/') }}" class="btn-custom-primary">
            <i class="fas fa-home"></i> Trở Về Trang Chủ
        </a>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
