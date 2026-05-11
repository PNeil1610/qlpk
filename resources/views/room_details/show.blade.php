@extends('layouts.app')

@section('content')
    <h3>Thông tin chi tiết phòng</h3>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">ID</th>
                            <td>{{ $roomDetail->id }}</td>
                        </tr>
                        <tr>
                            <th>Phòng</th>
                            <td>{{ $roomDetail->room->room_name ?? '--' }} ({{ $roomDetail->room->room_code ?? '--' }})</td>
                        </tr>
                        <tr>
                            <th>Danh sách thiết bị</th>
                            <td>{!! nl2br(e($roomDetail->equipment_list ?? '--')) !!}</td>
                        </tr>
                        <tr>
                            <th>Tầng</th>
                            <td>{{ $roomDetail->floor ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Sức chứa</th>
                            <td>{{ $roomDetail->capacity ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Diện tích (m2)</th>
                            <td>{{ $roomDetail->area_sqm ?? '--' }}</td>
                        </tr>
                        <tr>
                            <th>Ghi chú</th>
                            <td>{!! nl2br(e($roomDetail->notes ?? '--')) !!}</td>
                        </tr>
                        <tr>
                            <th>Lần dọn dẹp cuối</th>
                            <td>{{ $roomDetail->last_cleaned_at ? $roomDetail->last_cleaned_at->format('d/m/Y H:i') : '--' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('room_details.edit', $roomDetail->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('room_details.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
@endsection
