@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Sửa Tên Thuốc: {{ $medicine->medicine_name }}</h3>
                <a href="{{ route('medicines.show', $medicine->id) }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('medicines.update', $medicine->id) }}" method="POST">
                        @csrf
                        @method('PUT')
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
                            <label class="form-label fw-bold">Hướng dẫn sử dụng</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Nhập hướng dẫn sử dụng cụ thể...">{{ $medicine->note }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái kho</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $medicine->status == 1 ? 'selected' : '' }}>Có sẵn</option>
                                <option value="0" {{ $medicine->status == 0 ? 'selected' : '' }}>Tạm ngưng</option>
                                <option value="2" {{ $medicine->status == 2 ? 'selected' : '' }}>Ngừng SX</option>
                                <option value="3" {{ $medicine->status == 3 ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-5">Lưu Thay Đổi Thông Tin Gốc</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection