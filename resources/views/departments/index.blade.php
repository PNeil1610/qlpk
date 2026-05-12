@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-building me-2"></i>Danh sách Khoa</h3>
        <a href="{{ route('departments.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Thêm Khoa
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
                        <input type="text" id="search" class="form-control border-start-0 ps-0" placeholder="Tìm theo mã hoặc tên khoa...">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" id="resetSearch" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-sync-alt me-1"></i> Reset
                    </button>
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
                            <th scope="col" class="ps-4">Mã khoa</th>
                            <th scope="col">Tên khoa</th>
                            <th scope="col">Mô tả</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4" width="250">Thao tác</th>
                        </tr>
                    </thead>

        <tbody id="departmentTable">

            @forelse($departments as $department)

                <tr>

                    <td class="ps-4 fw-bold text-secondary">{{ $department->dept_code }}</td>
                    <td class="fw-bold text-dark">{{ $department->dept_name }}</td>
                    <td class="text-muted">{{ \Illuminate\Support\Str::limit($department->description, 60, '...') ?: 'Không có mô tả' }}</td>
                    <td class="text-center">

                        @if($department->status == 1)

                            <span class="badge rounded-pill bg-success">Hoạt động</span>
                        @elseif($department->status == 0)
                            <span class="badge rounded-pill bg-secondary">Không hoạt động</span>
                        @else
                            <span class="badge rounded-pill bg-warning text-dark">Đang xây dựng</span>
                        @endif

                    </td>

                    <td class="text-center pe-4">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                <i class="fas fa-eye me-1"></i> Xem
                            </a>
                            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                <i class="fas fa-edit me-1"></i> Sửa
                            </a>
                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
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
                        <i class="fas fa-folder-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                        Không có dữ liệu
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-end">
            {{ $departments->links() }}
        </div>
    </div>
    </div>
</div>

@endsection

<!--tìm kiếm ajax-->
@push('scripts')

    <script>

        $(document).ready(function () {

            $('#search').on('keyup', function () {

                let keyword = $(this).val();

                $.ajax({

                    url: "{{ route('departments.ajax.search') }}",

                    type: "GET",

                    data: {
                        keyword: keyword
                    },

                    success: function (data) {

                        let html = '';

                        if (data.length === 0) {
                            html += `
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                        Không tìm thấy dữ liệu
                                    </td>
                                </tr>
                            `;
                        } else {
                            data.forEach(function (d) {
                                let status = '';
                                if(d.status == 1) status = '<span class="badge rounded-pill bg-success">Hoạt động</span>';
                                else if(d.status == 0) status = '<span class="badge rounded-pill bg-secondary">Không hoạt động</span>';
                                else status = '<span class="badge rounded-pill bg-warning text-dark">Đang xây dựng</span>';
                                
                                let desc = d.description ? d.description : 'Không có mô tả';
                                if(desc.length > 60) desc = desc.substring(0, 60) + '...';

                                html += `
                                    <tr>
                                        <td class="ps-4 fw-bold text-secondary">${d.dept_code ?? ''}</td>
                                        <td class="fw-bold text-dark">${d.dept_name ?? ''}</td>
                                        <td class="text-muted">${desc}</td>
                                        <td class="text-center">${status}</td>
                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-2 justify-content-center">
                                                <a href="/departments/${d.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                                    <i class="fas fa-eye me-1"></i> Xem
                                                </a>
                                                <a href="/departments/${d.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                                    <i class="fas fa-edit me-1"></i> Sửa
                                                </a>
                                                <form action="/departments/${d.id}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
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

                        $('#departmentTable').html(html);

                    }

                });

            });

            $('#resetSearch').click(function () {

                $('#search').val('');

                location.reload();

            });

        });

    </script>

@endpush