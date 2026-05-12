@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <h3 class="mb-0 text-primary"><i class="fas fa-user-md me-2"></i>Danh sách Nhân sự</h3>
        <a href="{{ route('staff.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Thêm Nhân Viên
        </a>
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
                            <th scope="col" class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                            'Technician' => ['Kỹ thuật viên', 'warning'],
                                            'Pharmacist' => ['Dược sĩ', 'success'],
                                            'Receptionist' => ['Lễ tân', 'secondary'],
                                            'Cashier' => ['Thu ngân', 'dark'],
                                            'Admin' => ['Quản trị', 'danger'],
                                        ];
                                        $typeData = $typeColors[$staff->staff_type] ?? ['Chưa rõ', 'light'];
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
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('staff.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên {{ $staff->full_name }} không? Hành động này không thể hoàn tác!');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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