@extends('layouts.app')

@section('title', 'Chi tiết tài khoản - ' . $user->username)
@section('page_title', '')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center mb-4 mt-2 gap-3">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h3 class="mb-0 fw-bold" style="color: #2d3748;">
                <i class="fas fa-user-circle me-2 text-primary"></i> Chi tiết Tài khoản
            </h3>
            <p class="text-muted mb-0 small">Thông tin đầy đủ của tài khoản <strong>{{ $user->username }}</strong></p>
        </div>
    </div>


    @if(session('temp_password_info'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-start border-warning border-4" role="alert">
            <i class="fas fa-key me-2"></i> {!! session('temp_password_info') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Profile Card --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center" style="overflow: hidden;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 80px;"></div>
                <div class="card-body pt-0">
                    <div class="position-relative d-inline-block" style="margin-top: -40px;">
                        <img src="{{ $user->avatar_url ? asset($user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($user->full_name) . '&size=80&background=667eea&color=fff' }}"
                             class="rounded-circle border border-3 border-white shadow"
                             width="80" height="80" style="object-fit: cover;">
                    </div>
                    <h5 class="fw-bold mt-2 mb-1">{{ $user->full_name }}</h5>
                    <p class="text-muted mb-2"><i class="fas fa-at me-1"></i>{{ $user->username }}</p>

                    <div class="mb-3">
                        @forelse($user->roles as $role)
                            @php
                                $roleColors = [
                                    'ADMIN' => 'danger', 'DOCTOR' => 'primary',
                                    'RECEPTIONIST' => 'info', 'CASHIER' => 'success', 'PATIENT' => 'secondary',
                                ];
                                $color = $roleColors[$role->role_code] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $color }} px-3 py-2">{{ $role->role_name }}</span>
                        @empty
                            <span class="badge bg-light text-dark border">Chưa cấp vai trò</span>
                        @endforelse
                    </div>

                    @if($user->status == 1)
                        <span class="badge rounded-pill bg-success px-3 py-2">
                            <i class="fas fa-circle me-1" style="font-size:0.6rem;"></i> Đang hoạt động
                        </span>
                    @else
                        <span class="badge rounded-pill bg-danger px-3 py-2">
                            <i class="fas fa-circle me-1" style="font-size:0.6rem;"></i> Bị khóa
                        </span>
                    @endif
                </div>

                <div class="card-footer bg-white border-0 pb-3">
                    @if($user->last_login_at)
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Đăng nhập lần cuối: {{ \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y H:i') }}
                        </small>
                    @else
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>Chưa đăng nhập lần nào</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info & Actions --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>Thông tin cá nhân</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Email</label>
                            <p class="mb-0">{{ $user->email ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Số điện thoại</label>
                            <p class="mb-0">{{ $user->phone_number ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Số CCCD / CMND</label>
                            <p class="mb-0">{{ $user->national_id ?: '—' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold text-uppercase">Địa chỉ</label>
                            <p class="mb-0">{{ $user->address ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reset Password --}}
            <div class="card shadow-sm border-0 border-start border-warning border-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-key me-2 text-warning"></i> Đặt lại mật khẩu tạm thời
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Đặt lại mật khẩu tạm thời cho người dùng. Hệ thống sẽ hiển thị mật khẩu mới sau khi xác nhận. Người dùng cần đổi mật khẩu sau khi đăng nhập.
                    </p>
                    <form action="{{ route('users.reset_password', $user->id) }}" method="POST" id="resetPwForm">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Mật khẩu tạm mới <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('new_temp_password') is-invalid @enderror"
                                           name="new_temp_password" id="new_temp_password"
                                           placeholder="Nhập mật khẩu tạm mới (≥6 ký tự)">
                                    <button type="button" class="btn btn-outline-secondary" id="genNewPass" title="Tạo ngẫu nhiên">
                                        <i class="fas fa-random"></i>
                                    </button>
                                    @error('new_temp_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning w-100"
                                        onclick="return confirm('Xác nhận đặt lại mật khẩu cho {{ $user->username }}?')">
                                    <i class="fas fa-sync me-1"></i> Đặt lại
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if($user->id !== auth()->id())
            <div class="card shadow-sm border-0 border-start border-danger border-3 mt-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Vùng nguy hiểm</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-0 small text-muted">Xóa vĩnh viễn tài khoản {{ $user->username }} khỏi hệ thống. Hành động này không thể hoàn tác.</p>
                    </div>
                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ $user->username }}? Hành động này không thể hoàn tác!')">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm ms-3 text-nowrap">
                            <i class="fas fa-trash"></i> tài khoản
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('genNewPass').addEventListener('click', function () {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$!';
    let pass = '';
    for (let i = 0; i < 10; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('new_temp_password').value = pass;
});
</script>
@endpush
