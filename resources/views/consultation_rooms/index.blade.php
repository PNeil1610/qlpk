@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-hospital-user me-2"></i>Sắp xếp Phòng khám</h3>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Phiên khám</th>
                            <th scope="col">Bệnh nhân</th>
                            <th scope="col">Phòng</th>
                            <th scope="col">Ghi chú</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4" width="220">Hành động</th>
                        </tr>
                    </thead>

        <tbody>

            @forelse($consultationRooms as $item)

                    <tr>

                        <td class="ps-4 fw-bold text-secondary">#{{ $item->consultation_id }}</td>
                        <td class="fw-bold text-dark">{{ $item->consultation->patient->full_name ?? '--' }}</td>
                        <td class="fw-bold text-primary">{{ $item->room->room_name ?? '--' }}</td>
                        <td class="text-muted">{{ $item->notes ?? '--' }}</td>
                        <td class="text-center">
                            @if($item->status == 1)
                                <span class="badge rounded-pill bg-success">Hoạt động</span>
                            @else
                                <span class="badge rounded-pill bg-secondary">Không hoạt động</span>
                            @endif
                        </td>

                        <td class="text-center pe-4">
                            <div class="d-flex gap-2 justify-content-center">
                                <a href="{{ route('consultation_rooms.show', [
                                    'consultation_id' => $item->consultation_id,
                                    'room_id' => $item->room_id
                                ]) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('consultation_rooms.edit', [
                                    'consultation_id' => $item->consultation_id,
                                    'room_id' => $item->room_id
                                ]) }}" class="btn btn-sm btn-primary shadow-sm" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('consultation_rooms.destroy', [
                                    'consultation_id' => $item->consultation_id,
                                    'room_id' => $item->room_id
                                ]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
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
                        <i class="fas fa-hospital-user fs-2 mb-3 mt-2 d-block text-black-50"></i>
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
            {{ $consultationRooms->links('pagination::bootstrap-5') }}
        </div>
    </div>
    </div>
</div>
@endsection