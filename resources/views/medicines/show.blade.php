@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-file-medical me-2"></i>Chi Tiết Thuốc / Vật Tư
        </h3>
        <div>
            <a href="{{ route('medicines.edit', $medicine->id) }}" class="btn btn-warning shadow-sm me-2">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100 border-top border-primary border-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-capsules text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $medicine->medicine_name }}</h4>
                    <p class="text-muted mb-3">Mã nội bộ: <strong>{{ $medicine->medicine_code }}</strong></p>
                    
                    <div class="mb-3">
                        <span class="badge bg-info text-dark px-3 py-2 fs-6">
                            <i class="fas fa-tags me-1"></i> {{ $medicine->category->category_name ?? 'Chưa phân nhóm' }}
                        </span>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <p class="mb-1 text-muted small">Trạng thái lưu hành:</p>
                        @if($medicine->status == 1)
                            <span class="badge bg-success px-3 py-2">Có sẵn trong kho</span>
                        @elseif($medicine->status == 0)
                            <span class="badge bg-warning text-dark px-3 py-2">Tạm ngừng sử dụng</span>
                        @elseif($medicine->status == 2)
                            <span class="badge bg-secondary px-3 py-2">Ngừng sản xuất</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Hết hàng</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-list-ul text-success me-1"></i> Thông số kỹ thuật & Đơn giá hiện tại
                </div>
                <div class="card-body p-4">
                    @php
                        // Lấy chi tiết đang hoạt động (status = 1)
                        $currentDetail = $medicine->details->where('status', 1)->first();
                    @endphp

                    @if($currentDetail)
                        <div class="row mb-4">
                            <div class="col-sm-4 text-muted">Đơn giá niêm yết:</div>
                            <div class="col-sm-8 text-danger fw-bold fs-4">
                                {{ number_format($currentDetail->price, 0, ',', '.') }} VNĐ
                                <span class="text-muted fs-6 fw-normal">/ {{ $currentDetail->unit }}</span>
                            </div>
                        </div>

                        <div class="row mb-3 border-bottom pb-2">
                            <div class="col-sm-4 text-muted">Hoạt chất chính:</div>
                            <div class="col-sm-8 fw-bold">{{ $currentDetail->active_ingredient ?: 'Chưa cập nhật' }}</div>
                        </div>

                        <div class="row mb-3 border-bottom pb-2">
                            <div class="col-sm-4 text-muted">Số đăng ký (SĐK):</div>
                            <div class="col-sm-8 fw-bold">{{ $currentDetail->order_number ?: 'Chưa cập nhật' }}</div>
                        </div>

                        <div class="row mb-3 border-bottom pb-2">
                            <div class="col-sm-4 text-muted">Điều kiện bảo quản:</div>
                            <div class="col-sm-8 fw-bold">{{ $currentDetail->storage_condition ?: 'Điều kiện thường' }}</div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Thuốc này chưa có dữ liệu chi tiết về giá và đơn vị tính.
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12 text-muted fw-bold mb-2"><i class="fas fa-info-circle me-1"></i> Hướng dẫn sử dụng:</div>
                        <div class="col-12 bg-light p-3 rounded border text-dark">
                            {!! nl2br(e($medicine->note)) ?: '<span class="fst-italic text-muted">Chưa cập nhật hướng dẫn sử dụng.</span>' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection