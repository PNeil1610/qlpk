@extends('layouts.app')

@section('content')

    <h3>Chi tiết bệnh</h3>

    <table class="table table-bordered">

        <tr>
            <th>Mã bệnh</th>
            <td>{{ $diagnosis->diagnosis_code }}</td>
        </tr>

        <tr>
            <th>Tên bệnh</th>
            <td>{{ $diagnosis->diagnosis_name }}</td>
        </tr>

        <tr>
            <th>Phiên bản ICD</th>
            <td>{{ $diagnosis->icd_version }}</td>
        </tr>

        <tr>
            <th>Mô tả</th>
            <td>{{ $diagnosis->description }}</td>
        </tr>

        <tr>
            <th>Trạng thái</th>
            <td>

                @if($diagnosis->status == 1)
                    Hoạt động
                @else
                    Ngưng hoạt động
                @endif

            </td>
        </tr>

    </table>

    <a href="{{ route('diagnoses.index') }}" class="btn btn-secondary">
        Quay lại
    </a>

@endsection