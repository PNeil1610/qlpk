@extends('layouts.app')

@section('content')
<h3>Danh sách bệnh nhân</h3>
<a href="{{ route('patients.create') }}" class="btn btn-primary mb-3">+ Thêm</a>

<form method="GET" action="{{ route('patients.index') }}" class="mb-3">
    <div style="display:flex; gap:10px;">
        <input type="text" name="keyword" class="form-control"
               placeholder="Nhập tên, SĐT hoặc mã bệnh nhân..."
               value="{{ request('keyword') }}">

        <button class="btn btn-primary">Tìm</button>

        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Tên</th>
        <th>Tuổi</th>
        <th>Giới tính</th>
        <th>SĐT</th>
        <th>Trạng thái</th>
        <th>Hành động</th>
    </tr>

    @foreach($patients as $p)
    <tr>
        <td>{{ $p->id }}</td>
        <td>{{ $p->full_name }}</td>
        <td>{{ $p->age }}</td>
        <td>{{ $p->gender_text }}</td>
        <td>{{ $p->phone_number }}</td>
        <td>
    @switch($p->status)
        @case(1)
            <span class="badge bg-secondary">Hoạt động</span>
            @break
        @case(0)
            <span class="badge bg-dark">Không hoạt động</span>
            @break
    @endswitch
</td>
        <td>
    <!-- XEM -->
    <a href="{{ route('patients.show', $p->id) }}" 
       class="btn btn-info btn-sm">Xem</a>

    <!-- SỬA -->
    <a href="{{ route('patients.edit', $p->id) }}" 
       class="btn btn-warning btn-sm">Sửa</a>

    <!-- XOÁ -->
    <form action="{{ route('patients.destroy', $p->id) }}" 
          method="POST" style="display:inline;">
        @csrf
        @method('DELETE')

        <button type="submit" 
                class="btn btn-danger btn-sm"
                onclick="return confirm('Bạn có chắc muốn xoá không?')">
            Xoá
        </button>
    </form>
</td>
    </tr>
    @endforeach
</table>
<div class="d-flex justify-content-center">
    {{ $patients->appends(request()->all())->links() }}
</div>
@endsection