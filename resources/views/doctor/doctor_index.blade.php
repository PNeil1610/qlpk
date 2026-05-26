@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-stethoscope me-2"></i>Danh sách Phiếu khám ({{ $roomName }})</h3>
    </div>


    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên hoặc mã bệnh nhân...">
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
                            <th scope="col" class="ps-4">Mã PK</th>
                            <th scope="col">Mã BN</th>
                            <th scope="col">Bệnh nhân</th>
                            <th scope="col">Ngày khám</th>
                            <th scope="col" class="text-center">Nhiệt độ</th>
                            <th scope="col" class="text-center">Huyết áp</th>
                            <th scope="col">Triệu chứng</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4" width="220">Hành động</th>
                        </tr>
                    </thead>

    <tbody id="consultationTable">
        @forelse($consultations as $c)
        <tr>
            <td class="ps-4 fw-bold text-secondary">{{ $c->id }}</td>
            <td class="fw-bold text-secondary">{{ $c->patient->patient_code ?? 'N/A' }}</td>
            <td class="fw-bold text-dark">{{ $c->patient->full_name ?? 'N/A' }}</td>
            <td>{{ $c->consultation_date ? \Carbon\Carbon::parse($c->consultation_date)->format('d/m/Y H:i') : '' }}</td>
            <td class="text-center">{{ $c->temperature ?? '--' }}</td>
            <td class="text-center">{{ $c->blood_pressure ?? '--' }}</td>
            <td class="text-muted">{{ Str::limit($c->symptoms, 40) }}</td>
            <td class="text-center">
                @switch($c->status)
                    @case(0) <span class="badge rounded-pill bg-secondary">Mới tạo</span> @break
                    @case(1) <span class="badge rounded-pill bg-dark">Chờ</span> @break
                    @case(2) <span class="badge rounded-pill bg-primary">Đang khám</span> @break
                    @case(3) <span class="badge rounded-pill bg-warning text-dark">Chờ thanh toán</span> @break
                    @case(4) <span class="badge rounded-pill bg-success">Hoàn thành</span> @break
                    @default <span class="badge rounded-pill bg-danger">Không xác định</span>
                @endswitch
            </td>
            <td class="text-center pe-4">
                <div class="d-flex gap-2 justify-content-center">
                    <!-- <a href="{{ route('consultations.show', $c->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem chi tiết">
                        <i class="fas fa-eye"></i>
                    </a> -->
                    @if($c->status === \App\Models\Consultation::STATUS_COMPLETED)
                        <a href="{{ route('consultations.doctor.examine', $c->id) }}" class="btn btn-sm btn-outline-secondary shadow-sm" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                    @else
                        <a href="{{ route('consultations.doctor.examine', $c->id) }}" class="btn btn-sm btn-success shadow-sm" title="Khám bệnh & Kê đơn">
                            <i class="fas fa-stethoscope"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center py-5 text-muted">
                <i class="fas fa-stethoscope fs-2 mb-3 mt-2 d-block text-black-50"></i>
                Không có dữ liệu phiếu khám tại phòng của bạn
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>
</div>
<div class="card-footer bg-white border-0 py-3">
    <div class="d-flex justify-content-end">
        {{ $consultations->appends(request()->all())->links() }}
    </div>
</div>
</div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    function loadConsultations() {
        let keyword = $('#search').val();

        $.ajax({
            url: "{{ route('consultations.doctor.ajax.search') }}",
            type: "GET",
            data: { keyword: keyword },
            success: function (data) {
                let html = '';

                if (data.length === 0) {
                    html += `<tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fas fa-stethoscope fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Không tìm thấy dữ liệu
                                </td>
                            </tr>`;
                } else {
                    data.forEach(function (c) {
                        let status = '';
                        switch(c.status) {
                            case 0: status = '<span class="badge rounded-pill bg-secondary">Mới tạo</span>'; break;
                            case 1: status = '<span class="badge rounded-pill bg-dark">Chờ</span>'; break;
                            case 2: status = '<span class="badge rounded-pill bg-primary">Đang khám</span>'; break;
                            case 3: status = '<span class="badge rounded-pill bg-warning text-dark">Chờ thanh toán</span>'; break;
                            case 4: status = '<span class="badge rounded-pill bg-success">Hoàn thành</span>'; break;
                        }

                        let dateStr = c.consultation_date ? new Date(c.consultation_date).toLocaleString('vi-VN') : '';

                        let actionHtml = '';
                        if(c.status === 4) {
                            actionHtml = `
                                <a href="/consultations/doctor/${c.id}/examine" class="btn btn-sm btn-outline-secondary shadow-sm" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            `;
                        } else {
                            actionHtml = `
                                <a href="/consultations/doctor/${c.id}/examine" class="btn btn-sm btn-success shadow-sm" title="Khám bệnh & Kê đơn">
                                    <i class="fas fa-stethoscope"></i>
                                </a>
                            `;
                        }

                        html += `
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">${c.id}</td>
                            <td class="fw-bold text-secondary">${c.patient?.patient_code ?? 'N/A'}</td>
                            <td class="fw-bold text-dark">${c.patient?.full_name ?? 'N/A'}</td>
                            <td>${dateStr}</td>
                            <td class="text-center">${c.temperature ?? '--'}</td>
                            <td class="text-center">${c.blood_pressure ?? '--'}</td>
                            <td class="text-muted">${c.symptoms ?? ''}</td>
                            <td class="text-center">${status}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    ${actionHtml}
                                </div>
                            </td>
                        </tr>
                        `;
                    });
                }
                $('#consultationTable').html(html);
            }
        });
    }

    // 🔥 SEARCH AJAX
    $('#search').on('keyup', function () {
        loadConsultations();
    });

    // 🔥 AUTO REFRESH EVERY 5 SECONDS
    setInterval(function() {
        // Chỉ auto refresh nếu người dùng đang không gõ chữ liên tục
        if (!$('#search').is(':focus') || $('#search').val() === '') {
            loadConsultations();
        }
    }, 5000);

    // 🔥 RESET
    $('#resetSearch').click(function () {
        $('#search').val('');
        loadConsultations();
    });
});
</script>
@endpush
