@extends('layouts.app')

@section('content')

    <h3>Chi tiết phòng</h3>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">Mã phòng</th>
                            <td>{{ $room->room_code }}</td>
                        </tr>
                        <tr>
                            <th>Tên phòng</th>
                            <td>{{ $room->room_name }}</td>
                        </tr>
                        <tr>
                            <th>Loại phòng</th>
                            <td>{{ $room->room_type }}</td>
                        </tr>
                        <tr>
                            <th>Khoa</th>
                            <td>{{ $room->department->dept_name ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Tầng</th>
                            <td>{{ $room->floor ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Sức chứa</th>
                            <td>{{ $room->capacity ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @switch($room->status)
                                    @case(1) Hoạt động @break
                                    @case(0) Đóng @break
                                    @case(2) Đang sử dụng @break
                                    @case(3) Bảo trì @break
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ $room->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật</th>
                            <td>{{ $room->updated_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </div>

@endsection