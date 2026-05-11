@extends('layouts.app')

@section('content')
    <div class="mb-3">
        <h3>Danh sách chi tiết phòng</h3>
    </div>

    <div class="mb-3">
        <div class="d-flex gap-2">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã phòng hoặc tên phòng...">
            <button type="button" id="resetSearch" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Mã phòng</th>
                            <th>Phòng</th>
                            <th>Tầng</th>
                            <th>Sức chứa</th>
                            <th>Diện tích (m2)</th>
                            <th>Lần dọn dẹp cuối</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="roomDetailTable">
                        @foreach($roomDetails as $item)
                            <tr>
                                <td>{{ $item->room->room_code }}</td>
                                <td>{{ $item->room->room_name ?? '--' }}</td>
                                <td>{{ $item->floor ?? '--' }}</td>
                                <td>{{ $item->capacity ?? '--' }}</td>
                                <td>{{ $item->area_sqm ?? '--' }}</td>
                                <td>{{ $item->last_cleaned_at ? $item->last_cleaned_at->format('d/m/Y H:i') : '--' }}</td>
                                <td>
                                    <a href="{{ route('room_details.show', $item->id) }}" class="btn btn-info btn-sm">Xem</a>
                                    <a href="{{ route('room_details.edit', $item->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                    <form action="{{ route('room_details.destroy', $item->id) }}" method="POST"
                                        class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($roomDetails->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $roomDetails->links() }}
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
                    html += `<tr><td colspan="7" class="text-center">Không tìm thấy dữ liệu</td></tr>`;
                } else {
                    data.forEach(function (r) {
                        let roomCode = r.room ? r.room.room_code : '--';
                        let roomName = r.room ? r.room.room_name : '--';
                        let lastCleanedAt = r.last_cleaned_at ? new Date(r.last_cleaned_at).toLocaleString('vi-VN', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'}) : '--';

                        html += `
                            <tr>
                                <td>${roomCode}</td>
                                <td>${roomName}</td>
                                <td>${r.floor ?? '--'}</td>
                                <td>${r.capacity ?? '--'}</td>
                                <td>${r.area_sqm ?? '--'}</td>
                                <td>${lastCleanedAt}</td>
                                <td>
                                    <a href="/room-details/${r.id}" class="btn btn-info btn-sm">Xem</a>
                                    <a href="/room-details/${r.id}/edit" class="btn btn-warning btn-sm">Sửa</a>
                                    <form action="/room-details/${r.id}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
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