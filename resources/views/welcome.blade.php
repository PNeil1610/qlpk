<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Phòng khám - Trang chủ</title>
    
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
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Styling */
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 15px 0;
        }
        .navbar-brand {
            font-weight: 800;
            color: #0369a1 !important;
            font-size: 1.5rem;
            letter-spacing: 0.5px;
        }
        .navbar-brand i {
            color: #ef4444;
        }

        /* Hero Section */
        .hero-section {
            flex-grow: 1;
            display: flex;
            align-items: center;
            padding: 60px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-bg-shapes {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.1), rgba(14, 165, 233, 0.05));
        }
        .shape-1 { width: 600px; height: 600px; top: -200px; right: -100px; }
        .shape-2 { width: 400px; height: 400px; bottom: -100px; left: -100px; }
        
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .hero-title span {
            color: #0284c7;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #475569;
            margin-bottom: 40px;
            font-weight: 400;
            line-height: 1.6;
            max-width: 600px;
        }

        /* Action Buttons */
        .btn-custom-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            border: none;
            padding: 14px 35px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.3);
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        .btn-custom-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(2, 132, 199, 0.4);
            color: white;
        }
        
        /* Features Box */
        .feature-box {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .feature-box:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background: #e0f2fe;
            color: #0284c7;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }
        .feature-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
        }
        .feature-desc {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 0;
        }

        /* Footer */
        .footer {
            background: white;
            padding: 25px 0;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            color: #64748b;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
            .hero-content { text-align: center; }
            .hero-subtitle { margin: 0 auto 30px; }
            .features-container { margin-top: 50px; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
                <i class="fas fa-heartbeat"></i> QLPK SYSTEM
            </a>
            <div class="d-flex ms-auto">
                <!-- Nút bấm đã được chuyển xuống phần thân trang -->
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
        </div>
        
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="pe-lg-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3 fw-bold border border-primary-subtle">
                            <i class="fas fa-star text-warning me-1"></i> Giải pháp quản lý y tế toàn diện
                        </span>
                        <h1 class="hero-title">
                            Nâng tầm <br><span>Quản lý Phòng khám</span> của bạn
                        </h1>
                        <p class="hero-subtitle">
                            Hệ thống thông minh giúp tối ưu hóa quy trình khám bệnh, quản lý bệnh án điện tử, toa thuốc và thanh toán một cách dễ dàng, chuyên nghiệp.
                        </p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/home') }}" class="btn btn-custom-primary">
                                        Vào Trang Quản Lý <i class="fas fa-arrow-right"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-custom-primary">
                                        Bắt Đầu Đăng Nhập <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 features-container">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="feature-box">
                                <div class="feature-icon"><i class="fas fa-laptop-medical"></i></div>
                                <h3 class="feature-title">Hồ Sơ Điện Tử (EMR)</h3>
                                <p class="feature-desc">Quản lý toàn bộ hồ sơ y tế, lịch sử khám và toa thuốc bệnh nhân một cách an toàn và truy xuất nhanh chóng.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-box">
                                <div class="feature-icon"><i class="fas fa-pills"></i></div>
                                <h3 class="feature-title">Quản Lý Nhà Thuốc</h3>
                                <p class="feature-desc">Kiểm soát danh mục thuốc, tự động tính toán tổng số lượng thuốc cần dùng dựa trên liều lượng bác sĩ kê.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-box">
                                <div class="feature-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                <h3 class="feature-title">Thanh Toán Nhanh</h3>
                                <p class="feature-desc">Tự động tổng hợp chi phí khám và tiền thuốc. Quản lý biên lai hóa đơn chính xác, minh bạch.</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="feature-box">
                                <div class="feature-icon"><i class="fas fa-chart-pie"></i></div>
                                <h3 class="feature-title">Hệ Thống Báo Cáo</h3>
                                <p class="feature-desc">Theo dõi luồng bệnh nhân, báo cáo doanh thu và đánh giá hiệu suất của từng phòng ban liên tục.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Hệ thống Quản lý Phòng khám Đa khoa. Tất cả các quyền được bảo lưu.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
