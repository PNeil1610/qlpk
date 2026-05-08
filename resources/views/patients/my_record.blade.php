@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 text-primary">🏥 Hồ Sơ Y Tế Cá Nhân</h3>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white font-weight-bold">
                    Thông tin hành chính
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Mã Bệnh Nhân:</strong> <span class="badge bg-warning text-dark fs-6">{{ $myPatientRecord->patient_code }}</span></p>
                            <p class="mb-2"><strong>Họ và tên:</strong> {{ $myPatientRecord->full_name }}</p>
                            <p class="mb-2"><strong>Giới tính:</strong> 
                                @if($myPatientRecord->gender == 'Male') Nam
                                @elseif($myPatientRecord->gender == 'Female') Nữ
                                @else Khác
                                @endif
                            </p>
                        </div>
                        
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Số điện thoại:</strong> {{ $myPatientRecord->phone_number ?? 'Chưa cập nhật' }}</p>
                            <p class="mb-2"><strong>Địa chỉ:</strong> {{ $myPatientRecord->address ?? 'Chưa cập nhật' }}</p>
                            <p class="mb-2"><strong>Trạng thái:</strong> 
                                @if($myPatientRecord->status == 1) 
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Đang hoạt động</span>
                                @else 
                                    <span class="text-danger">Đã khóa</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-4">
                <h5 class="alert-heading">Lịch sử khám bệnh</h5>
                <p class="mb-0">Hiện tại hệ thống đang cập nhật tính năng xem lịch sử phiên khám và toa thuốc. Vui lòng quay lại sau!</p>
            </div>

        </div>
    </div>
</div>
@endsection