@extends('layouts.app')

@section('content')

<h3>Chi tiết phòng</h3>

<div class="card">

    <div class="card-body">

        <table class="table table-bordered">

            <tr>

                <th width="250">
                    Mã phòng
                </th>

                <td>
                    {{ $room->room_code }}
                </td>

            </tr>

            <tr>

                <th>
                    Tên phòng
                </th>

                <td>
                    {{ $room->room_name }}
                </td>

            </tr>

            <tr>

                <th>
                    Loại phòng
                </th>

                <td>
                    {{ $room->room_type }}
                </td>

            </tr>

            <tr>

                <th>
                    Khoa
                </th>

                <td>
                    {{ $room->department->dept_name ?? '--' }}
                </td>

            </tr>

            <tr>

                <th>
                    Tầng
                </th>

                <td>
                    {{ $room->floor ?? '--' }}
                </td>

            </tr>

            <tr>

                <th>
                    Sức chứa
                </th>

                <td>
                    {{ $room->capacity ?? '--' }}
                </td>

            </tr>

            <tr>

                <th>
                    Trạng thái
                </th>

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
                            <span class="badge bg-warning">
                                Bảo trì
                            </span>
                            @break

                    @endswitch

                </td>

            </tr>

            <tr>

                <th>
                    Ngày tạo
                </th>

                <td>
                    {{ $room->created_at }}
                </td>

            </tr>

        </table>

        <a href="{{ route('rooms.index') }}"
           class="btn btn-secondary">

            Quay lại

        </a>

    </div>

</div>

@endsection