@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-user-edit me-2"></i>Chỉnh sửa hồ sơ: {{ $staff->full_name }}</h3>
        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white font-weight-bold border-bottom">
                        <i class="fas fa-id-card text-primary me-1"></i> Thông tin cá nhân
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Họ và tên *</label>
                            <input type="text" name="full_name" class="form-control" value="{{ $staff->full_name }}" required>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Giới tính</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" {{ $staff->gender == 'Male' ? 'selected' : '' }}>Nam</option>
                                    <option value="Female" {{ $staff->gender == 'Female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="Other" {{ $staff->gender == 'Other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Năm sinh</label>
                                <input type="number" name="yob" class="form-control" value="{{ $staff->yob }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ $staff->phone_number }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CCCD / CMND</label>
                                <input type="text" name="national_id" class="form-control" value="{{ $staff->national_id }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $staff->email }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="{{ $staff->address }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white font-weight-bold border-bottom">
                        <i class="fas fa-briefcase text-success me-1"></i> Chuyên môn & Vị trí
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-danger fw-bold">Loại nhân viên *</label>
                                <select name="staff_type" class="form-select" required>
                                    <option value="Doctor" {{ $staff->staff_type == 'Doctor' ? 'selected' : '' }}>Bác sĩ</option>
                                    <option value="Nurse" {{ $staff->staff_type == 'Nurse' ? 'selected' : '' }}>Điều dưỡng</option>
                                    <option value="Technician" {{ $staff->staff_type == 'Technician' ? 'selected' : '' }}>Kỹ thuật viên</option>
                                    <option value="Pharmacist" {{ $staff->staff_type == 'Pharmacist' ? 'selected' : '' }}>Dược sĩ</option>
                                    <option value="Receptionist" {{ $staff->staff_type == 'Receptionist' ? 'selected' : '' }}>Lễ tân</option>
                                    <option value="Cashier" {{ $staff->staff_type == 'Cashier' ? 'selected' : '' }}>Thu ngân</option>
                                    <option value="Admin" {{ $staff->staff_type == 'Admin' ? 'selected' : '' }}>Quản trị viên</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Học vị / Bằng cấp</label>
                                <input type="text" name="academic_degree" class="form-control" value="{{ $staff->academic_degree }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Khoa / Phòng ban</label>
                                <select name="department_id" class="form-select">
                                    <option value="">-- Chọn Khoa --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ $staff->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phòng làm việc</label>
                                <select name="room_id" class="form-select">
                                    <option value="">-- Chọn Phòng --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ $staff->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Chuyên khoa</label>
                                <input type="text" name="specialty" class="form-control" value="{{ $staff->specialty }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chức vụ</label>
                                <input type="text" name="position" class="form-control" value="{{ $staff->position }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Chứng chỉ hành nghề</label>
                                <input type="text" name="medical_license" class="form-control" value="{{ $staff->medical_license }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Trạng thái</label>
                                <select name="status" class="form-select border-primary">
                                    <option value="1" {{ $staff->status == 1 ? 'selected' : '' }}>Đang làm việc</option>
                                    <option value="2" {{ $staff->status == 2 ? 'selected' : '' }}>Nghỉ phép</option>
                                    <option value="3" {{ $staff->status == 3 ? 'selected' : '' }}>Nghỉ hưu</option>
                                    <option value="0" {{ $staff->status == 0 ? 'selected' : '' }}>Nghỉ việc</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                <i class="fas fa-save me-1"></i> Lưu Thay Đổi
            </button>
        </div>
    </form>
</div>
@endsection