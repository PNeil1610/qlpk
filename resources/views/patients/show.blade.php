@extends('layouts.app')

@section('title', 'Chi tiết bệnh nhân')
@section('page_title', 'Thông tin bệnh nhân')

@section('content')

<div class="card">
    <div class="card-body">

        <p><strong>Họ tên:</strong> {{ $patient->full_name }}</p>
        <p><b>Mã BN:</b> {{ $patient->patient_code }}</p>
        <p><b>Giới tính:</b> {{ $patient->gender_text }}</p>
        <p><strong>Tuổi:</strong> {{ $patient->age }}</p>
        <p><strong>SĐT:</strong> {{ $patient->phone_number }}</p>
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Địa chỉ:</strong> {{ $patient->address }}</p>
        <p><strong>CCCD:</strong> {{ $patient->national_id }}</p>
        <p><b>Bảo hiểm:</b> {{ $patient->insurance_number ?? 'Chưa có' }}</p>
        <p><b>Trạng thái:</b> {{ $patient->status_text }}</p>
</div>
</div>

<a href="{{ route('patients.index') }}" class="btn btn-secondary mt-3">Quay lại</a>

@endsection