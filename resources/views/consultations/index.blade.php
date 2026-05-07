@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp
@section('content')

<h3>Danh sách phiên khám</h3>

<a href="{{ route('consultations.create') }}" class="btn btn-primary mb-3">+ Tạo phiên khám</a>

<form method="GET" class="mb-3">
    <div class="d-flex gap-2">
        
        <input type="text" 
               name="keyword" 
               class="form-control"
               placeholder="Tìm tên hoặc mã bệnh nhân..."
               value="{{ request('keyword') }}">

        <button class="btn btn-primary">
            Tìm
        </button>

        <!-- 🔥 RESET -->
        <a href="{{ route('consultations.index') }}" 
           class="btn btn-secondary">
            Reset
        </a>

    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Mã PK</th>
            <th>Mã BN</th>
            <th>Bệnh nhân</th>
            <th>Ngày khám</th>
            <th>Triệu chứng</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($consultations as $c)
        <tr>
            <td>{{ $c->id }}</td>
            <td>{{ $c->patient->patient_code ?? '' }}</td>
            <td>{{ $c->patient->full_name ?? '' }}</td>
            <td>{{ $c->consultation_date }}</td>
            <td>{{ Str::limit($c->symptoms, 30) }}</td>
            <td>{{ $c->status_text }}</td>
            <td>
                <a href="{{ route('consultations.show', $c->id) }}" class="btn btn-info btn-sm">Xem</a>
                <a href="{{ route('consultations.edit', $c->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                <form action="{{ route('consultations.destroy', $c->id) }}"
                      method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $consultations->appends(request()->all())->links() }}
</div>

@endsection