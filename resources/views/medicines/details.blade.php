@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary"><i class="fas fa-tags me-2"></i>Quản lý Đơn giá & Chi tiết</h3>
            <span class="text-muted fw-bold fs-5">Thuốc: {{ $medicine->medicine_name }} ({{ $medicine->medicine_code }})</span>
        </div>
        <div>
            <a href="#" class="btn btn-primary shadow-sm me-2">
                <i class="fas fa-plus-circle me-1"></i> Thêm phiên bản mới
            </a>
            <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Ngày tạo</th>
                            <th scope="col">Đơn vị tính</th>
                            <th scope="col" class="text-end">Giá bán (VNĐ)</th>
                            <th scope="col">Hoạt chất / Bảo quản</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $detail)
                            <tr class="{{ $detail->status == 1 ? 'table-success' : '' }}">
                                <td class="ps-4 text-muted">{{ $detail->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold">{{ $detail->unit }}</td>
                                <td class="text-end fw-bold {{ $detail->status == 1 ? 'text-danger' : 'text-muted' }}">
                                    {{ number_format($detail->price, 0, ',', '.') }} đ
                                </td>
                                <td>
                                    <small class="d-block"><strong>HC:</strong> {{ $detail->active_ingredient ?? '---' }}</small>
                                    <small class="d-block text-muted"><strong>BQ:</strong> {{ $detail->storage_condition ?? '---' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($detail->status == 1)
                                        <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> Đang áp dụng</span>
                                    @else
                                        <span class="badge bg-secondary px-2 py-1"><i class="fas fa-history me-1"></i> Lịch sử cũ</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" title="Sửa chi tiết này"><i class="fas fa-edit"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Thuốc này chưa có đơn vị tính và mức giá nào.<br>Hãy bấm <strong>"Thêm phiên bản mới"</strong> để thiết lập giá!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection