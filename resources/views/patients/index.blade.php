@extends('layouts.app')

@section('title', 'Bệnh nhân')
@section('page_title', 'Danh sách bệnh nhân')

@section('content')

<a href="{{ route('patients.create') }}" class="btn btn-primary mb-3">
    + Thêm bệnh nhân
</a>

<table class="table table-hover table-bordered">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Họ tên</th>
            <th>Giới tính</th>
            <th>SĐT</th>
            <th>Nhóm máu</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $p)
        <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->full_name }}</td>
            <td>{{ $p->gender }}</td>
            <td>{{ $p->phone_number }}</td>
            <td>{{ $p->blood_type }}</td>
            <td>
                <a href="{{ route('patients.show', $p->id) }}" class="btn btn-info btn-sm">Xem</a>
                <a href="{{ route('patients.edit', $p->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                <form action="{{ route('patients.destroy', $p->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button onclick="return confirm('Xóa?')" class="btn btn-danger btn-sm">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $patients->links() }}

@endsection