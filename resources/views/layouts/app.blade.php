<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'QL Phòng Khám'))</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/custom-premium-style.css') }}">

    @stack('styles')
</head>

<body>
    <div id="app" class="container-fluid p-0">
        <div class="row g-0">

            <!-- Desktop Sidebar -->
            <div class="col-lg-3 col-xl-2 sidebar d-none d-lg-block">
                @include('partials.sidebar')
            </div>

            <!-- Content Column -->
            <div class="col-lg-9 col-xl-10 col-12 d-flex flex-column min-vh-100">

                <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
                    <div class="container-fluid px-4">

                        <!-- Hamburger Toggle for Mobile Sidebar Drawer -->
                        <button class="btn btn-link text-dark d-lg-none me-3 p-0 border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebarOffcanvas" aria-controls="mobileSidebarOffcanvas" style="font-size: 1.25rem; text-decoration: none;">
                            <i class="fas fa-bars"></i>
                        </button>

                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav me-auto">
                            </ul>

                            <ul class="navbar-nav ms-auto">
                                @guest
                                    @if (Route::has('login'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                        </li>
                                    @endif

                                    @if (Route::has('register'))
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                        </li>
                                    @endif
                                @else
                                    <li class="nav-item d-flex align-items-center">
                                        <a class="nav-link d-flex align-items-center" href="{{ url('/profile') }}">
                                            <img src="{{ Auth::user()->avatar_url ? asset(Auth::user()->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name) . '&background=random' }}"
                                                alt="Avatar" width="32" height="32" class="rounded-circle me-2 border">
                                            <span class="fw-bold text-dark">{{ Auth::user()->full_name }}</span>
                                        </a>
                                    </li>

                                    <li class="nav-item d-flex align-items-center ms-3">
                                        <a class="btn btn-outline-danger btn-sm" href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Đăng xuất
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                @endguest
                            </ul>
                        </div>
                    </div>
                </nav>

                <main class="p-4 flex-grow-1">
                    <h3>@yield('page_title')</h3>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('resent'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0">
                            <i class="fas fa-check-circle me-2"></i>{{ __('A fresh verification link has been sent to your email address.') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0">
                            <i class="fas fa-info-circle me-2"></i>{{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>

            </div>
        </div>
    </div>

    <!-- Mobile Sidebar Drawer (Offcanvas) -->
    <div class="offcanvas offcanvas-start offcanvas-sidebar d-lg-none" tabindex="-1" id="mobileSidebarOffcanvas" aria-labelledby="mobileSidebarOffcanvasLabel">
        <div class="offcanvas-header justify-content-end p-0 border-0">
            <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            @include('partials.sidebar')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    @stack('scripts')
</body>

</html>