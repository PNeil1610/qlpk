@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-info-circle me-2"></i>Danh sách Chi tiết Phòng</h3>
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
                            <th scope="col">Phòng</th>
                            <th scope="col" class="text-center">Tầng</th>
                            <th scope="col" class="text-center">Sức chứa</th>
                            <th scope="col" class="text-center">Diện tích (m2)</th>
                            <th scope="col" class="text-center">Lần dọn dẹp cuối</th>
                            <th scope="col" class="text-center pe-4" width="220">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="roomDetailTable">
                        @foreach($roomDetails as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $item->room->room_code }}</td>
                                <td class="fw-bold text-dark">{{ $item->room->room_name ?? '--' }}</td>
                                <td class="text-center">{{ $item->floor ?? '--' }}</td>
                                <td class="text-center">{{ $item->capacity ?? '--' }}</td>
                                <td class="text-center">{{ $item->area_sqm ?? '--' }}</td>
                                <td class="text-center">{{ $item->last_cleaned_at ? $item->last_cleaned_at->format('d/m/Y H:i') : '--' }}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('room_details.show', $item->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="{{ route('room_details.edit', $item->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                        <form action="{{ route('room_details.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($roomDetails->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Chưa có dữ liệu
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $roomDetails->links() }}
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
            url: "{{ route('room_details.ajax.search') }}",
            type: "GET",
            data: { keyword: keyword },
            success: function (data) {
                let html = '';
                if (data.length === 0) {
                    html += `<tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Không tìm thấy dữ liệu
                                </td>
                            </tr>`;
                } else {
                    data.forEach(function (r) {
                        let roomCode = r.room ? r.room.room_code : '--';
                        let roomName = r.room ? r.room.room_name : '--';
                        let lastCleanedAt = r.last_cleaned_at ? new Date(r.last_cleaned_at).toLocaleString('vi-VN', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'}) : '--';

                        html += `
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">${roomCode}</td>
                                <td class="fw-bold text-dark">${roomName}</td>
                                <td class="text-center">${r.floor ?? '--'}</td>
                                <td class="text-center">${r.capacity ?? '--'}</td>
                                <td class="text-center">${r.area_sqm ?? '--'}</td>
                                <td class="text-center">${lastCleanedAt}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="/room-details/${r.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="/room-details/${r.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                        <form action="/room-details/${r.id}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
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
                $('#roomDetailTable').html(html);
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