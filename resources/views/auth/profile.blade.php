@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card shadow-sm p-4">
                <div class="position-relative d-inline-block">
                    <img src="{{ $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&size=150&background=random' }}" 
                         class="rounded-circle img-thumbnail" width="150" alt="Avatar">
                    <button class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle">
                        📸
                    </button>
                </div>
                <h4 class="mt-3">{{ $user->full_name }}</h4>
                <p class="text-muted">@ {{ $user->username }}</p>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white font-weight-bold">Thông tin chi tiết</div>
                <div class="card-body">
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Họ và tên (Không thể đổi)</label>
                            <input type="text" class="form-control" value="{{ $user->full_name }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Nhập email của bạn" value="{{ old('email', $user->email) }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại" value="{{ old('phone_number', $user->phone_number) }}">
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection