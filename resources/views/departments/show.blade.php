@extends('layouts.app')

@section('content')

    <h3>Chi tiết khoa</h3>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">Mã khoa</th>
                            <td>{{ $department->dept_code }}</td>
                        </tr>
                        <tr>
                            <th>Tên khoa</th>
                            <td>{{ $department->dept_name }}</td>
                        </tr>
                        <tr>
                            <th>Mô tả</th>
                            <td>{{ $department->description ?? 'Không có mô tả' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>
                                @if($department->status == 1)
                                    Hoạt động
                                @elseif($department->status == 0)
                                    Không hoạt động
                                @else
                                    Đang xây dựng
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ $department->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Cập nhật lần cuối</th>
                            <td>{{ $department->updated_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('departments.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>

@endsection