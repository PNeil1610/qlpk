@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-pills me-2"></i>Danh mục Loại thuốc</h3>
            <a href="{{ route('medicine_categories.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Thêm Loại Thuốc
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="ps-4">Mã Loại</th>
                                <th scope="col">Tên Nhóm Thuốc / Loại Thuốc</th>
                                <th scope="col">Mô tả tóm tắt</th>
                                <th scope="col" class="text-center">Trạng thái</th>
                                <th scope="col" class="text-center pe-4">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">{{ $item->category_code ?? '---' }}</td>
                                    <td class="fw-bold text-dark">{{ $item->category_name }}</td>
                                    <td class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($item->description, 60, '...') ?: 'Không có mô tả' }}
                                    </td>
                                    <td class="text-center">
                                        @if($item->status == 1)
                                            <span class="badge rounded-pill bg-success">Đang dùng</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger">Đã khóa</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('medicine_categories.edit', $item->id) }}"
                                                class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>
                                            <form action="{{ route('medicine_categories.destroy', $item->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm thuốc này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                                    <i class="fas fa-trash-alt me-1"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                        Chưa có danh mục thuốc nào trên hệ thống.<br>Hãy bấm <strong>"Thêm Loại Thuốc"</strong>
                                        để phân loại tủ thuốc của bạn!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-end">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection