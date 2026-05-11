@extends('layouts.app')

@section('content')

    <h3>Danh sách phòng khám của phiên khám</h3>

    <!-- <a href="{{ route('consultation_rooms.create') }}" class="btn btn-primary mb-3">

            + Thêm phòng cho phiên khám

        </a> -->

    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>Phiên khám</th>
                <th>Bệnh nhân</th>
                <th>Phòng</th>
                <th>Ghi chú</th>
                <th>Trạng thái</th>
                <th width="220">Hành động</th>

            </tr>

        </thead>

        <tbody>

            @forelse($consultationRooms as $item)

                    <tr>

                        <td>
                            #{{ $item->consultation_id }}
                        </td>

                        <td>
                            {{ $item->consultation->patient->full_name ?? '--' }}
                        </td>

                        <td>
                            {{ $item->room->room_name ?? '--' }}
                        </td>

                        <td>
                            {{ $item->notes ?? '--' }}
                        </td>

                        <td>

                            @if($item->status == 1)

                                <span class="badge bg-success">
                                    Hoạt động
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    Không hoạt động
                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('consultation_rooms.show', [
                    'consultation_id' => $item->consultation_id,
                    'room_id' => $item->room_id
                ]) }}" class="btn btn-info btn-sm">

                                Xem

                            </a>

                            <a href="{{ route('consultation_rooms.edit', [
                    'consultation_id' => $item->consultation_id,
                    'room_id' => $item->room_id
                ]) }}" class="btn btn-warning btn-sm">

                                Sửa

                            </a>

                            <form action="{{ route('consultation_rooms.destroy', [
                    'consultation_id' => $item->consultation_id,
                    'room_id' => $item->room_id
                ]) }}" method="POST" style="display:inline;">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bạn có chắc muốn xóa không?')">

                                    Xóa

                                </button>

                            </form>

                        </td>

                    </tr>

            @empty

                <tr>

                    <td colspan="6" class="text-center text-muted">

                        Không có dữ liệu

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center">

        {{ $consultationRooms->links('pagination::bootstrap-5') }}

    </div>

@endsection