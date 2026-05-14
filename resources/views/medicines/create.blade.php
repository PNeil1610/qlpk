@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Khởi Tạo Thuốc Mới</h3>
                <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">Thông tin định danh thuốc</div>
                <div class="card-body p-4">
                    <form action="{{ route('medicines.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên thuốc / Vật tư *</label>
                            <input type="text" name="medicine_name" class="form-control" required placeholder="VD: Paracetamol 500mg">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhóm thuốc *</label>
                            <select name="medicine_category_id" class="form-select" required>
                                <option value="">-- Chọn nhóm thuốc --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Hướng dẫn sử dụng</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Nhập hướng dẫn sử dụng cụ thể..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái kho</label>
                            <select name="status" class="form-select">
                                <option value="1">Đang kinh doanh (Available)</option>
                                <option value="0">Tạm ngưng</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5">Tạo Thuốc & Chuyển tới Bảng Giá</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection