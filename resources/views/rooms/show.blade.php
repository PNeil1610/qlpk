@extends('layouts.app')

@section('content')

    <h3>Chi tiết phòng</h3>

    <div class="card">

        <div class="card-body">

            <p>
                <strong>Mã phòng:</strong>
                {{ $room->room_code }}
            </p>

            <p>
                <strong>Tên phòng:</strong>
                {{ $room->room_name }}
            </p>

            <p>
                <strong>Loại phòng:</strong>
                {{ $room->room_type }}
            </p>

            <p>
                <strong>Khoa:</strong>
                {{ $room->department->dept_name ?? '--' }}
            </p>

            <p>
                <strong>Tầng:</strong>
                {{ $room->floor ?? '--' }}
            </p>

            <p>
                <strong>Sức chứa:</strong>
                {{ $room->capacity ?? '--' }}
            </p>

            <p>
                <strong>Trạng thái:</strong>

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

            </p>

            <p>
                <strong>Ngày tạo:</strong>
                {{ $room->created_at }}
            </p>

            <p>
                <strong>Cập nhật:</strong>
                {{ $room->updated_at }}
            </p>

        </div>

    </div>

    <a href="{{ route('rooms.index') }}"
       class="btn btn-secondary mt-3">

        Quay lại

    </a>

@endsection