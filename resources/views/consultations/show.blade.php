@extends('layouts.app')

@section('content')

<h3>Chi tiết phiên khám</h3>

<div class="card">
    <div class="card-body">
        <p><b>Mã PK:</b> {{ $consultation->id }}</p>
        <p><b>Mã BN:</b> {{ $consultation->patient->patient_code }}</p>
        <p><b>Bệnh nhân:</b> {{ $consultation->patient->full_name }}</p>
        <p><b>Ngày khám:</b> {{ $consultation->consultation_date }}</p>
        <p><b>Triệu chứng:</b> {{ $consultation->symptoms }}</p>
        <p><b>Lý do khám:</b> {{ $consultation->chief_complaint }}</p>
        <p><b>Chẩn đoán:</b> {{ $consultation->diagnosis_notes }}</p>
        <p><b>Mã bệnh chuẩn:</b> {{ $consultation->diagnosis_code}}</p>
        <p><b>Trạng thái:</b> {{ $consultation->status_text }}</p>
    </div>
</div>
<a href="{{ route('consultations.index') }}" class="btn btn-secondary">Quay lại</a>

@endsection