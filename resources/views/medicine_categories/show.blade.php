@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 text-primary"><i class="fas fa-eye me-2"></i>Chi Tiết Danh Mục Thuốc</h3>
                <a href="{{ route('medicine_categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold border-bottom py-3">
                    <i class="fas fa-info-circle text-info me-1"></i> Thông tin danh mục
                </div>
                <div class="card-body p-4">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr>
                                    <th width="250" class="bg-light">Mã Loại</th>
                                    <td class="fw-bold text-secondary">{{ $category->category_code ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Tên Nhóm Thuốc / Loại Thuốc</th>
                                    <td class="fw-bold text-dark">{{ $category->category_name }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Mô tả tóm tắt</th>
                                    <td>{{ $category->description ?: 'Không có mô tả' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Trạng thái sử dụng</th>
                                    <td>
                                        @if($category->status == 1)
                                            <span class="badge bg-success">Đang dùng (Active)</span>
                                        @else
                                            <span class="badge bg-danger">Tạm khóa (Inactive)</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Ngày tạo</th>
                                    <td>{{ $category->created_at ? $category->created_at->format('d/m/Y H:i:s') : '---' }}</td>
                                </tr>
                                <tr>
                                    <th class="bg-light">Cập nhật lần cuối</th>
                                    <td>{{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i:s') : '---' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top d-flex gap-2 justify-content-end">
                        <a href="{{ route('medicine_categories.edit', $category->id) }}" class="btn btn-primary px-4 shadow-sm">
                            <i class="fas fa-edit"></i> Danh Mục
                        </a>
                        <form action="{{ route('medicine_categories.destroy', $category->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm thuốc này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger shadow-sm px-4">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
