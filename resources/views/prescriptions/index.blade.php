@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-file-prescription me-2"></i>Danh Sách Toa Thuốc</h3>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" class="ps-4">ID Toa</th>
                                <th scope="col">Ngày kê</th>
                                <th scope="col">Bệnh nhân</th>
                                <th scope="col">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prescriptions as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-secondary">#{{ $item->id }}</td>
                                    <td>{{ $item->prescribed_at ? \Carbon\Carbon::parse($item->prescribed_at)->format('d/m/Y H:i') : '---' }}
                                    </td>
                                    <td>
                                        <span
                                            class="fw-bold text-dark">{{ $item->consultation->patient->full_name ?? 'Không rõ' }}</span>
                                    </td>
                                    <td>
                                        @if($item->status == 1)
                                            <span class="badge bg-primary">Đã kê đơn</span>
                                        @elseif($item->status == 2)
                                            <span class="badge bg-success">Đã phát thuốc</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        Chưa có toa thuốc nào trên hệ thống.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-end">
                    {{ $prescriptions->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection