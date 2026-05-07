@extends('layouts.app')
<!-- danh sách bệnh nhân -->
@section('content')
<h3>Danh sách bệnh nhân</h3>
<a href="{{ route('patients.create') }}" class="btn btn-primary mb-3">+ Thêm</a>

<div class="mb-3">

    <div class="d-flex gap-2">

        <input type="text"
               id="search"
               class="form-control"
               placeholder="Tìm tên, mã bệnh nhân, SĐT...">

        <button type="button"
                id="resetSearch"
                class="btn btn-secondary">
            Reset
        </button>

    </div>

</div>

<table class="table table-bordered">
    <thead class="table-dark">
    <tr>
        <th>Mã BN</th>
        <th>Tên</th>
        <th>Tuổi</th>
        <th>Giới tính</th>
        <th>SĐT</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>
    </thead>
<tbody id="patientTable">
    @foreach($patients as $p)
    <tr>
        <td>{{ $p->patient_code }}</td>
        <td>{{ $p->full_name }}</td>
        <td>{{ $p->age }}</td>
        <td>{{ $p->gender_text }}</td>
        <td>{{ $p->phone_number }}</td>
        <td>
    @switch($p->status)
        @case(1)
            <span class="badge bg-secondary">Hoạt động</span>
            @break
        @case(0)
            <span class="badge bg-dark">Không hoạt động</span>
            @break
    @endswitch
</td>
        <td>

    {{-- XEM --}}
    <a href="{{ route('patients.show', $p->id) }}"
       class="btn btn-info btn-sm">
        Xem
    </a>

    {{-- SỬA --}}
    <a href="{{ route('patients.edit', $p->id) }}"
       class="btn btn-warning btn-sm">
        Sửa
    </a>

    {{-- 🔥 TẠO PHIÊN KHÁM --}}
    <a href="{{ route('consultations.create', ['patient_id' => $p->id]) }}"
       class="btn btn-success btn-sm">
        Tạo phiên khám
    </a>

    {{-- XÓA --}}
    <form action="{{ route('patients.destroy', $p->id) }}"
          method="POST"
          style="display:inline;">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Bạn có chắc muốn xoá không?')">

            Xóa
        </button>

    </form>
</td>
    </tr>
    @endforeach
</tbody>
</table>
<div class="d-flex justify-content-center">
    {{ $patients->appends(request()->all())->links() }}
</div>
@endsection

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
                            <td colspan="7" class="text-center">
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
                            ? '<span class="badge bg-success">Hoạt động</span>'
                            : '<span class="badge bg-secondary">Không hoạt động</span>';

                        html += `
                            <tr>

                                <td>${p.patient_code ?? ''}</td>

                                <td>${p.full_name ?? ''}</td>

                                <td>${p.age ?? ''}</td>

                                <td>${gender}</td>

                                <td>${p.phone_number ?? ''}</td>

                                <td>${status}</td>

                                <td>

    <a href="/patients/${p.id}"
       class="btn btn-info btn-sm">
       Xem
    </a>

    <a href="/patients/${p.id}/edit"
       class="btn btn-warning btn-sm">
       Sửa
    </a>

    <a href="/consultations/create?patient_id=${p.id}"
       class="btn btn-success btn-sm">
       Tạo phiên khám
    </a>

    <form action="/patients/${p.id}"
          method="POST"
          style="display:inline;">

        <input type="hidden"
               name="_token"
               value="{{ csrf_token() }}">

        <input type="hidden"
               name="_method"
               value="DELETE">

        <button type="submit"
                class="btn btn-danger btn-sm"
                onclick="return confirm('Bạn có chắc muốn xoá không?')">

            Xóa
        </button>

    </form>

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