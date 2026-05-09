@extends('layouts.app')

@section('title', 'Chi tiết bệnh nhân')
@section('page_title', 'Thông tin bệnh nhân')

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">Mã BN</th>
                            <td>{{ $patient->patient_code }}</td>
                        </tr>
                        <tr>
                            <th>Họ tên</th>
                            <td>{{ $patient->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Giới tính</th>
                            <td>{{ $patient->gender_text }}</td>
                        </tr>
                        <tr>
                            <th>Tuổi</th>
                            <td>{{ $patient->age }}</td>
                        </tr>
                        <tr>
                            <th>SĐT</th>
                            <td>{{ $patient->phone_number }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $patient->email }}</td>
                        </tr>
                        <tr>
                            <th>Địa chỉ</th>
                            <td>{{ $patient->address }}</td>
                        </tr>
                        <tr>
                            <th>CCCD</th>
                            <td>{{ $patient->national_id }}</td>
                        </tr>
                        <tr>
                            <th>Bảo hiểm</th>
                            <td>{{ $patient->insurance_number ?? 'Chưa có' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>{{ $patient->status_text }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </div>

@endsection