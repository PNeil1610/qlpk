@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Thêm Phiên bản Đơn giá mới</h3>
                    <span class="text-muted fw-bold">Thuốc: {{ $medicine->medicine_name }}</span>
                </div>
                <a href="{{ route('medicines.details', $medicine->id) }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-1"></i> Lưu ý: Khi thêm phiên bản mới, hệ thống sẽ tự động đặt phiên bản này làm <strong>Mặc định (Đang áp dụng)</strong> và chuyển các phiên bản cũ vào lịch sử.
                    </div>

                    <form action="{{ route('medicines.details.store', $medicine->id) }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Đơn vị tính *</label>
                                <input type="text" name="unit" class="form-control" required placeholder="VD: Viên, Vỉ, Hộp, Ống...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giá bán (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" required min="0" placeholder="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hoạt chất chính</label>
                            <input type="text" name="active_ingredient" class="form-control" placeholder="Nhập hoạt chất (không bắt buộc)...">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Điều kiện bảo quản</label>
                            <input type="text" name="storage_condition" class="form-control" placeholder="VD: Bảo quản nơi khô ráo, tránh ánh nắng...">
                        </div>

                        <div class="text-end pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-check-circle me-1"></i> Tạo và Áp dụng ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
