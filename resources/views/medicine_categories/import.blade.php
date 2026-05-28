@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-import me-2"></i>Import Loại Thuốc</h3>
        <a href="{{ route('medicine_categories.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <!-- Alert thông báo lỗi hệ thống chung nếu có -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fs-5 me-3"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Kết quả Import sau khi xử lý -->
    @if(session('import_results'))
        @php $res = session('import_results'); @endphp
        <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3 border-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-chart-pie me-2"></i>Kết quả xử lý file dữ liệu loại thuốc</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 text-center mb-4">
                    <div class="col-md-2 col-6">
                        <div class="p-3 bg-light rounded-3 shadow-2xs">
                            <span class="fs-2 fw-bold text-dark d-block">{{ $res['total'] }}</span>
                            <span class="text-muted fs-7 text-uppercase fw-semibold">Tổng số dòng</span>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 bg-success-2xs rounded-3 shadow-2xs border border-success-subtle">
                            <span class="fs-2 fw-bold text-success d-block">{{ $res['success'] }}</span>
                            <span class="text-success fs-7 text-uppercase fw-semibold">Thêm mới</span>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 bg-primary-2xs rounded-3 shadow-2xs border border-primary-subtle">
                            <span class="fs-2 fw-bold text-primary d-block">{{ $res['updated'] }}</span>
                            <span class="text-primary fs-7 text-uppercase fw-semibold">Cập nhật</span>
                        </div>
                    </div>
                    <div class="col-md-2 col-6">
                        <div class="p-3 bg-warning-2xs rounded-3 shadow-2xs border border-warning-subtle">
                            <span class="fs-2 fw-bold text-warning d-block">{{ $res['skipped'] }}</span>
                            <span class="text-warning fs-7 text-uppercase fw-semibold">Bỏ qua (Trùng)</span>
                        </div>
                    </div>
                    <div class="col-md-2 col-12">
                        <div class="p-3 bg-danger-2xs rounded-3 shadow-2xs border border-danger-subtle">
                            <span class="fs-2 fw-bold text-danger d-block">{{ $res['errors_count'] }}</span>
                            <span class="text-danger fs-7 text-uppercase fw-semibold">Lỗi dòng</span>
                        </div>
                    </div>
                </div>

                @if(count($res['errors']) > 0)
                    <div class="border border-danger-subtle rounded-3 p-3 bg-danger-2xs">
                        <h6 class="text-danger fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Chi tiết lỗi phát hiện tại các dòng:</h6>
                        <div class="overflow-auto" style="max-height: 250px;">
                            <ul class="list-unstyled mb-0 text-danger fs-6">
                                @foreach($res['errors'] as $err)
                                    <li class="py-1 border-bottom border-danger-subtle last-border-0">
                                        <i class="fas fa-caret-right me-2"></i>{!! $err !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success border-0 rounded-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-check-circle fs-4 me-3"></i>
                        <span class="fw-semibold">Tuyệt vời! Dữ liệu file import hoàn toàn hợp lệ, không phát hiện bất kỳ lỗi định dạng nào.</span>
                    </div>
                @endif

                @if(isset($res['similar_warnings']) && count($res['similar_warnings']) > 0)
                    <div class="border border-warning-subtle rounded-3 p-3 bg-warning-2xs mt-3">
                        <h6 class="text-warning-dark fw-bold mb-2"><i class="fas fa-exclamation-circle me-2"></i>Cảnh báo phát hiện trùng lặp gần đúng (Fuzzy Match Warnings):</h6>
                        <div class="overflow-auto" style="max-height: 250px;">
                            <ul class="list-unstyled mb-0 text-warning-dark fs-6 font-monospace" style="font-size: 0.9rem;">
                                @foreach($res['similar_warnings'] as $warning)
                                    <li class="py-2 border-bottom border-warning-subtle last-border-0">
                                        <i class="fas fa-info-circle me-2 text-warning"></i>{!! $warning !!}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row g-4">
        <!-- Form Upload -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title text-dark fw-bold mb-4"><i class="fas fa-upload text-primary me-2"></i>Tải Lên File Dữ Liệu Loại Thuốc</h5>

                    <form action="{{ route('medicine_categories.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Drag & Drop Zone -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary">Chọn File Excel/CSV nguồn</label>
                            <div class="drag-drop-zone p-5 border border-2 border-dashed border-primary-subtle rounded-4 text-center bg-light position-relative cursor-pointer transition-all hover-bg-primary-5" id="dropZone">
                                <input type="file" name="csv_file" id="fileInput" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" accept=".csv" required>
                                <div class="py-3" id="dropZoneContent">
                                    <div class="mb-3">
                                        <i class="fas fa-cloud-upload-alt text-primary display-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Kéo & Thả file CSV vào đây</h6>
                                    <p class="text-muted fs-7 mb-2">hoặc nhấn để duyệt file từ máy tính của bạn</p>
                                    <span class="badge bg-secondary rounded-pill px-3 py-2 fs-8">Hỗ trợ định dạng: .csv (UTF-8)</span>
                                </div>
                                <div class="py-3 d-none" id="selectedFileContent">
                                    <div class="mb-3">
                                        <i class="fas fa-file-csv text-success display-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" id="fileName">Tên_file_mau.csv</h6>
                                    <p class="text-muted fs-7 mb-2" id="fileSize">0 KB</p>
                                    <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1 rounded-pill mt-2" id="removeFileBtn">
                                        <i class="fas fa-times me-1"></i> Thay đổi file
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Cấu hình khi trùng lặp -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary mb-3">Khi phát hiện Loại Thuốc đã tồn tại:</label>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="behavior-card p-3 border rounded-3 cursor-pointer transition-all position-relative" id="behaviorSkipCard">
                                        <input class="form-check-input position-absolute top-3 end-3" type="radio" name="duplicate_behavior" id="behaviorSkip" value="skip" checked>
                                        <label class="form-check-label w-100 cursor-pointer d-flex flex-column h-100" for="behaviorSkip">
                                            <span class="fw-bold text-dark mb-1 d-block"><i class="fas fa-clone text-warning me-2"></i>Bỏ qua trùng tên</span>
                                            <span class="text-muted fs-7 lh-sm">Giữ nguyên mô tả loại thuốc cũ và bỏ qua dòng trùng trong file import.</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="behavior-card p-3 border rounded-3 cursor-pointer transition-all position-relative" id="behaviorUpdateCard">
                                        <input class="form-check-input position-absolute top-3 end-3" type="radio" name="duplicate_behavior" id="behaviorUpdate" value="update">
                                        <label class="form-check-label w-100 cursor-pointer d-flex flex-column h-100" for="behaviorUpdate">
                                            <span class="fw-bold text-dark mb-1 d-block"><i class="fas fa-sync-alt text-primary me-2"></i>Cập nhật ghi đè</span>
                                            <span class="text-muted fs-7 lh-sm">Cập nhật đè phần mô tả mới từ file cho loại thuốc đã có.</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-3 rounded-3 shadow-sm fw-bold fs-6">
                                <i class="fas fa-file-import me-2"></i>Bắt đầu Import & Đồng bộ hóa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Hướng dẫn & Tải file mẫu -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 rounded-3 h-100 bg-white">
                <div class="card-body p-4">
                    <h5 class="card-title text-dark fw-bold mb-3"><i class="fas fa-info-circle text-info me-2"></i>Hướng Dẫn Chuẩn Bị File</h5>
                    
                    <p class="text-secondary fs-6 mb-4">
                        Vui lòng tải file mẫu cấu trúc Loại thuốc và điền các trường chính xác để hệ thống phân loại tủ thuốc khoa học.
                    </p>

                    <!-- Nút Tải file mẫu -->
                    <div class="p-3 bg-light rounded-3 mb-4 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold text-dark d-block">Tải file mẫu loại thuốc</span>
                            <span class="text-muted fs-7">File CSV chứa cột tiêu đề và ví dụ mẫu</span>
                        </div>
                        <a href="{{ route('medicine_categories.import.template') }}" class="btn btn-success shadow-sm">
                            <i class="fas fa-file-csv me-1"></i> Tải File Mẫu
                        </a>
                    </div>

                    <!-- Bảng cấu trúc -->
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-table text-secondary me-2"></i>Cấu trúc các cột tiêu đề:</h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover table-striped mb-0 align-middle fs-6">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th scope="col" class="ps-3 py-2">Tên cột</th>
                                    <th scope="col" class="py-2">Kiểu dữ liệu</th>
                                    <th scope="col" class="py-2 text-center">Bắt buộc</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-3 fw-bold text-dark py-2">Tên loại thuốc</td>
                                    <td class="text-secondary py-2">Chữ, tối đa 150 kí tự</td>
                                    <td class="text-center py-2"><span class="badge bg-danger rounded-pill">Có</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-bold text-dark py-2">Mô tả</td>
                                    <td class="text-secondary py-2">Đoạn văn mô tả nhóm thuốc</td>
                                    <td class="text-center py-2"><span class="badge bg-secondary rounded-pill">Không</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Lưu ý quan trọng -->
                    <div class="mt-4 p-3 border-start border-4 border-warning rounded-end bg-warning-2xs">
                        <h6 class="text-warning-dark fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Chú ý cách lưu File:</h6>
                        <p class="text-secondary fs-7 mb-0 lh-sm">
                            Đảm bảo lưu file Excel dưới dạng **CSV UTF-8 (Comma delimited) (*.csv)** để các chữ có dấu như "Kháng sinh", "Giảm đau" không bị biến dạng sau khi tải lên.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .drag-drop-zone {
        transition: all 0.25s ease-in-out;
    }
    .drag-drop-zone:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
    }
    .cursor-pointer {
        cursor: pointer !important;
    }
    .fs-7 {
        font-size: 0.85rem !important;
    }
    .fs-8 {
        font-size: 0.75rem !important;
    }
    .hover-bg-primary-5:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    }
    
    .behavior-card {
        border-color: #dee2e6 !important;
        background-color: #fff;
    }
    .behavior-card:hover {
        border-color: #9ec5fe !important;
        background-color: rgba(13, 110, 253, 0.02);
    }
    .behavior-card.active {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }

    .bg-success-2xs {
        background-color: rgba(40, 167, 69, 0.08);
    }
    .bg-primary-2xs {
        background-color: rgba(0, 123, 255, 0.08);
    }
    .bg-warning-2xs {
        background-color: rgba(255, 193, 7, 0.08);
    }
    .bg-danger-2xs {
        background-color: rgba(220, 53, 69, 0.08);
    }
    .text-warning-dark {
        color: #856404;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const dropZone = $('#dropZone');
        const fileInput = $('#fileInput');
        const dropZoneContent = $('#dropZoneContent');
        const selectedFileContent = $('#selectedFileContent');
        const fileName = $('#fileName');
        const fileSize = $('#fileSize');

        fileInput.on('change', function(e) {
            handleFileSelect(this.files[0]);
        });

        function handleFileSelect(file) {
            if (file) {
                const name = file.name;
                const size = (file.size / 1024).toFixed(1) + ' KB';
                
                fileName.text(name);
                fileSize.text(size);
                
                dropZoneContent.addClass('d-none');
                selectedFileContent.removeClass('d-none');
                dropZone.addClass('border-success').removeClass('border-primary-subtle');
            }
        }

        $('#removeFileBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.val('');
            dropZoneContent.removeClass('d-none');
            selectedFileContent.addClass('d-none');
            dropZone.removeClass('border-success').addClass('border-primary-subtle');
        });

        dropZone.on('dragover', function(e) {
            e.preventDefault();
            dropZone.addClass('bg-primary-2xs').css('border-color', 'var(--bs-primary)');
        });

        dropZone.on('dragleave drop', function(e) {
            e.preventDefault();
            dropZone.removeClass('bg-primary-2xs').css('border-color', '');
        });

        dropZone.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                fileInput[0].files = files;
                handleFileSelect(files[0]);
            }
        });

        function updateBehaviorCards() {
            if ($('#behaviorSkip').is(':checked')) {
                $('#behaviorSkipCard').addClass('active');
                $('#behaviorUpdateCard').removeClass('active');
            } else {
                $('#behaviorUpdateCard').addClass('active');
                $('#behaviorSkipCard').removeClass('active');
            }
        }

        updateBehaviorCards();

        $('#behaviorSkipCard').on('click', function() {
            $('#behaviorSkip').prop('checked', true);
            updateBehaviorCards();
        });

        $('#behaviorUpdateCard').on('click', function() {
            $('#behaviorUpdate').prop('checked', true);
            updateBehaviorCards();
        });
    });
</script>
@endpush
