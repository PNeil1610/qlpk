@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-compress-arrows-alt me-2"></i>Gộp Loại Thuốc</h3>
        <a href="{{ route('medicine_categories.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Hộp thông báo lỗi biểu mẫu -->
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">
                    <h5 class="card-title text-dark fw-bold mb-3">
                        <i class="fas fa-compress text-warning me-2"></i>Quy Trình Gộp Nhóm Dữ Liệu
                    </h5>
                    <p class="text-secondary fs-6 mb-4">
                        Nếu bạn lỡ tạo hai Loại thuốc bị trùng lắp (ví dụ: <strong>"Kháng sinh"</strong> và <strong>"Thuốc kháng sinh"</strong>), tính năng này sẽ giúp bạn chuyển toàn bộ các thuốc đang thuộc nhóm trùng lặp sang nhóm chính xác và tự động xóa nhóm trùng lặp rác đó một cách an toàn nhất!
                    </p>

                    <!-- Cảnh báo quan trọng -->
                    <div class="alert alert-warning border-0 rounded-3 mb-4 p-3 d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle fs-4 text-warning-dark me-3 mt-1"></i>
                        <div>
                            <h6 class="fw-bold text-warning-dark mb-1">Cảnh Báo Dữ Liệu:</h6>
                            <p class="mb-0 text-secondary fs-7">
                                Hành động này sẽ thực hiện cập nhật toàn bộ thuốc thuộc nhóm nguồn sang nhóm đích và xóa vĩnh viễn nhóm nguồn. Các liên kết đơn giá và phiên bản sẽ được giữ nguyên 100%. Hành động này <strong>không thể khôi phục lại</strong>.
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('medicine_categories.merge') }}" method="POST" id="mergeForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Nhóm nguồn (Muốn xóa) -->
                            <div class="col-md-6">
                                <div class="p-3 border border-danger-subtle rounded-3 bg-danger-2xs">
                                    <label class="form-label fw-bold text-danger mb-2"><i class="fas fa-trash-alt me-2"></i>1. Chọn Nhóm Muốn Loại Bỏ (Nhóm trùng lặp)</label>
                                    <select name="source_category_id" id="sourceCategory" class="form-control select2" required style="width: 100%;">
                                        <option value="" disabled selected>-- Chọn nhóm muốn xóa --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ $cat->category_code }})</option>
                                        @endforeach
                                    </select>
                                    <span class="form-text fs-8 text-muted d-block mt-2">Ví dụ: Chọn nhóm "Kháng sinh" (nhóm bị gõ thiếu chữ).</span>
                                </div>
                            </div>

                            <!-- Nhóm đích (Muốn giữ lại) -->
                            <div class="col-md-6">
                                <div class="p-3 border border-success-subtle rounded-3 bg-success-2xs">
                                    <label class="form-label fw-bold text-success mb-2"><i class="fas fa-check-circle me-2"></i>2. Chọn Nhóm Muốn Giữ Lại (Nhóm chuẩn)</label>
                                    <select name="destination_category_id" id="destinationCategory" class="form-control select2" required style="width: 100%;">
                                        <option value="" disabled selected>-- Chọn nhóm muốn giữ lại --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->category_name }} ({{ $cat->category_code }})</option>
                                        @endforeach
                                    </select>
                                    <span class="form-text fs-8 text-muted d-block mt-2">Ví dụ: Chọn nhóm "Thuốc kháng sinh" (nhóm viết chuẩn).</span>
                                </div>
                            </div>
                        </div>

                        <!-- Xác nhận hành động -->
                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-warning py-3 rounded-3 shadow-sm fw-bold text-dark fs-6" id="submitBtn">
                                <i class="fas fa-compress-arrows-alt me-2"></i>Bắt Đầu Gộp Dữ Liệu & Đồng Bộ Kho
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* CSS Premium cho Select2 */
    .select2-container--default .select2-selection--single {
        border: 1px solid #dee2e6 !important;
        border-radius: 0.375rem !important;
        height: 42px !important;
        padding-top: 6px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #212529 !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
    }

    .fs-7 {
        font-size: 0.85rem !important;
    }
    .fs-8 {
        font-size: 0.75rem !important;
    }
    .bg-success-2xs {
        background-color: rgba(40, 167, 69, 0.05);
    }
    .bg-danger-2xs {
        background-color: rgba(220, 53, 69, 0.05);
    }
    .text-warning-dark {
        color: #856404;
    }
</style>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Khởi tạo select2 tìm kiếm loại thuốc
        $('#sourceCategory').select2({
            placeholder: "-- Tìm hoặc chọn nhóm muốn xóa --",
            allowClear: true,
            width: '100%'
        });

        $('#destinationCategory').select2({
            placeholder: "-- Tìm hoặc chọn nhóm muốn giữ lại --",
            allowClear: true,
            width: '100%'
        });

        // Xác nhận gộp dữ liệu
        $('#mergeForm').on('submit', function(e) {
            const sourceText = $('#sourceCategory option:selected').text();
            const destText = $('#destinationCategory option:selected').text();
            
            if (!confirm(`Bạn có chắc chắn muốn thực hiện hành động này?\n\nToàn bộ thuốc từ nhóm "${sourceText}" sẽ được gộp sang nhóm "${destText}" và xóa vĩnh viễn nhóm "${sourceText}".\n\nHành động này KHÔNG THỂ HOÀN TÁC!`)) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
