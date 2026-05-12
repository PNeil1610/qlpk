@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-pills me-2"></i>Danh mục Loại thuốc</h3>
            <a href="{{ route('medicine_categories.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Thêm Loại Thuốc
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-3">
                <div class="row">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Tìm theo mã hoặc tên loại thuốc...">
                        </div>
                    </div>
                </div>
            </div>
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
                                            <a href="{{ route('medicine_categories.show', $item->id) }}"
                                                class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                                <i class="fas fa-eye me-1"></i> Xem
                                            </a>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        let keyword = $(this).val();
        
        $.ajax({
            url: "{{ route('medicine_categories.ajax.search') }}",
            type: "GET",
            data: { keyword: keyword },
            success: function(data) {
                let html = '';
                
                if(data.length === 0) {
                    html = `<tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Không tìm thấy loại thuốc nào phù hợp.
                                </td>
                            </tr>`;
                } else {
                    data.forEach(function(item) {
                        let statusBadge = item.status == 1 
                            ? '<span class="badge rounded-pill bg-success">Đang dùng</span>'
                            : '<span class="badge rounded-pill bg-danger">Đã khóa</span>';
                        
                        let desc = item.description ? item.description : 'Không có mô tả';
                        if (desc.length > 60) desc = desc.substring(0, 60) + '...';

                        let code = item.category_code ? item.category_code : '---';

                        html += `
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">${code}</td>
                            <td class="fw-bold text-dark">${item.category_name}</td>
                            <td class="text-muted">${desc}</td>
                            <td class="text-center">${statusBadge}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="/medicine-categories/${item.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                        <i class="fas fa-eye me-1"></i> Xem
                                    </a>
                                    <a href="/medicine-categories/${item.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                        <i class="fas fa-edit me-1"></i> Sửa
                                    </a>
                                    <form action="/medicine-categories/${item.id}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhóm thuốc này không?');">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                            <i class="fas fa-trash-alt me-1"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                }
                
                $('tbody').html(html);
            }
        });
    });
});
</script>
@endpush