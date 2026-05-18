@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-prescription me-2"></i>Toa thuốc mẫu</h3>
        <a href="{{ route('doctor.templates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tạo toa mẫu mới
        </a>
    </div>

    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('doctor.templates.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm tên toa, tên bệnh..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Tên Toa Mẫu</th>
                            <th scope="col">Tên Bệnh</th>
                            <th scope="col">Mô tả</th>
                            <th scope="col">Số loại thuốc</th>
                            <th scope="col" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $item)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $item->template_name }}</td>
                            <td>{{ $item->disease_name ?: '---' }}</td>
                            <td>{{ Str::limit($item->description, 50) ?: '---' }}</td>
                            <td><span class="badge bg-info">{{ $item->details->count() }} thuốc</span></td>
                            <td class="text-center">
                                <a href="{{ route('doctor.templates.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa toa mẫu">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('doctor.templates.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa toa mẫu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa toa mẫu">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                Bạn chưa có toa thuốc mẫu nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
