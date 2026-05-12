@extends('layouts.app')
<!-- danh sách bệnh nhân -->
@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-users me-2"></i>Danh sách Bệnh nhân</h3>
        <a href="{{ route('patients.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-1"></i> Thêm Bệnh nhân
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <div class="row">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="search" class="form-control border-start-0 ps-0" placeholder="Tìm tên, mã bệnh nhân, SĐT...">
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
                            <th scope="col" class="ps-4">Mã BN</th>
                            <th scope="col">Tên</th>
                            <th scope="col" class="text-center">Tuổi</th>
                            <th scope="col" class="text-center">Giới tính</th>
                            <th scope="col">SĐT</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4" width="300">Hành động</th>
                        </tr>
                    </thead>
<tbody id="patientTable">
    @foreach($patients as $p)
    <tr>
        <td class="ps-4 fw-bold text-secondary">{{ $p->patient_code }}</td>
        <td class="fw-bold text-dark">{{ $p->full_name }}</td>
        <td class="text-center">{{ $p->age }}</td>
        <td class="text-center">{{ $p->gender_text }}</td>
        <td>{{ $p->phone_number }}</td>
        <td class="text-center">
            @switch($p->status)
                @case(1)
                    <span class="badge rounded-pill bg-success">Hoạt động</span>
                    @break
                @case(0)
                    <span class="badge rounded-pill bg-secondary">Không hoạt động</span>
                    @break
            @endswitch
        </td>
        <td class="text-center pe-4">
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('patients.show', $p->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                    <i class="fas fa-eye me-1"></i> Xem
                </a>
                <a href="{{ route('patients.edit', $p->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                    <i class="fas fa-edit me-1"></i> Sửa
                </a>
                <a href="{{ route('consultations.create', ['patient_id' => $p->id]) }}" class="btn btn-sm btn-success shadow-sm" title="Tạo phiên khám">
                    <i class="fas fa-notes-medical me-1"></i> Phiên khám
                </a>
                <form action="{{ route('patients.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá không?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                        <i class="fas fa-trash-alt me-1"></i> Xóa
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @endforeach
        </tbody>
    </table>
    </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        <div class="d-flex justify-content-end">
            {{ $patients->appends(request()->all())->links() }}
        </div>
    </div>
    </div>
</div>
@endsection


<!--tìm kiếm ajax-->
@push('scripts')

<script>

$(document).ready(function () {

    // 🔥 SEARCH REALTIME
    $('#search').on('keyup', function () {

        let keyword = $(this).val();

        $.ajax({

            url: "{{ route('patients.ajax.search') }}",

            type: "GET",

            data: {
                keyword: keyword
            },

            success: function (data) {

                let html = '';

                if (data.length === 0) {
                    html += `
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                Không tìm thấy dữ liệu
                            </td>
                        </tr>
                    `;
                } else {

                    data.forEach(function (p) {

                        let gender = '';

                        if (p.gender === 'Male') {
                            gender = 'Nam';
                        } else if (p.gender === 'Female') {
                            gender = 'Nữ';
                        } else {
                            gender = 'Khác';
                        }

                        let status = p.status == 1
                            ? '<span class="badge rounded-pill bg-success">Hoạt động</span>'
                            : '<span class="badge rounded-pill bg-secondary">Không hoạt động</span>';

                        html += `
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">${p.patient_code ?? ''}</td>
                                <td class="fw-bold text-dark">${p.full_name ?? ''}</td>
                                <td class="text-center">${p.age ?? ''}</td>
                                <td class="text-center">${gender}</td>
                                <td>${p.phone_number ?? ''}</td>
                                <td class="text-center">${status}</td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="/patients/${p.id}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="/patients/${p.id}/edit" class="btn btn-sm btn-primary shadow-sm" title="Sửa">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                        <a href="/consultations/create?patient_id=${p.id}" class="btn btn-sm btn-success shadow-sm" title="Tạo phiên khám">
                                            <i class="fas fa-notes-medical me-1"></i> Phiên khám
                                        </a>
                                        <form action="/patients/${p.id}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá không?');">
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

                $('#patientTable').html(html);
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