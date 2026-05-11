@extends('layouts.app')

@section('content')

    <h3>Danh sách phòng</h3>

    <a href="{{ route('rooms.create') }}"
       class="btn btn-primary mb-3">

        + Thêm phòng

    </a>

    <div class="mb-3">

        <div class="d-flex gap-2">

            <input type="text"
                   id="search"
                   class="form-control"
                   placeholder="Tìm mã phòng hoặc tên phòng...">

            <button type="button"
                    id="resetSearch"
                    class="btn btn-secondary">

                Reset

            </button>

        </div>

    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>Mã phòng</th>
                <th>Tên phòng</th>
                <th>Loại phòng</th>
                <th>Khoa</th>

                <th>Trạng thái</th>
                <th width="220">Hành động</th>

            </tr>

        </thead>

        <tbody id="roomTable">

            @forelse($rooms as $room)

                <tr>

                    <td>{{ $room->room_code }}</td>

                    <td>{{ $room->room_name }}</td>

                    <td>{{ $room->room_type }}</td>

                    <td>{{ $room->department->dept_name ?? '--' }}</td>



                    <td>

                        @switch($room->status)

                            @case(1)
                                <span class="badge bg-success">
                                    Hoạt động
                                </span>
                                @break

                            @case(0)
                                <span class="badge bg-secondary">
                                    Đóng
                                </span>
                                @break

                            @case(2)
                                <span class="badge bg-primary">
                                    Đang sử dụng
                                </span>
                                @break

                            @case(3)
                                <span class="badge bg-warning text-dark">
                                    Bảo trì
                                </span>
                                @break

                        @endswitch

                    </td>

                    <td>

                        <a href="{{ route('rooms.show', $room->id) }}"
                           class="btn btn-info btn-sm">

                            Xem

                        </a>

                        <a href="{{ route('rooms.edit', $room->id) }}"
                           class="btn btn-warning btn-sm">

                            Sửa

                        </a>

                        <a href="{{ route('room_details.create', ['room_id' => $room->id]) }}"
                           class="btn btn-success btn-sm">

                            + Chi tiết

                        </a>

                        <form action="{{ route('rooms.destroy', $room->id) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc muốn xóa không?')">

                                Xóa

                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8"
                        class="text-center text-muted">

                        Không có dữ liệu

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center">

        {{ $rooms->links('pagination::bootstrap-5') }}

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
                            <td colspan="8"
                                class="text-center">
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

                                <td>${r.room_code ?? ''}</td>

                                <td>${r.room_name ?? ''}</td>

                                <td>${r.room_type ?? ''}</td>

                                <td>${r.department?.dept_name ?? '--'}</td>



                                <td>${status}</td>

                                <td>

                                    <a href="/rooms/${r.id}"
                                       class="btn btn-info btn-sm">

                                        Xem

                                    </a>

                                    <a href="/rooms/${r.id}/edit"
                                       class="btn btn-warning btn-sm">

                                        Sửa

                                    </a>

                                    <a href="/room-details/create?room_id=${r.id}"
                                       class="btn btn-success btn-sm">

                                        + Chi tiết

                                    </a>

                                    <form action="/rooms/${r.id}"
                                          method="POST"
                                          style="display:inline;">

                                        <input type="hidden"
                                               name="_token"
                                               value="{{ csrf_token() }}">

                                        <input type="hidden"
                                               name="_method"
                                               value="DELETE">

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Bạn có chắc muốn xóa không?')">

                                            Xóa

                                        </button>

                                    </form>

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