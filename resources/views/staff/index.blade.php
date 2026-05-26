@extends('layouts.app')

@section('title', 'Danh sách Nhân sự')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h3 class="mb-0 text-primary"><i class="fas fa-user-md me-2"></i>Danh sách Nhân sự</h3>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm Nhân Viên
        </a>
    </div>

    <!-- Search bar -->
    <div class="mb-4">
        <div class="d-flex gap-2">
            <input type="text" id="search" class="form-control" placeholder="Tìm tên, mã nhân viên, SĐT...">
            <button type="button" id="resetSearch" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Mã NV</th>
                            <th scope="col">Họ và tên</th>
                            <th scope="col">Vị trí / Chức danh</th>
                            <th scope="col">Khoa & Phòng</th>
                            <th scope="col">Số điện thoại</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="staffTable">
                        @forelse($staffs as $staff)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $staff->staff_code ?? 'Chưa cấp' }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $staff->full_name }}</span><br>
                                    <small class="text-muted">{{ $staff->academic_degree ?? '' }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'Doctor' => ['Bác sĩ', 'primary'],
                                            'Nurse' => ['Điều dưỡng', 'info'],
                                            'Pharmacist' => ['Dược sĩ', 'success'],
                                            'Receptionist' => ['Lễ tân', 'secondary'],
                                            'Cashier' => ['Thu ngân', 'dark'],
                                            'Admin' => ['Quản trị', 'danger'],
                                        ];
                                        $typeData = $typeColors[$staff->staff_type] ?? ['Chưa rõ', 'warning'];
                                    @endphp
                                    <span class="badge bg-{{ $typeData[1] }}">{{ $typeData[0] }}</span>
                                    <br><small class="text-muted">{{ $staff->position ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="mb-1"><i class="fas fa-hospital-user text-muted me-1"></i> {{ $staff->department->dept_name ?? 'Chưa xếp khoa' }}</div>
                                    <div><i class="fas fa-door-open text-muted me-1"></i> {{ $staff->room->room_name ?? 'Chưa xếp phòng' }}</div>
                                </td>
                                <td>{{ $staff->phone_number ?? '-' }}</td>
                                <td class="text-center">
                                    @if($staff->status == 1)
                                        <span class="badge rounded-pill bg-success">Đang làm việc</span>
                                    @elseif($staff->status == 2)
                                        <span class="badge rounded-pill bg-warning text-dark">Nghỉ phép</span>
                                    @elseif($staff->status == 3)
                                        <span class="badge rounded-pill bg-secondary">Đã nghỉ hưu</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Đã nghỉ việc</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    {{-- XEM --}}
                                    <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- SỬA --}}
                                    <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    {{-- XÓA --}}
                                    <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên {{ $staff->full_name }} không? Hành động này không thể hoàn tác!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa nhân viên">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Chưa có nhân viên nào trên hệ thống.<br>Hãy bấm "Thêm Nhân Viên" để bắt đầu!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $staffs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // AJAX SEARCH REALTIME
    $('#search').on('keyup', function () {
        let keyword = $(this).val();

        $.ajax({
            url: "{{ route('staff.ajax.search') }}",
            type: "GET",
            data: { keyword: keyword },
            success: function (data) {
                let html = '';

                if (data.length === 0) {
                    html += `
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                Không tìm thấy nhân viên nào!
                            </td>
                        </tr>
                    `;
                } else {
                    data.forEach(function (staff) {
                        // Vị trí / Chức danh
                        let typeData = ['Chưa rõ', 'light'];
                        const typeColors = {
                            'Doctor': ['Bác sĩ', 'primary'],
                            'Nurse': ['Điều dưỡng', 'info'],
                            'Pharmacist': ['Dược sĩ', 'success'],
                            'Receptionist': ['Lễ tân', 'secondary'],
                            'Cashier': ['Thu ngân', 'dark'],
                            'Admin': ['Quản trị', 'danger']
                        };
                        if (typeColors[staff.staff_type]) {
                            typeData = typeColors[staff.staff_type];
                        }
                        
                        let staffTypeHtml = `<span class="badge bg-${typeData[1]}">${typeData[0]}</span><br><small class="text-muted">${staff.position ?? ''}</small>`;

                        // Trạng thái
                        let statusHtml = '';
                        if (staff.status == 1) {
                            statusHtml = '<span class="badge rounded-pill bg-success">Đang làm việc</span>';
                        } else if (staff.status == 2) {
                            statusHtml = '<span class="badge rounded-pill bg-warning text-dark">Nghỉ phép</span>';
                        } else if (staff.status == 3) {
                            statusHtml = '<span class="badge rounded-pill bg-secondary">Đã nghỉ hưu</span>';
                        } else {
                            statusHtml = '<span class="badge rounded-pill bg-danger">Đã nghỉ việc</span>';
                        }

                        // Khoa & Phòng
                        let deptName = staff.department ? staff.department.dept_name : 'Chưa xếp khoa';
                        let roomName = staff.room ? staff.room.room_name : 'Chưa xếp phòng';
                        let deptHtml = `<div class="mb-1"><i class="fas fa-hospital-user text-muted me-1"></i> ${deptName}</div>
                                        <div><i class="fas fa-door-open text-muted me-1"></i> ${roomName}</div>`;

                        html += `
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">${staff.staff_code ?? 'Chưa cấp'}</td>
                                <td>
                                    <span class="fw-bold text-dark">${staff.full_name ?? ''}</span><br>
                                    <small class="text-muted">${staff.academic_degree ?? ''}</small>
                                </td>
                                <td>${staffTypeHtml}</td>
                                <td>${deptHtml}</td>
                                <td>${staff.phone_number ?? '-'}</td>
                                <td class="text-center">${statusHtml}</td>
                                <td class="text-center pe-4">
                                    <a href="/staff/${staff.id}" class="btn btn-info btn-sm" title="Xem chi tiết"><i class="fas fa-eye"></i></a>
                                    <a href="/staff/${staff.id}/edit" class="btn btn-warning btn-sm" title="Chỉnh sửa"><i class="fas fa-edit"></i></a>
                                    <form action="/staff/${staff.id}" method="POST" style="display:inline;">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa nhân viên ${staff.full_name} không? Hành động này không thể hoàn tác!');" title="Xóa nhân viên"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#staffTable').html(html);
            }
        });
    });

    // RESET
    $('#resetSearch').click(function () {
        $('#search').val('');
        location.reload();
    });
});
</script>
@endpush