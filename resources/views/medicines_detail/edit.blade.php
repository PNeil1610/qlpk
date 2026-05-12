@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Sửa Chi tiết & Đơn giá</h3>
                    <span class="text-muted fw-bold">Thuốc: {{ $detail->medicine->medicine_name }}</span>
                </div>
                <a href="{{ route('medicines.details', $detail->medicine_id) }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('medicines.details.update', $detail->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Đơn vị tính *</label>
                                <input type="text" name="unit" class="form-control" value="{{ $detail->unit }}" required placeholder="VD: Viên, Vỉ, Hộp...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giá bán (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" value="{{ $detail->price }}" required min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hoạt chất chính</label>
                            <input type="text" name="active_ingredient" class="form-control" value="{{ $detail->active_ingredient }}" placeholder="Nhập hoạt chất (nếu có)...">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Điều kiện bảo quản</label>
                            <input type="text" name="storage_condition" class="form-control" value="{{ $detail->storage_condition }}" placeholder="VD: Bảo quản dưới 30 độ C...">
                        </div>

                        @if($detail->status == 1)
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-info-circle me-1"></i> Đây là phiên bản đang được áp dụng mặc định cho thuốc này.
                            </div>
                        @endif

                        <div class="text-end pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-5 shadow-sm">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
