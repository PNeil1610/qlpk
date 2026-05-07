@extends('layouts.app')

@section('content')
    <h3>Danh sách bệnh</h3>
    <a href="{{ route('diagnoses.create') }}" class="btn btn-primary mb-3">
        + Thêm bệnh
    </a>
    <div class="mb-3">
        <div class="d-flex gap-2">
            <input type="text" id="search" class="form-control" placeholder="Tìm mã bệnh hoặc tên bệnh...">
            <button type="button" id="resetSearch" class="btn btn-secondary">
                Reset
            </button>
        </div>
    </div>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Mã bệnh</th>
                <th>Tên bệnh</th>
                <th>ICD</th>
                <th>Trạng thái</th>
                <th width="220">Hành động</th>
            </tr>
        </thead>
        <tbody id="diagnosisTable">
            @foreach($diagnoses as $d)
                <tr>
                    <td>{{ $d->diagnosis_code }}</td>
                    <td>{{ $d->diagnosis_name }}</td>
                    <td>{{ $d->icd_version }}</td>
                    <td>
                        @if($d->status == 1)
                            <span class="badge bg-success">
                                Hoạt động
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                Ngưng
                            </span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('diagnoses.show', $d->id) }}" class="btn btn-info btn-sm">
                            Xem
                        </a>
                        <a href="{{ route('diagnoses.edit', $d->id) }}" class="btn btn-warning btn-sm">
                            Sửa
                        </a>
                        <form action="{{ route('diagnoses.destroy', $d->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Xóa bệnh này?')">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {{ $diagnoses->links() }}
    </div>
@endsection

<!-- 
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#search').on('keyup', function () {
                let keyword = $(this).val();
                $.ajax({
                    url: "{{ route('diagnoses.ajax.search') }}",
                    type: "GET",
                    data: {
                        keyword: keyword
                    },
                    success: function (data) {
                        let html = '';
                        if (data.length === 0) {
                            html += `
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                Không tìm thấy dữ liệu
                                            </td>
                                        </tr>
                                    `;
                        } else {
                            data.forEach(function (d) {
                                let status = d.status == 1
                                    ? '<span class="badge bg-success">Hoạt động</span>'
                                    : '<span class="badge bg-secondary">Ngưng</span>';
                                html += `
                                            <tr>
                                                <td>${d.diagnosis_code ?? ''}</td>
                                                <td>${d.diagnosis_name ?? ''}</td>
                                                <td>${d.icd_version ?? ''}</td>
                                                <td>${status}</td>
                                                <td>
                                                    <a href="/diagnoses/${d.id}"
                                                       class="btn btn-info btn-sm">
                                                        Xem
                                                    </a>
                                                    <a href="/diagnoses/${d.id}/edit"
                                                       class="btn btn-warning btn-sm">
                                                        Sửa
                                                    </a>
                                                    <form action="/diagnoses/${d.id}"
                                                          method="POST"
                                                          style="display:inline;">
                                                        <input type="hidden"
                                                               name="_token"
                                                               value="{{ csrf_token() }}">
                                                        <input type="hidden"
                                                               name="_method"
                                                               value="DELETE">
                                                        <button class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Xóa bệnh này?')">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        `;
                            });
                        }
                        $('#diagnosisTable').html(html);
                    }
                });
            });
            $('#resetSearch').click(function () {
                $('#search').val('');
                location.reload();
            });
        });
    </script>
@endpush -->