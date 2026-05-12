@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-stethoscope me-2"></i>Danh sách Phiên khám</h3>
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
                            <th scope="col" class="text-center">Phòng khám</th>
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
            <td>{{ $c->consultation_date ? $c->consultation_date->format('d/m/Y H:i') : '' }}</td>
            <td class="text-center text-primary fw-bold">
                @if($c->rooms->isNotEmpty())
                    {{ $c->rooms->pluck('room_name')->join(', ') }}
                @else
                    --
                @endif
            </td>
            <td class="text-center">{{ $c->temperature ?? '--' }}</td>
            <td class="text-center">{{ $c->blood_pressure ?? '--' }}</td>
            <td class="text-muted">{{ Str::limit($c->symptoms, 40) }}</td>
            <td class="text-center">
                @switch($c->status)
                    @case(0)
                        <span class="badge rounded-pill bg-secondary">Mới tạo</span>
                        @break
                    @case(1)
                        <span class="badge rounded-pill bg-dark">Chờ</span>
                        @break
                    @case(2)
                        <span class="badge rounded-pill bg-primary">Đang khám</span>
                        @break
                    @case(3)
                        <span class="badge rounded-pill bg-warning text-dark">Chờ thanh toán</span>
                        @break
                    @case(4)
                        <span class="badge rounded-pill bg-success">Hoàn thành</span>
                        @break
                    @default
                        <span class="badge rounded-pill bg-danger">Không xác định</span>
                @endswitch
            </td>
            <td class="text-center pe-4">
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('consultations.show', $c->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                        <i class="fas fa-eye me-1"></i> Xem
                    </a>
                    <a href="{{ route('consultations.edit', $c->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                        <i class="fas fa-edit me-1"></i> Sửa
                    </a>
                    <form action="{{ route('consultations.destroy', $c->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                            <i class="fas fa-trash-alt me-1"></i> Xóa
                        </button>
                    </form>
                </div>
            </td>

        </tr>

        @empty
        <tr>
            <td colspan="10" class="text-center py-5 text-muted">
                <i class="fas fa-stethoscope fs-2 mb-3 mt-2 d-block text-black-50"></i>
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
        {{ $consultations->appends(request()->all())->links() }}
    </div>
</div>
</div>
</div>

@endsection
<!--tìm kiếm ajax-->
@push('scripts')

<script>

$(document).ready(function () {

    // 🔥 SEARCH AJAX
    $('#search').on('keyup', function () {

        let keyword = $(this).val();

        $.ajax({

            url: "{{ route('consultations.ajax.search') }}",

            type: "GET",

            data: {
                keyword: keyword
            },

            success: function (data) {

                let html = '';

                if (data.length === 0) {
                    html += `
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-stethoscope fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                Không tìm thấy dữ liệu
                            </td>
                        </tr>
                    `;
                } else {

                    data.forEach(function (c) {

                        let status = '';

                        switch(c.status) {

                            case 0:
                                status = '<span class="badge rounded-pill bg-secondary">Mới tạo</span>';
                                break;
                            case 1:
                                status = '<span class="badge rounded-pill bg-warning">Chờ</span>';
                                break;
                            case 2:
                                status = '<span class="badge rounded-pill bg-primary">Đang khám</span>';
                                break;
                            case 3:
                                status = '<span class="badge rounded-pill bg-info">Chờ thanh toán</span>';
                                break;
                            case 4:
                                status = '<span class="badge rounded-pill bg-success">Hoàn thành</span>';
                                break;
                        }

                        let roomNames = '--';
                        if (c.rooms && c.rooms.length > 0) {
                            roomNames = c.rooms.map(r => r.room_name).join(', ');
                        }

                        html += `
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">${c.id}</td>
                                <td class="fw-bold text-secondary">${c.patient?.patient_code ?? 'N/A'}</td>
                                <td class="fw-bold text-dark">${c.patient?.full_name ?? 'N/A'}</td>
                                <td>${ c.consultation_date ? new Date(c.consultation_date).toLocaleString('vi-VN') : '' }</td>
                                <td class="text-center text-primary fw-bold">${roomNames}</td>
                                <td class="text-center">${c.temperature ?? '--'}</td>
                                <td class="text-center">${c.blood_pressure ?? '--'}</td>
                                <td class="text-muted">${c.symptoms ?? ''}</td>
                                <td class="text-center">${status}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="/consultations/${c.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="/consultations/${c.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                        <form action="/consultations/${c.id}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa không?');">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#consultationTable').html(html);
            }
        });
    });


    // 🔥 RESET
    $('#resetSearch').click(function () {

        $('#search').val('');

        location.reload();
    });

});

</script>

@endpush