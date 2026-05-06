@extends('layouts.app')

@section('title', 'Chi tiết bệnh nhân')
@section('page_title', 'Thông tin bệnh nhân')

@section('content')

<div class="card">
    <div class="card-body">

        <p><strong>Họ tên:</strong> {{ $patient->full_name }}</p>
        <p><strong>Giới tính:</strong> {{ $patient->gender }}</p>
        <p><strong>Ngày sinh:</strong> {{ $patient->date_of_birth }}</p>
        <p><strong>SĐT:</strong> {{ $patient->phone_number }}</p>
        <p><strong>Email:</strong> {{ $patient->email }}</p>
        <p><strong>Địa chỉ:</strong> {{ $patient->address }}</p>
        <p><strong>Nhóm máu:</strong> {{ $patient->blood_type }}</p>
        <p><strong>CCCD:</strong> {{ $patient->national_id }}</p>

    </div>
</div>

<a href="{{ route('patients.index') }}" class="btn btn-secondary mt-3">Quay lại</a>

@endsection