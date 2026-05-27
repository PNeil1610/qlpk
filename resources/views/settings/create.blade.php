@extends('layouts.app')

@section('title', 'Thêm cấu hình phòng khám')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-plus-circle me-2"></i>Thêm cấu hình mới
        </h3>
        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- ===== LOGO UPLOAD ===== --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-image me-1 text-primary"></i>
                                Logo phòng khám
                                <span class="text-muted small">(PNG, JPG, WEBP — tối đa 2MB)</span>
                            </label>
                            <div class="logo-upload-area" id="logoUploadArea" onclick="document.getElementById('logoInput').click()">
                                <div id="logoPreviewWrap" class="d-none">
                                    <img id="logoPreview" src="" alt="Preview logo" class="logo-preview-img">
                                    <button type="button" class="logo-clear-btn" id="logoClearBtn" title="Xóa ảnh đã chọn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="logoPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <div class="fw-semibold text-muted">Kéo & thả hoặc nhấn để chọn logo</div>
                                    <div class="text-muted small mt-1">PNG, JPG, WEBP, GIF · Tối đa 2MB</div>
                                </div>
                            </div>
                            <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*">
                            @error('logo')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tên phòng khám --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-hospital me-1 text-primary"></i>
                                Tên phòng khám <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="clinic_name"
                                   class="form-control form-control-lg @error('clinic_name') is-invalid @enderror"
                                   value="{{ old('clinic_name') }}"
                                   placeholder="Ví dụ: Phòng khám đa khoa Minh Đức"
                                   maxlength="200">
                            @error('clinic_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            {{-- Điện thoại --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-phone me-1 text-primary"></i>Số điện thoại
                                </label>
                                <input type="text" name="phone_number"
                                       class="form-control @error('phone_number') is-invalid @enderror"
                                       value="{{ old('phone_number') }}"
                                       placeholder="0901 234 567"
                                       maxlength="20">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-1 text-primary"></i>Email
                                </label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}"
                                       placeholder="info@phongkham.com"
                                       maxlength="100">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-1 text-primary"></i>Địa chỉ
                            </label>
                            <input type="text" name="address"
                                   class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address') }}"
                                   placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                                   maxlength="255">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Thông tin thêm --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                Thông tin bổ sung
                                <span class="text-muted small">(giới thiệu, slogan, MST...)</span>
                            </label>
                            <textarea name="additional_info" rows="4"
                                      class="form-control @error('additional_info') is-invalid @enderror"
                                      placeholder="Nhập thông tin bổ sung về phòng khám...">{{ old('additional_info') }}</textarea>
                            @error('additional_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Áp dụng ngay --}}
                        <div class="mb-4 p-3 bg-light rounded">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       name="apply_now" id="apply_now" value="1"
                                       {{ old('apply_now') ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="apply_now">
                                    Áp dụng ngay cấu hình này
                                </label>
                            </div>
                            <small class="text-muted ms-4">
                                Nếu bật, cấu hình hiện tại sẽ bị thu hồi và cấu hình mới này sẽ được áp dụng ngay lập tức.
                            </small>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="fas fa-save me-2"></i>Lưu cấu hình
                            </button>
                            <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-lg">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.logo-upload-area {
    border: 2px dashed #c0cfe0;
    border-radius: 12px;
    padding: 28px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .25s, background .25s;
    background: #f8fbff;
    position: relative;
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.logo-upload-area:hover {
    border-color: #0d6efd;
    background: #eef4ff;
}
.logo-upload-area.drag-over {
    border-color: #0d6efd;
    background: #dbeaff;
}
.logo-preview-img {
    max-height: 100px;
    max-width: 260px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.logo-clear-btn {
    position: absolute;
    top: 8px;
    right: 10px;
    background: #dc3545;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.logo-clear-btn:hover { background: #b02a37; }
</style>

<script>
(function () {
    const input   = document.getElementById('logoInput');
    const area    = document.getElementById('logoUploadArea');
    const preview = document.getElementById('logoPreview');
    const previewWrap = document.getElementById('logoPreviewWrap');
    const placeholder = document.getElementById('logoPlaceholder');
    const clearBtn = document.getElementById('logoClearBtn');

    function showPreview(file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            previewWrap.classList.remove('d-none');
            placeholder.classList.add('d-none');
        };
        reader.readAsDataURL(file);
    }

    input.addEventListener('change', function () {
        if (this.files && this.files[0]) showPreview(this.files[0]);
    });

    clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        input.value = '';
        preview.src = '';
        previewWrap.classList.add('d-none');
        placeholder.classList.remove('d-none');
    });

    // Drag & Drop
    area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('drag-over'); });
    area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
    area.addEventListener('drop', e => {
        e.preventDefault();
        area.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            const dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            showPreview(file);
        }
    });
})();
</script>
@endsection
