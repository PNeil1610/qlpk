@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Chào mừng đến với hệ thống quản lý phòng khám</h5>
                </div>

                <div class="card-body text-center p-5">
                    <h3 class="mb-3">Xin chào, {{ Auth::user()->full_name }}!</h3>
                    
                    <p class="text-muted mb-4">
                        Đăng nhập thành công. Bạn có thể cập nhật thông tin cá nhân của mình.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('profile') }}" class="btn btn-secondary">
                            Xem hồ sơ của tôi
                        </a>
                        @if(auth()->user()->hasRole('ADMIN'))
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection