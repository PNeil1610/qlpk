@extends('layouts.app')

@section('content')

    <h3>Chi tiết phiên khám</h3>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">Mã PK</th>
                            <td>{{ $consultation->id }}</td>
                        </tr>
                        <tr>
                            <th>Mã BN</th>
                            <td>{{ $consultation->patient->patient_code }}</td>
                        </tr>
                        <tr>
                            <th>Bệnh nhân</th>
                            <td>{{ $consultation->patient->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Ngày khám</th>
                            <td>{{ $consultation->consultation_date }}</td>
                        </tr>
                        <tr>
                            <th>Lý do khám</th>
                            <td>{{ $consultation->chief_complaint }}</td>
                        </tr>
                        <tr>
                            <th>Triệu chứng</th>
                            <td>{{ $consultation->symptoms }}</td>
                        </tr>
                        <tr>
                            <th>Nhiệt độ</th>
                            <td>{{ $consultation->temperature }}</td>
                        </tr>
                        <tr>
                            <th>Huyết áp</th>
                            <td>{{ $consultation->blood_pressure }}</td>
                        </tr>
                        <tr>
                            <th>Chẩn đoán</th>
                            <td>{{ $consultation->diagnosis_notes }}</td>
                        </tr>
                        <tr>
                            <th>Mã bệnh chuẩn (tham khảo)</th>
                            <td>{{ $consultation->diagnosis_code }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tái khám</th>
                            <td>{{ $consultation->follow_up_date }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>{{ $consultation->status_text }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>
    </div>

@endsection