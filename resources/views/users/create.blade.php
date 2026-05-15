@extends('layouts.app')

@section('title', 'Tạo tài khoản mới')
@section('page_title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center mb-4 mt-2 gap-3">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="mb-0 fw-bold" style="color: #2d3748;">
                <i class="fas fa-user-plus me-2 text-primary"></i> Tạo Tài Khoản Mới
            </h3>
            <p class="text-muted mb-0 small">Điền đầy đủ thông tin để tạo tài khoản và cấp mật khẩu tạm thời</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- Form chính --}}
        <div class="col-md-8">
            <form action="{{ route('users.store') }}" method="POST" id="createUserForm">
                @csrf

                {{-- Thông tin đăng nhập --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0"><i class="fas fa-key me-2"></i>Thông tin đăng nhập</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-semibold">
                                    Tên đăng nhập <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       id="username" name="username"
                                       value="{{ old('username') }}"
                                       placeholder="vd: nguyenvana"
                                       autocomplete="off">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Chỉ dùng chữ thường, số, dấu gạch dưới. Không thể thay đổi sau khi tạo.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label fw-semibold">
                                    Vai trò <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">-- Chọn vai trò --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->role_name }} ({{ $role->role_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="temp_password" class="form-label fw-semibold">
                                    Mật khẩu tạm thời <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('temp_password') is-invalid @enderror"
                                           id="temp_password" name="temp_password"
                                           value="{{ old('temp_password') }}"
                                           placeholder="Nhập mật khẩu tạm (≥6 ký tự)">
                                    <button type="button" class="btn btn-outline-secondary" id="generatePass" title="Tạo mật khẩu ngẫu nhiên">
                                        <i class="fas fa-random"></i>
                                    </button>
                                </div>
                                @error('temp_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-warning me-1"></i>
                                    Người dùng sẽ cần đổi mật khẩu này sau khi đăng nhập lần đầu.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thông tin cá nhân --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 text-dark"><i class="fas fa-user me-2 text-primary"></i>Thông tin cá nhân</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="full_name" class="form-label fw-semibold">
                                    Họ và tên <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('full_name') is-invalid @enderror"
                                       id="full_name" name="full_name"
                                       value="{{ old('full_name') }}"
                                       placeholder="Nguyễn Văn A">
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email"
                                       value="{{ old('email') }}"
                                       placeholder="email@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number" name="phone_number"
                                       value="{{ old('phone_number') }}"
                                       placeholder="0912345678">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="national_id" class="form-label fw-semibold">Số CCCD / CMND</label>
                                <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                       id="national_id" name="national_id"
                                       value="{{ old('national_id') }}"
                                       placeholder="012345678901">
                                @error('national_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">Địa chỉ</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror"
                                       id="address" name="address"
                                       value="{{ old('address') }}"
                                       placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Tạo tài khoản
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>

        {{-- Sidebar hướng dẫn --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white p-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-lightbulb me-2"></i>Hướng dẫn</h6>
                    <ul class="list-unstyled small mb-0" style="line-height: 2;">
                        <li><i class="fas fa-check-circle me-2 opacity-75"></i>Tên đăng nhập <strong>không thể thay đổi</strong> sau khi tạo</li>
                        <li><i class="fas fa-check-circle me-2 opacity-75"></i>Mật khẩu tạm phải <strong>ít nhất 6 ký tự</strong></li>
                        <li><i class="fas fa-check-circle me-2 opacity-75"></i>Hệ thống sẽ hiển thị mật khẩu tạm <strong>sau khi tạo thành công</strong></li>
                        <li><i class="fas fa-check-circle me-2 opacity-75"></i>Người dùng có thể <strong>tự đổi mật khẩu</strong> tại trang cá nhân</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-shield-alt me-2 text-warning"></i>Vai trò hệ thống</h6>
                    @foreach($roles as $role)
                    @php
                        $roleColors = [
                            'ADMIN' => ['danger', 'Quản trị viên toàn hệ thống'],
                            'DOCTOR' => ['primary', 'Bác sĩ khám bệnh'],
                            'RECEPTIONIST' => ['info', 'Lễ tân tiếp nhận'],
                            'CASHIER' => ['success', 'Thu ngân hóa đơn'],
                            'PATIENT' => ['secondary', 'Bệnh nhân'],
                        ];
                        $rd = $roleColors[$role->role_code] ?? ['dark', ''];
                    @endphp
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-{{ $rd[0] }} me-2">{{ $role->role_code }}</span>
                        <small class="text-muted">{{ $rd[1] ?: $role->role_name }}</small>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('generatePass').addEventListener('click', function () {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$!';
    let pass = '';
    for (let i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('temp_password').value = pass;
});
</script>
@endpush
