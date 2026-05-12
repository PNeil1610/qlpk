@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-user-plus me-2"></i>Thêm Nhân Viên Mới</h3>
        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('staff.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white font-weight-bold border-bottom">
                        <i class="fas fa-id-card text-primary me-1"></i> Thông tin cá nhân
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-danger fw-bold">Họ và tên *</label>
                            <input type="text" name="full_name" class="form-control" required placeholder="Nhập họ và tên">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Giới tính</label>
                                <select name="gender" class="form-select">
                                    <option value="Male">Nam</option>
                                    <option value="Female">Nữ</option>
                                    <option value="Other" selected>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Năm sinh</label>
                                <input type="number" name="yob" class="form-control" placeholder="VD: 1990">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="Nhập SĐT">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CCCD / CMND</label>
                                <input type="text" name="national_id" class="form-control" placeholder="Số CCCD">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="VD: nguyenvan@gmail.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" placeholder="VD: Ninh Kiều, Cần Thơ">
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
                                    <option value="Doctor">Bác sĩ</option>
                                    <option value="Nurse">Điều dưỡng</option>
                                    <option value="Technician">Kỹ thuật viên</option>
                                    <option value="Pharmacist">Dược sĩ</option>
                                    <option value="Receptionist">Lễ tân</option>
                                    <option value="Cashier">Thu ngân</option>
                                    <option value="Admin">Quản trị viên</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Học vị / Bằng cấp</label>
                                <input type="text" name="academic_degree" class="form-control" placeholder="VD: BS.CKII, ThS">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Khoa / Phòng ban</label>
                                <select name="department_id" class="form-select">
                                    <option value="">-- Chọn Khoa --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phòng làm việc</label>
                                <select name="room_id" class="form-select">
                                    <option value="">-- Chọn Phòng --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->room_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Chuyên khoa</label>
                                <input type="text" name="specialty" class="form-control" placeholder="VD: Nội tim mạch">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Chức vụ</label>
                                <input type="text" name="position" class="form-control" placeholder="VD: Trưởng khoa">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chứng chỉ hành nghề</label>
                            <input type="text" name="medical_license" class="form-control" placeholder="Số CCHN">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                <i class="fas fa-save me-1"></i> Lưu Nhân Viên
            </button>
        </div>
    </form>
</div>
@endsection