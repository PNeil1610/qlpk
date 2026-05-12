@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Khai Báo Thuốc Mới</h3>
        <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('medicines.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold border-bottom py-3">
                        <i class="fas fa-pills text-primary me-1"></i> Thông tin cơ bản
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên thuốc / Vật tư *</label>
                            <input type="text" name="medicine_name" class="form-control" required placeholder="VD: Paracetamol 500mg" value="{{ old('medicine_name') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhóm thuốc / Loại danh mục *</label>
                            <select name="medicine_category_id" class="form-select" required>
                                <option value="">-- Chọn nhóm thuốc --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú / Hướng dẫn nhanh</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Ghi chú về thuốc...">{{ old('note') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái kho</label>
                            <select name="status" class="form-select border-primary">
                                <option value="1">Đang kinh doanh (Available)</option>
                                <option value="0">Tạm ngưng</option>
                                <option value="3">Hết hàng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold border-bottom py-3 text-success">
                        <i class="fas fa-tag me-1"></i> Giá bán & Chuyên môn
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Đơn vị tính *</label>
                                <input type="text" name="unit" class="form-control" required placeholder="Viên, Gói, Chai..." value="{{ old('unit') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" required placeholder="0" value="{{ old('price') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hoạt chất chính (Active Ingredient)</label>
                            <input type="text" name="active_ingredient" class="form-control" placeholder="VD: Paracetamol" value="{{ old('active_ingredient') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Điều kiện bảo quản</label>
                            <input type="text" name="storage_condition" class="form-control" placeholder="Nơi khô ráo, dưới 30 độ C..." value="{{ old('storage_condition') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Số đăng ký / Số thứ tự</label>
                            <input type="text" name="order_number" class="form-control" placeholder="VD: VD-1234-20" value="{{ old('order_number') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                <i class="fas fa-save me-1"></i> Lưu Thông Tin Thuốc
            </button>
        </div>
    </form>
</div>
@endsection