@extends('layouts.app')

@section('title', 'Gửi thông báo')

@section('content')
<style>
.target-section {
    display: none;
    animation: fadeIn 0.2s ease;
}
.target-section.visible { display: block; }
.type-card {
    border: 2px solid #dee2e6;
    border-radius: 12px;
    padding: 16px 20px;
    cursor: pointer;
    transition: all 0.2s;
    background: #fff;
}
.type-card:hover { border-color: #0d6efd; background: #f0f4ff; }
.type-card.selected { border-color: #0d6efd; background: #e8f0fe; }
.type-card input[type=radio] { display: none; }
</style>

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-paper-plane me-2"></i>Gửi thông báo
        </h3>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('notifications.store') }}" method="POST" id="notifForm">
                        @csrf

                        {{-- ĐỐI TƯỢNG NHẬN --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold fs-6 mb-3">
                                <i class="fas fa-users me-1 text-primary"></i>Đối tượng nhận
                            </label>
                            <div class="row g-3">

                                @if($isAdmin)
                                <div class="col-md-4">
                                    <label class="type-card d-flex align-items-center gap-2 w-100"
                                           id="card-all" onclick="selectType('all')">
                                        <input type="radio" name="target_type" value="all">
                                        <div>
                                            <div class="fw-bold">🌐 Tất cả</div>
                                            <small class="text-muted">Toàn bộ nhân viên</small>
                                        </div>
                                    </label>
                                </div>
                                @endif

                                <div class="col-md-4">
                                    <label class="type-card d-flex align-items-center gap-2 w-100"
                                           id="card-role" onclick="selectType('role')">
                                        <input type="radio" name="target_type" value="role">
                                        <div>
                                            <div class="fw-bold">👥 Theo nhóm</div>
                                            <small class="text-muted">
                                                @if($isAdmin) Chọn nhóm bất kỳ @else Nhóm của bạn @endif
                                            </small>
                                        </div>
                                    </label>
                                </div>

                                <div class="col-md-4">
                                    <label class="type-card d-flex align-items-center gap-2 w-100"
                                           id="card-user" onclick="selectType('user')">
                                        <input type="radio" name="target_type" value="user">
                                        <div>
                                            <div class="fw-bold">👤 Cá nhân</div>
                                            <small class="text-muted">Một người cụ thể</small>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @error('target_type')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Chọn role --}}
                        <div id="section-role" class="target-section mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user-tag me-1 text-primary"></i>Chọn nhóm nhận
                            </label>
                            <select name="target_role" id="target_role" class="form-select">
                                <option value="">-- Chọn nhóm --</option>
                                @foreach($allowedRoles as $role)
                                    <option value="{{ $role->role_code }}"
                                        {{ old('target_role') == $role->role_code ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('target_role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Chọn cá nhân --}}
                        <div id="section-user" class="target-section mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-primary"></i>Chọn người nhận
                            </label>
                            <select name="target_user_id" id="target_user_id" class="form-select" style="width:100%;">
                                <option value="">-- Tìm theo tên --</option>
                                @foreach($staffUsers as $u)
                                    <option value="{{ $u['id'] }}"
                                        {{ old('target_user_id') == $u['id'] ? 'selected' : '' }}>
                                        {{ $u['full_name'] }} ({{ $u['role_name'] }})
                                    </option>
                                @endforeach
                            </select>
                            @error('target_user_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        {{-- TIÊU ĐỀ --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-heading me-1 text-primary"></i>Tiêu đề thông báo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title') }}"
                                   placeholder="Ví dụ: Thông báo họp giao ban sáng thứ Hai..."
                                   maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NỘI DUNG --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i>Nội dung chi tiết
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="content" id="content" rows="6"
                                      class="form-control @error('content') is-invalid @enderror"
                                      placeholder="Nhập nội dung thông báo...">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- HẾT HẠN --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hourglass-half me-1 text-warning"></i>
                                Thời gian hết hạn <span class="text-muted small">(để trống = vĩnh viễn)</span>
                            </label>
                            <input type="datetime-local" name="expires_at"
                                   class="form-control @error('expires_at') is-invalid @enderror"
                                   value="{{ old('expires_at') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SUBMIT --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="fas fa-paper-plane me-2"></i>Gửi thông báo
                            </button>
                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary btn-lg">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="old-target-type" value="{{ old('target_type', '') }}">

<script>
function selectType(type) {
    document.querySelectorAll('.type-card').forEach(function(c) {
        c.classList.remove('selected');
    });
    var card = document.getElementById('card-' + type);
    if (card) {
        card.classList.add('selected');
        card.querySelector('input[type=radio]').checked = true;
    }
    document.getElementById('section-role').classList.toggle('visible', type === 'role');
    document.getElementById('section-user').classList.toggle('visible', type === 'user');
}

$(document).ready(function() {
    $('#target_user_id').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Tìm theo tên hoặc chức danh --',
        allowClear: true,
        width: '100%',
    });

    var oldType = document.getElementById('old-target-type').value;
    if (oldType) {
        selectType(oldType);
    }
});
</script>

@endsection
