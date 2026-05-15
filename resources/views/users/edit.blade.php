@extends('layouts.app')

@section('title', 'Sửa tài khoản - ' . $user->username)
@section('page_title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center mb-4 mt-2 gap-3">
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="mb-0 fw-bold" style="color: #2d3748;">
                <i class="fas fa-user-edit me-2 text-warning"></i> Sửa Tài Khoản
            </h3>
            <p class="text-muted mb-0 small">Chỉnh sửa thông tin tài khoản <strong>{{ $user->username }}</strong></p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf @method('PUT')

                {{-- Thông tin tài khoản --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-cog me-2"></i>Cấu hình tài khoản</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tên đăng nhập</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->username }}" disabled>
                                <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label fw-semibold">
                                    Vai trò <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ (old('role_id', $userRoleId) == $role->id) ? 'selected' : '' }}>
                                            {{ $role->role_name }} ({{ $role->role_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold">
                                    Trạng thái <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>
                                        ✅ Hoạt động
                                    </option>
                                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>
                                        🔒 Bị khóa
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thông tin cá nhân --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-user me-2 text-primary"></i>Thông tin cá nhân</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label fw-semibold">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="full_name" name="full_name"
                                       value="{{ old('full_name', $user->full_name) }}"
                                       placeholder="Nguyễn Văn A">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email', $user->email) }}"
                                       placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number" name="phone_number"
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="0912345678">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="national_id" class="form-label fw-semibold">Số CCCD / CMND</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                       id="national_id" name="national_id"
                                       value="{{ old('national_id', $user->national_id) }}"
                                       placeholder="012345678901">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                       id="address" name="address"
                                       value="{{ old('address', $user->address) }}"
                                       placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning px-4">
                        <i class="fas fa-save me-2"></i>Lưu thay đổi
                    </button>
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <img src="{{ $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&size=80&background=667eea&color=fff' }}"
                         class="rounded-circle border border-3 mb-3"
                         width="80" height="80" style="object-fit: cover; border-color: #667eea !important;">
                    <h6 class="fw-bold mb-1">{{ $user->full_name }}</h6>
                    <p class="text-muted small mb-2"><i class="fas fa-at me-1"></i>{{ $user->username }}</p>
                    <small class="text-muted">
                        Tạo lúc: {{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}
                    </small>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm mt-3 small">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Lưu ý:</strong> Để đặt lại mật khẩu, vui lòng truy cập trang
                <a href="{{ route('users.show', $user->id) }}" class="alert-link">chi tiết tài khoản</a>.
            </div>
        </div>
    </div>
</div>
@endsection
