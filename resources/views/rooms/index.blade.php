@extends('layouts.app')

@section('title', 'Danh sách Phòng')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-door-open me-2"></i>Danh sách Phòng</h3>
        <a href="{{ route('rooms.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle"></i>
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
                        <input type="text" id="search" class="form-control border-start-0 ps-0" placeholder="Tìm theo mã hoặc tên phòng...">
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
                            <th scope="col" class="ps-4">Mã phòng</th>
                            <th scope="col">Tên phòng</th>
                            <th scope="col">Loại phòng</th>
                            <th scope="col">Khoa</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4" width="280">Thao tác</th>
                        </tr>
                    </thead>

        <tbody id="roomTable">

            @forelse($rooms as $room)

                <tr>
                    <td class="ps-4 fw-bold text-secondary">{{ $room->room_code }}</td>
                    <td class="fw-bold text-dark">{{ $room->room_name }}</td>
                    <td>{{ $room->room_type }}</td>
                    <td>{{ $room->department->dept_name ?? '--' }}</td>
                    <td class="text-center">

                        @switch($room->status)
                            @case(1)
                                <span class="badge rounded-pill bg-success">Hoạt động</span>
                                @break
                            @case(0)
                                <span class="badge rounded-pill bg-secondary">Đóng</span>
                                @break
                            @case(2)
                                <span class="badge rounded-pill bg-primary">Đang sử dụng</span>
                                @break
                            @case(3)
                                <span class="badge rounded-pill bg-warning text-dark">Bảo trì</span>
                                @break
                        @endswitch
                    </td>
                    <td class="text-center pe-4">
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('room_details.create', ['room_id' => $room->id]) }}" class="btn btn-sm btn-success shadow-sm" title="Thêm Chi tiết">
                                <i class="fas fa-plus-circle"></i>
                            </a>
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-door-closed fs-2 mb-3 mt-2 d-block text-black-50"></i>
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
            {{ $rooms->links() }}
        </div>
    </div>
    </div>
</div>

@endsection

@push('scripts')

<script>

$(document).ready(function () {

    $('#search').on('keyup', function () {

        let keyword = $(this).val();

        $.ajax({

            url: "{{ route('rooms.ajax.search') }}",

            type: "GET",

            data: {
                keyword: keyword
            },

            success: function (data) {

                let html = '';

                if (data.length === 0) {
                    html += `
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-door-closed fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                Không tìm thấy dữ liệu
                            </td>
                        </tr>
                    `;
                } else {

                    data.forEach(function (r) {

                        let status = '';

                        switch(r.status) {

                            case 1:
                                status = '<span class="badge bg-success">Hoạt động</span>';
                                break;

                            case 0:
                                status = '<span class="badge bg-secondary">Đóng</span>';
                                break;

                            case 2:
                                status = '<span class="badge bg-primary">Đang sử dụng</span>';
                                break;

                            case 3:
                                status = '<span class="badge bg-warning text-dark">Bảo trì</span>';
                                break;
                        }

                        html += `
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">${r.room_code ?? ''}</td>
                                <td class="fw-bold text-dark">${r.room_name ?? ''}</td>
                                <td>${r.room_type ?? ''}</td>
                                <td>${r.department?.dept_name ?? '--'}</td>
                                <td class="text-center">${status}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="/rooms/${r.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/rooms/${r.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/room-details/create?room_id=${r.id}" class="btn btn-sm btn-success shadow-sm" title="Thêm Chi tiết">
                                            <i class="fas fa-plus-circle"></i>
                                        </a>
                                        <form action="/rooms/${r.id}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#roomTable').html(html);

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