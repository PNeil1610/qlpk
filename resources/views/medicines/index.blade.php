@extends('layouts.app')

@section('title', 'Quản lý Kho thuốc - Vật tư')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-capsules me-2"></i>Danh sách Thuốc & Vật tư</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('medicines.import.form') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-file-import me-1"></i> Import Excel/CSV
            </a>
            <a href="{{ route('medicines.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Thêm Thuốc Mới
            </a>
        </div>
    </div>

    <!-- Thanh tìm kiếm thuốc cao cấp -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form action="{{ route('medicines.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="keyword" class="form-control border-start-0 ps-0 text-dark" placeholder="Tìm theo tên thuốc, mã thuốc hoặc hoạt chất..." value="{{ $keyword ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary shadow-sm px-3">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    @if(!empty($keyword))
                        <a href="{{ route('medicines.index') }}" class="btn btn-secondary shadow-sm ms-1">
                            <i class="fas fa-sync-alt me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Mã Thuốc</th>
                            <th scope="col">Tên thương mại</th>
                            <th scope="col">Hoạt chất</th>
                            <th scope="col">Nhóm Thuốc</th>
                            <th scope="col" class="text-end">Đơn giá</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicines as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $item->medicine_code ?? '---' }}</td>
                                <td>
                                    <span class="fw-bold text-dark fs-6">{{ $item->medicine_name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted"><i class="fas fa-flask"></i> {{ $item->details->first()->active_ingredient ?? 'Chưa cập nhật' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $item->category->category_name ?? 'Không rõ' }}</span>
                                </td>
                                <td class="text-end">
                                    @if($item->details->first())
                                        <span class="fw-bold text-danger">{{ number_format($item->details->first()->price, 0, ',', '.') }} VNĐ</span>
                                        <br><small class="text-muted">/ {{ $item->details->first()->unit }}</small>
                                    @else
                                        <span class="text-muted fst-italic">Chưa có giá</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status == 1)
                                        <span class="badge rounded-pill bg-success">Có sẵn</span>
                                    @elseif($item->status == 0)
                                        <span class="badge rounded-pill bg-warning text-dark">Tạm ngưng</span>
                                    @elseif($item->status == 2)
                                        <span class="badge rounded-pill bg-secondary">Ngừng SX</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Hết hàng</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('medicines.details', $item->id) }}" class="btn btn-sm btn-success shadow-sm text-white" title="Quản lý chi tiết & giá">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
                                        <a href="{{ route('medicines.show', $item->id) }}" class="btn btn-sm btn-info shadow-sm text-white" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('medicines.edit', $item->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('medicines.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thuốc này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger shadow-sm" title="Xóa">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-prescription-bottle-alt fs-2 mb-3 mt-2 d-block text-black-50"></i>
                                    Chưa có loại thuốc nào trong kho.<br>Hãy bấm <strong>"Thêm Thuốc Mới"</strong> để bắt đầu nhập kho!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $medicines->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection