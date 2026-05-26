<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
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
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(26, 41, 128, 0.05);
            max-width: 600px;
            width: 90%;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.6);
            backdrop-filter: blur(15px);
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1A2980 0%, #26D0CE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 10px;
            letter-spacing: -2px;
        }

        .error-icon {
            font-size: 5rem;
            background: linear-gradient(135deg, #1A2980 0%, #26D0CE 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        .error-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .error-desc {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 35px;
            line-height: 1.6;
            font-weight: 500;
        }

        .btn-group-custom {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-custom-primary {
            background: linear-gradient(135deg, #1A2980 0%, #26D0CE 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(26, 41, 128, 0.2);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-custom-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 41, 128, 0.3);
            color: white;
        }

        .btn-custom-secondary {
            background: #ffffff;
            color: #1A2980;
            border: 1px solid #1A2980;
            padding: 12px 28px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-custom-secondary:hover {
            transform: translateY(-2px);
            background: #f1f5f9;
            color: #1A2980;
        }

        /* Decorative Background Shapes */
        .bg-shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(26, 41, 128, 0.1), rgba(38, 208, 206, 0.05));
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
            50% { transform: scale(1.08); opacity: 0.85; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 576px) {
            .error-code { font-size: 5.5rem; }
            .error-icon { font-size: 3.5rem; }
            .error-title { font-size: 1.4rem; }
            .error-container { padding: 40px 20px; }
            .btn-group-custom {
                flex-direction: column;
                gap: 10px;
            }
            .btn-custom-primary, .btn-custom-secondary {
                width: 100%;
                justify-content: center;
            }
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
        <div class="btn-group-custom">
            <a href="javascript:history.back()" class="btn-custom-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại
            </a>
            <a href="{{ url('/') }}" class="btn-custom-primary">
                <i class="fas fa-home"></i> Về Trang Chủ
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
