@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 text-primary"><i class="fas fa-edit me-2"></i>Sửa Danh Mục Thuốc</h3>
                <a href="{{ route('medicine_categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold border-bottom py-3">
                    <i class="fas fa-info-circle text-info me-1"></i> Thông tin danh mục
                </div>
                <div class="card-body p-4">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('medicine_categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label text-danger fw-bold">Tên nhóm thuốc / Loại thuốc *</label>
                            <input type="text" name="category_name" class="form-control form-control-lg" required placeholder="VD: Nhóm Kháng sinh, Nhóm Vitamin..." value="{{ old('category_name', $category->category_name) }}">
                            <small class="text-muted">Tên danh mục nên ngắn gọn và dễ hiểu.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả tóm tắt</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Nhập công dụng chung hoặc ghi chú cho nhóm thuốc này...">{{ old('description', $category->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái sử dụng</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', $category->status) == 1 ? 'selected' : '' }}>Đang dùng (Active)</option>
                                <option value="0" {{ old('status', $category->status) == 0 ? 'selected' : '' }}>Tạm khóa (Inactive)</option>
                            </select>
                        </div>

                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                <i class="fas fa-save me-1"></i> Cập nhật Danh Mục
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
