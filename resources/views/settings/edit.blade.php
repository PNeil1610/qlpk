@extends('layouts.app')

@section('title', 'Sửa cấu hình phòng khám')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-edit"></i> cấu hình phòng khám
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
                    <form action="{{ route('settings.update', $setting->id) }}" method="POST">
                        @csrf @method('PUT')

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
@endsection
