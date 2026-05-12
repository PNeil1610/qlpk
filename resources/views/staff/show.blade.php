@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-id-badge me-2"></i>Hồ Sơ Nhân Sự</h3>
        <div>
            <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-warning text-dark fw-bold me-2">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </a>
            <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-center p-4 h-100">
                <div class="mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->full_name) }}&size=150&background=random" class="rounded-circle img-thumbnail shadow-sm" alt="Avatar">
                </div>
                <h4 class="fw-bold mb-1">{{ $staff->full_name }}</h4>
                <p class="text-muted mb-2">{{ $staff->academic_degree ?? '' }} {{ $staff->position ?? 'Nhân viên' }}</p>
                
                <div class="mb-3">
                    <span class="badge bg-primary px-3 py-2 fs-6">{{ $staff->staff_code }}</span>
                </div>

                <ul class="list-group list-group-flush text-start mt-3">
                    <li class="list-group-item px-0">
                        <i class="fas fa-hospital-user text-primary me-2"></i> <strong>Khoa:</strong> {{ $staff->department->dept_name ?? 'Chưa cập nhật' }}
                    </li>
                    <li class="list-group-item px-0">
                        <i class="fas fa-door-open text-primary me-2"></i> <strong>Phòng:</strong> {{ $staff->room->room_name ?? 'Chưa cập nhật' }}
                    </li>
                    <li class="list-group-item px-0">
                        <i class="fas fa-briefcase text-primary me-2"></i> <strong>Trạng thái:</strong> 
                        @if($staff->status == 1) <span class="text-success fw-bold">Đang làm việc</span>
                        @elseif($staff->status == 2) <span class="text-warning fw-bold">Nghỉ phép</span>
                        @elseif($staff->status == 3) <span class="text-secondary fw-bold">Nghỉ hưu</span>
                        @else <span class="text-danger fw-bold">Nghỉ việc</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-info-circle text-info me-1"></i> Chi tiết lý lịch
                </div>
                <div class="card-body p-4">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Giới tính</div>
                        <div class="col-sm-8 fw-bold">
                            {{ $staff->gender == 'Male' ? 'Nam' : ($staff->gender == 'Female' ? 'Nữ' : 'Khác') }}
                        </div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Năm sinh (Tuổi)</div>
                        <div class="col-sm-8 fw-bold">
                            {{ $staff->yob ?? '?' }} 
                            @if($staff->age) ({{ $staff->age }} tuổi) @endif
                        </div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Số điện thoại</div>
                        <div class="col-sm-8 fw-bold">{{ $staff->phone_number ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Email</div>
                        <div class="col-sm-8 fw-bold">{{ $staff->email ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">CCCD / CMND</div>
                        <div class="col-sm-8 fw-bold">{{ $staff->national_id ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Địa chỉ</div>
                        <div class="col-sm-8 fw-bold">{{ $staff->address ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Chuyên khoa</div>
                        <div class="col-sm-8 fw-bold">{{ $staff->specialty ?? 'Chưa cập nhật' }}</div>
                    </div>
                    <hr class="text-muted" style="opacity: 0.1">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Chứng chỉ hành nghề</div>
                        <div class="col-sm-8 fw-bold text-success">{{ $staff->medical_license ?? 'Chưa cập nhật' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection