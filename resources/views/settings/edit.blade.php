@extends('layouts.app')

@section('title', 'Sửa cấu hình phòng khám')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-edit me-2"></i>Sửa cấu hình phòng khám
        </h3>
        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            @if($setting->status == 1)
            <div class="alert alert-success border-0 shadow-sm mb-3">
                <i class="fas fa-check-circle me-2"></i>
                Đây là cấu hình <strong>đang được áp dụng</strong>. Thay đổi sẽ có hiệu lực ngay lập tức.
            </div>
            @endif

            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <form action="{{ route('settings.update', $setting->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')

                        {{-- ===== LOGO ===== --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-image me-1 text-primary"></i>
                                Logo phòng khám
                                <span class="text-muted small">(PNG, JPG, WEBP — tối đa 2MB)</span>
                            </label>

                            {{-- Hiển thị logo hiện tại --}}
                            @if($setting->logo_path)
                            <div class="current-logo-wrap mb-3" id="currentLogoWrap">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                    <img src="{{ asset('storage/' . $setting->logo_path) }}"
                                         alt="Logo hiện tại"
                                         class="current-logo-thumb">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-success mb-1">
                                            <i class="fas fa-check-circle me-1"></i>Logo hiện tại
                                        </div>
                                        <small class="text-muted">Upload logo mới bên dưới để thay thế, hoặc nhấn "Xóa logo" để bỏ.</small>
                                    </div>
                                    <form action="{{ route('settings.removeLogo', $setting->id) }}" method="POST"
                                          onsubmit="return confirm('Xóa logo phòng khám này?')" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash me-1"></i>Xóa logo
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif

                            {{-- Vùng upload logo mới --}}
                            <div class="logo-upload-area" id="logoUploadArea" onclick="document.getElementById('logoInput').click()">
                                <div id="logoPreviewWrap" class="d-none">
                                    <img id="logoPreview" src="" alt="Preview logo" class="logo-preview-img">
                                    <button type="button" class="logo-clear-btn" id="logoClearBtn" title="Xóa ảnh đã chọn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div id="logoPlaceholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    @if($setting->logo_path)
                                        <div class="fw-semibold text-muted">Kéo & thả hoặc nhấn để <span class="text-primary">thay logo</span></div>
                                    @else
                                        <div class="fw-semibold text-muted">Kéo & thả hoặc nhấn để chọn logo</div>
                                    @endif
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
                                   value="{{ old('clinic_name', $setting->clinic_name) }}"
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
                                       value="{{ old('phone_number', $setting->phone_number) }}"
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
                                       value="{{ old('email', $setting->email) }}"
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
                                   value="{{ old('address', $setting->address) }}"
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
                                      placeholder="Nhập thông tin bổ sung về phòng khám...">{{ old('additional_info', $setting->additional_info) }}</textarea>
                            @error('additional_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Thông tin meta --}}
                        <div class="p-3 bg-light rounded mb-4">
                            <small class="text-muted">
                                <i class="fas fa-history me-1"></i>
                                Tạo lần đầu: <strong>{{ $setting->created_at?->format('d/m/Y H:i') }}</strong>
                                &nbsp;|&nbsp;
                                Cập nhật: <strong>{{ $setting->updated_at?->format('d/m/Y H:i') }}</strong>
                                &nbsp;|&nbsp;
                                Người cập nhật: <strong>{{ $setting->creator?->full_name ?? '---' }}</strong>
                            </small>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
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
.current-logo-thumb {
    height: 64px;
    max-width: 140px;
    object-fit: contain;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    padding: 4px;
    background: #fff;
}
.logo-upload-area {
    border: 2px dashed #c0cfe0;
    border-radius: 12px;
    padding: 24px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .25s, background .25s;
    background: #f8fbff;
    position: relative;
    min-height: 100px;
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
    max-height: 90px;
    max-width: 240px;
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
