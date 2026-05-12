@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Sửa Thông Tin: {{ $medicine->medicine_name }}</h3>
        <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">Quay lại</a>
    </div>

    <form action="{{ route('medicines.update', $medicine->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold py-3">Thông tin cơ bản</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên thuốc *</label>
                            <input type="text" name="medicine_name" class="form-control" value="{{ $medicine->medicine_name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhóm thuốc *</label>
                            <select name="medicine_category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ $medicine->medicine_category_id == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3">{{ $medicine->note }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái kho</label>
                            <select name="status" class="form-select border-primary">
                                <option value="1" {{ $medicine->status == 1 ? 'selected' : '' }}>Có sẵn</option>
                                <option value="0" {{ $medicine->status == 0 ? 'selected' : '' }}>Tạm ngưng</option>
                                <option value="3" {{ $medicine->status == 3 ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white fw-bold py-3 text-success">Giá & Chuyên môn</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Đơn vị tính *</label>
                                <input type="text" name="unit" class="form-control" value="{{ $currentDetail->unit ?? '' }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) *</label>
                                <input type="number" name="price" class="form-control" value="{{ $currentDetail->price ?? 0 }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hoạt chất chính</label>
                            <input type="text" name="active_ingredient" class="form-control" value="{{ $currentDetail->active_ingredient ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Điều kiện bảo quản</label>
                            <input type="text" name="storage_condition" class="form-control" value="{{ $currentDetail->storage_condition ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Số đăng ký</label>
                            <input type="text" name="order_number" class="form-control" value="{{ $currentDetail->order_number ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">Lưu Thay Đổi</button>
        </div>
    </form>
</div>
@endsection