@extends('layouts.app')

@section('title', 'Đổi mật khẩu')
@section('page_title', '')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-4 gap-3">
                <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-0 fw-bold" style="color: #2d3748;">
                        <i class="fas fa-lock me-2 text-primary"></i> Đổi mật khẩu
                    </h4>
                    <p class="text-muted mb-0 small">Bảo vệ tài khoản bằng mật khẩu mạnh</p>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">

                    {{-- Progress hint --}}
                    <div class="alert alert-info border-0 small mb-4" style="background: #ebf4ff;">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        Mật khẩu mới phải có <strong>ít nhất 6 ký tự</strong>. Sử dụng kết hợp chữ, số và ký tự đặc biệt để tăng bảo mật.
                    </div>

                    <form method="POST" action="{{ route('profile.update_password') }}" id="changePwForm">
                        @csrf

                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-semibold">
                                Mật khẩu hiện tại <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password"
                                       placeholder="Nhập mật khẩu đang dùng hiện tại"
                                       autocomplete="current-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="new_password" class="form-label fw-semibold">
                                Mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control @error('new_password') is-invalid @enderror"
                                       id="new_password" name="new_password"
                                       placeholder="Nhập mật khẩu mới (ít nhất 6 ký tự)"
                                       autocomplete="new-password"
                                       oninput="checkStrength(this.value)">
                                <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Password strength bar --}}
                            <div class="mt-2">
                                <div class="progress" style="height: 5px;">
                                    <div id="strengthBar" class="progress-bar" style="width: 0%; transition: all 0.3s;"></div>
                                </div>
                                <small id="strengthText" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-semibold">
                                Xác nhận mật khẩu mới <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="password"
                                       class="form-control"
                                       id="new_password_confirmation" name="new_password_confirmation"
                                       placeholder="Nhập lại mật khẩu mới"
                                       autocomplete="new-password">
                                <button type="button" class="btn btn-outline-secondary toggle-pw" data-target="new_password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Đổi mật khẩu
                            </button>
                            <a href="{{ route('profile') }}" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle show/hide password
document.querySelectorAll('.toggle-pw').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const targetId = this.getAttribute('data-target');
        const input = document.getElementById(targetId);
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
});

// Password strength checker
function checkStrength(password) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let strength = 0;

    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const levels = [
        { width: '0%', color: '', label: '' },
        { width: '20%', color: 'bg-danger', label: 'Rất yếu' },
        { width: '40%', color: 'bg-warning', label: 'Yếu' },
        { width: '60%', color: 'bg-info', label: 'Trung bình' },
        { width: '80%', color: 'bg-primary', label: 'Mạnh' },
        { width: '100%', color: 'bg-success', label: 'Rất mạnh' },
    ];

    const level = levels[Math.min(strength, 5)];
    bar.style.width = level.width;
    bar.className = `progress-bar ${level.color}`;
    text.textContent = level.label;
    text.className = `small ${strength <= 2 ? 'text-danger' : strength <= 3 ? 'text-warning' : 'text-success'}`;
}
</script>
@endpush
