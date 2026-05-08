@extends('layouts.app')

@section('content')

    <h3>Danh sách khoa</h3>

    <a href="{{ route('departments.create') }}" class="btn btn-primary mb-3">

        + Thêm khoa

    </a>

    <div class="mb-3">

        <div class="d-flex gap-2">

            <input type="text" id="search" class="form-control" placeholder="Tìm mã khoa hoặc tên khoa...">

            <button type="button" id="resetSearch" class="btn btn-secondary">

                Reset

            </button>

        </div>

    </div>

    <table class="table table-bordered table-hover">

        <thead class="table-dark">

            <tr>

                <th>Mã khoa</th>
                <th>Tên khoa</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th width="220">Hành động</th>

            </tr>

        </thead>

        <tbody id="departmentTable">

            @forelse($departments as $department)

                <tr>

                    <td>
                        {{ $department->dept_code }}
                    </td>

                    <td>
                        {{ $department->dept_name }}
                    </td>

                    <td>
                        {{ $department->description }}
                    </td>

                    <td>

                        @if($department->status == 1)

                            <span class="badge bg-success">
                                Hoạt động
                            </span>

                        @elseif($department->status == 0)

                            <span class="badge bg-secondary">
                                Không hoạt động
                            </span>

                        @else($department->status == 2)

                            <span class="badge bg-warning">
                                Đang xây dựng
                            </span>

                        @endif

                    </td>

                    <td>

                        <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-sm">

                            Xem

                        </a>

                        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning btn-sm">

                            Sửa

                        </a>

                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST"
                            style="display:inline;">

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

                    <td colspan="5" class="text-center text-muted">

                        Không có dữ liệu

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <div class="d-flex justify-content-center">

        {{ $departments->links('pagination::bootstrap-5') }}

    </div>

@endsection

<!--tìm kiếm ajax-->
@push('scripts')

    <script>

        $(document).ready(function () {

            $('#search').on('keyup', function () {

                let keyword = $(this).val();

                $.ajax({

                    url: "{{ route('departments.ajax.search') }}",

                    type: "GET",

                    data: {
                        keyword: keyword
                    },

                    success: function (data) {

                        let html = '';

                        if (data.length === 0) {

                            html += `
                                                    <tr>
                                                        <td colspan="5"
                                                            class="text-center">
                                                            Không tìm thấy dữ liệu
                                                        </td>
                                                    </tr>
                                                `;

                        } else {

                            data.forEach(function (d) {

                                let status = d.status == 1
                                    ? '<span class="badge bg-success">Hoạt động</span>'
                                    : '<span class="badge bg-secondary">Không hoạt động</span>';

                                html += `

                                                        <tr>

                                                            <td>${d.dept_code ?? ''}</td>

                                                            <td>${d.dept_name ?? ''}</td>

                                                            <td>${d.description ?? ''}</td>

                                                            <td>${status}</td>

                                                            <td>

                                                                <a href="/departments/${d.id}"
                                                                   class="btn btn-info btn-sm">
                                                                    Xem
                                                                </a>

                                                                <a href="/departments/${d.id}/edit"
                                                                   class="btn btn-warning btn-sm">
                                                                    Sửa
                                                                </a>

                                                                <form action="/departments/${d.id}"
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
                                                                            onclick="return confirm('Bạn có chắc muốn xóa không?')">

                                                                        Xóa

                                                                    </button>

                                                                </form>

                                                            </td>

                                                        </tr>

                                                    `;
                            });
                        }

                        $('#departmentTable').html(html);

                    }

                });

            });

            $('#resetSearch').click(function () {

                $('#search').val('');

                location.reload();

            });

        });

    </script>

@endpush