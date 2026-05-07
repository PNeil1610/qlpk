@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

<h3>Danh sách phiên khám</h3>

<!-- <a href="{{ route('consultations.create') }}"
   class="btn btn-primary mb-3">
    + Tạo phiên khám
</a> -->


{{-- ========================
    SEARCH
======================== --}}
<form method="GET" class="mb-3">
    <div class="mb-3">

    <div class="d-flex gap-2">

        <input type="text"
               id="search"
               class="form-control"
               placeholder="Tìm tên hoặc mã bệnh nhân...">

        <button type="button"
                id="resetSearch"
                class="btn btn-secondary">
            Reset
        </button>

    </div>

</div>
</form>


{{-- ========================
    TABLE
======================== --}}
<table class="table table-bordered table-hover align-middle">

    <thead class="table-dark">
        <tr>
            <th>Mã PK</th>
            <th>Mã BN</th>
            <th>Bệnh nhân</th>
            <th>Ngày khám</th>

            {{-- 🔥 mới --}}
            <th>Nhiệt độ</th>
            <th>Huyết áp</th>

            <th>Triệu chứng</th>
            <th>Trạng thái</th>
            <th width="180">Hành động</th>
        </tr>
    </thead>

    <tbody id="consultationTable">

        @forelse($consultations as $c)

        <tr>

            <td>{{ $c->id }}</td>

            <td>
                {{ $c->patient->patient_code ?? 'N/A' }}
            </td>

            <td>
                {{ $c->patient->full_name ?? 'N/A' }}
            </td>

            <td>
                {{ $c->consultation_date
                    ? $c->consultation_date->format('d/m/Y H:i')
                    : '' }}
            </td>

            {{-- 🌡️ nhiệt độ --}}
            <td>
                {{ $c->temperature ?? '--' }}
            </td>

            {{-- ❤️ huyết áp --}}
            <td>
                {{ $c->blood_pressure ?? '--' }}
            </td>

            <td>
                {{ Str::limit($c->symptoms, 40) }}
            </td>

            {{-- STATUS --}}
            <td>

                @switch($c->status)

                    @case(0)
                        <span class="badge bg-secondary">
                            Mới tạo
                        </span>
                        @break

                    @case(1)
                        <span class="badge bg-dark">
                            Chờ
                        </span>
                        @break

                    @case(2)
                        <span class="badge bg-primary">
                            Đang khám
                        </span>
                        @break

                    @case(3)
                        <span class="badge bg-warning text-dark">
                            Chờ thanh toán
                        </span>
                        @break

                    @case(4)
                        <span class="badge bg-success">
                            Hoàn thành
                        </span>
                        @break

                    @default
                        <span class="badge bg-danger">
                            Không xác định
                        </span>

                @endswitch

            </td>

            {{-- ACTION --}}
           <td>

    <a href="{{ route('consultations.show', $c->id) }}"
       class="btn btn-info btn-sm">
        Xem
    </a>

    <a href="{{ route('consultations.edit', $c->id) }}"
       class="btn btn-warning btn-sm">
        Sửa
    </a>

    <form action="{{ route('consultations.destroy', $c->id) }}"
          method="POST"
          style="display:inline;">

        @csrf
        @method('DELETE')

        <button class="btn btn-danger btn-sm"
                onclick="return confirm('Bạn có chắc muốn xóa không?')">

            Xóa
        </button>

    </form>

</td>

        </tr>

        @empty

        <tr>
            <td colspan="9" class="text-center text-muted">
                Không có dữ liệu
            </td>
        </tr>

        @endforelse

    </tbody>

</table>


{{-- ========================
    PAGINATION
======================== --}}
<div class="d-flex justify-content-center">
    {{ $consultations->appends(request()->all())->links() }}
</div>

@endsection

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
                            <td colspan="9" class="text-center">
                                Không tìm thấy dữ liệu
                            </td>
                        </tr>
                    `;

                } else {

                    data.forEach(function (c) {

                        let status = '';

                        switch(c.status) {

                            case 0:
                                status = '<span class="badge bg-secondary">Mới tạo</span>';
                                break;

                            case 1:
                                status = '<span class="badge bg-warning">Chờ</span>';
                                break;

                            case 2:
                                status = '<span class="badge bg-primary">Đang khám</span>';
                                break;

                            case 3:
                                status = '<span class="badge bg-info">Chờ thanh toán</span>';
                                break;

                            case 4:
                                status = '<span class="badge bg-success">Hoàn thành</span>';
                                break;
                        }

                        html += `
    <tr>

        <td>${c.id}</td>

        <td>${c.patient?.patient_code ?? 'N/A'}</td>

        <td>${c.patient?.full_name ?? 'N/A'}</td>

        <td>

    ${
        c.consultation_date
        ? new Date(c.consultation_date).toLocaleString('vi-VN')
        : ''
    }

        </td>

        <td>${c.temperature ?? '--'}</td>

        <td>${c.blood_pressure ?? '--'}</td>

        <td>${c.symptoms ?? ''}</td>

        <td>${status}</td>

        <td>

    <a href="{{ route('consultations.show', $c->id) }}"
       class="btn btn-info btn-sm">
        Xem
    </a>

    <a href="{{ route('consultations.edit', $c->id) }}"
       class="btn btn-warning btn-sm">
        Sửa
    </a>

    <form action="{{ route('consultations.destroy', $c->id) }}"
          method="POST"
          style="display:inline;">

        @csrf
        @method('DELETE')

        <button class="btn btn-danger btn-sm"
                onclick="return confirm('Bạn có chắc muốn xóa không?')">

            Xóa
        </button>

    </form>

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