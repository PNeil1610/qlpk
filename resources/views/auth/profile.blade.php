@extends('layouts.app')

@section('title', 'Trang cá nhân')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-4 text-center">
            <div class="card shadow-sm p-4 mb-3">
                <div class="position-relative d-inline-block">
                    <img src="{{ $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&size=150&background=667eea&color=fff' }}" 
                         class="rounded-circle img-thumbnail" width="150" alt="Avatar">
                </div>
                <h4 class="mt-3">{{ $user->full_name }}</h4>
                <p class="text-muted mb-2">@ {{ $user->username }}</p>
                
                <div class="mt-1">
                    @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                            <span class="badge bg-info text-dark border">{{ $role->role_name }}</span>
                        @endforeach
                    @else
                        <span class="badge bg-secondary">Chưa cấp quyền</span>
                    @endif
                </div>
            </div>

            {{-- Nút đổi mật khẩu --}}
            <div class="card shadow-sm border-0 border-start border-primary border-3">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="mb-0 fw-semibold small">Bảo mật tài khoản</p>
                            <small class="text-muted">Đổi mật khẩu của bạn</small>
                        </div>
                        <a href="{{ route('profile.change_password') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-lock me-1"></i> Đổi mật khẩu
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Thông tin chi tiết</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Họ và tên (Không thể đổi)</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->full_name }}" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control profile-input @error('email') is-invalid @enderror" id="email" name="email" placeholder="Nhập email của bạn" value="{{ old('email', $user->email) }}" disabled>
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control profile-input @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" placeholder="Nhập số điện thoại" value="{{ old('phone_number', $user->phone_number) }}" disabled>
                            @error('phone_number')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control profile-input @error('address') is-invalid @enderror" id="address" name="address" placeholder="Nhập địa chỉ của bạn" value="{{ old('address', $user->address) }}" disabled>
                            @error('address')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="national_id" class="form-label">Số CCCD / CMND</label>
                            <input type="text" class="form-control profile-input @error('national_id') is-invalid @enderror" id="national_id" name="national_id" placeholder="Nhập số CCCD/CMND" value="{{ old('national_id', $user->national_id) }}" disabled>
                            @error('national_id')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                        
                        <div class="d-flex gap-2 mt-4">
                            <button type="button" class="btn btn-primary" id="btn-edit">
                                <i class="fas fa-edit me-1"></i> Chỉnh sửa thông tin
                            </button>
                            <button type="submit" class="btn btn-success d-none" id="btn-save">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                            <button type="button" class="btn btn-secondary d-none" id="btn-cancel">
                                Hủy
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-outline-secondary" id="btn-back">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnEdit = document.getElementById('btn-edit');
        const btnSave = document.getElementById('btn-save');
        const btnCancel = document.getElementById('btn-cancel');
        const btnBack = document.getElementById('btn-back');
        const inputs = document.querySelectorAll('.profile-input');
        
        // Bật chế độ chỉnh sửa
        function enableEditMode() {
            inputs.forEach(input => input.removeAttribute('disabled'));
            btnEdit.classList.add('d-none');
            btnSave.classList.remove('d-none');
            btnCancel.classList.remove('d-none');
            btnBack.classList.add('d-none');
        }

        // Tắt chế độ chỉnh sửa
        function disableEditMode() {
            inputs.forEach(input => input.setAttribute('disabled', 'disabled'));
            btnEdit.classList.remove('d-none');
            btnSave.classList.add('d-none');
            btnCancel.classList.add('d-none');
            btnBack.classList.remove('d-none');
            
            // Xóa các lỗi hiển thị nếu có khi hủy
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        }

        btnEdit.addEventListener('click', enableEditMode);
        
        btnCancel.addEventListener('click', function() {
            disableEditMode();
            // Khôi phục giá trị cũ (cơ bản reload lại trang hoặc dùng value)
            window.location.reload();
        });

        // Nếu có lỗi validation (form submit bị lỗi), tự động bật chế độ chỉnh sửa
        @if($errors->any())
            enableEditMode();
        @endif
        
        // Trước khi submit phải xóa disabled để data được gửi đi
        document.querySelector('form').addEventListener('submit', function() {
            inputs.forEach(input => input.removeAttribute('disabled'));
        });
    });
</script>
@endpush
@endsection