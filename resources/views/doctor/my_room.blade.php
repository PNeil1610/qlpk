@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-clinic-medical me-2"></i>Phòng đang trực: <span class="text-danger">{{ $room->room_name ?? 'Chưa xác định' }}</span>
        </h3>
        <span class="text-muted"><i class="fas fa-calendar-day me-1"></i> Ngày hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white font-weight-bold">
            <i class="fas fa-users text-success me-1"></i> Danh sách bệnh nhân đang chờ
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">STT</th>
                            <th scope="col">Mã BN</th>
                            <th scope="col">Tên Bệnh Nhân</th>
                            <th scope="col">Giờ đăng ký</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col" class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $index => $item)
                        <tr>
                            <td class="ps-4">{{ $index + 1 }}</td>
                            <td class="fw-bold text-secondary">{{ $item->patient->patient_code ?? 'Chưa có' }}</td>
                            <td class="fw-bold">{{ $item->patient->full_name ?? 'Không rõ' }}</td>
                            <td>{{ $item->created_at->format('H:i') }}</td>
                            <td><span class="badge bg-warning text-dark">Đang chờ khám</span></td>
                            <td class="text-center pe-4">
                                <a href="#" class="btn btn-sm btn-success">
                                    <i class="fas fa-stethoscope me-1"></i> Gọi vào khám
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-mug-hot fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                Hiện tại không có bệnh nhân nào đang chờ tại phòng này!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection