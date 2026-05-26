@extends('layouts.app')

@section('title', 'Cấu hình phòng khám')

@section('content')
<div class="container-fluid px-4 mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary">
                <i class="fas fa-cog me-2"></i>Cấu hình phòng khám
            </h3>
            <small class="text-muted">Quản lý thông tin hiển thị & cài đặt hệ thống</small>
        </div>
        <a href="{{ route('settings.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Thêm cấu hình
        </a>
    </div>

    {{-- CẤU HÌNH ĐANG ÁP DỤNG --}}
    @if($activeSetting)
    <div class="alert border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #e8f4e8, #d4edda); border-left: 5px solid #198754 !important;">
        <div class="d-flex align-items-start gap-3">
            <div class="text-success mt-1" style="font-size:1.5rem;">✅</div>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-success mb-2">Cấu hình đang áp dụng</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <strong>Tên phòng khám:</strong><br>
                        <span class="text-dark fs-6 fw-semibold">{{ $activeSetting->clinic_name }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Điện thoại:</strong><br>
                        <span>{{ $activeSetting->phone_number ?? '(chưa có)' }}</span>
                    </div>
                    <div class="col-md-4">
                        <strong>Email:</strong><br>
                        <span>{{ $activeSetting->email ?? '(chưa có)' }}</span>
                    </div>
                    <div class="col-12">
                        <strong>Địa chỉ:</strong>
                        <span>{{ $activeSetting->address ?? '(chưa có)' }}</span>
                    </div>
                    @if($activeSetting->additional_info)
                    <div class="col-12">
                        <strong>Thông tin thêm:</strong>
                        <span class="text-muted">{{ Str::limit($activeSetting->additional_info, 150) }}</span>
                    </div>
                    @endif
                </div>
                <div class="mt-2 text-muted small">
                    Cập nhật lần cuối bởi <strong>{{ $activeSetting->creator?->full_name ?? '---' }}</strong>
                    lúc {{ $activeSetting->updated_at?->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning border-0 shadow-sm mb-4">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Chưa có cấu hình nào đang được áp dụng. Hệ thống đang dùng thông tin mặc định.
        <a href="{{ route('settings.create') }}" class="fw-bold ms-2">Tạo cấu hình ngay →</a>
    </div>
    @endif

    {{-- DANH SÁCH CẤU HÌNH --}}
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-secondary">
                <i class="fas fa-list me-2"></i>Tất cả cấu hình ({{ $settings->total() }} bản ghi)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Tên phòng khám</th>
                            <th>Điện thoại</th>
                            <th>Địa chỉ</th>
                            <th style="width:130px;">Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Ngày tạo</th>
                            <th style="width:200px;" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($settings as $s)
                        <tr class="{{ $s->status == 1 ? 'table-success' : '' }}">
                            <td class="text-muted small">{{ $s->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $s->clinic_name }}</div>
                                @if($s->email)
                                    <small class="text-muted">{{ $s->email }}</small>
                                @endif
                            </td>
                            <td>{{ $s->phone_number ?? '---' }}</td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($s->address, 50) ?? '---' }}</span>
                            </td>
                            <td>
                                @if($s->status == 1)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i>Đang áp dụng
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">Ngưng áp dụng</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $s->creator?->full_name ?? '---' }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $s->created_at?->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    {{-- Áp dụng --}}
                                    @if($s->status == 0)
                                    <form action="{{ route('settings.activate', $s->id) }}" method="POST"
                                          onsubmit="return confirm('Áp dụng cấu hình này cho hệ thống?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Áp dụng">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('settings.deactivate', $s->id) }}" method="POST"
                                          onsubmit="return confirm('Thu hồi cấu hình này?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" title="Thu hồi">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </form>
                                    @endif

                                    {{-- Sửa --}}
                                    <a href="{{ route('settings.edit', $s->id) }}"
                                       class="btn btn-primary btn-sm" title="Chỉnh sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Xóa --}}
                                    @if($s->status == 0)
                                    <form action="{{ route('settings.destroy', $s->id) }}" method="POST"
                                          onsubmit="return confirm('Xóa cấu hình này vĩnh viễn?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <button class="btn btn-danger btn-sm disabled" title="Thu hồi trước khi xóa"
                                            disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-cog fa-2x mb-2 d-block"></i>
                                Chưa có cấu hình nào. <a href="{{ route('settings.create') }}">Tạo ngay</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($settings->hasPages())
        <div class="card-footer bg-white">
            {{ $settings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
