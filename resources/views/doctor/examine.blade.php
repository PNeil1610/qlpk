@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                @if($consultation->status === \App\Models\Consultation::STATUS_COMPLETED)
                    <h3 class="mb-0 text-secondary"><i class="fas fa-eye me-2"></i>Xem phiếu khám (Chỉ đọc)</h3>
                @else
                    <h3 class="mb-0 text-primary"><i class="fas fa-stethoscope me-2"></i>Khám bệnh & Kê đơn thuốc</h3>
                @endif
                <span class="text-muted">Bệnh nhân: <strong>{{ $consultation->patient->full_name }}</strong> &mdash; Mã PK:
                    <strong>#{{ $consultation->id }}</strong></span>
            </div>
            <a href="{{ route('consultations.doctor') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        @if($consultation->status === \App\Models\Consultation::STATUS_COMPLETED)
            <div class="alert alert-secondary border-0 shadow-sm mb-4">
                <i class="fas fa-lock me-2"></i><strong>Phiếu khám này đã hoàn thành thanh toán.</strong> Bạn chỉ có thể xem
                thông tin, không thể chỉnh sửa.
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row g-4">
            {{-- ====== CỘT TRÁI: THÔNG TIN HÀNH CHÍNH (CHỈ ĐỌC) ====== --}}
            <div class="col-xl-3 col-lg-4">
                <div class="card shadow-sm border-0 border-top border-info border-3 mb-3">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="fas fa-clipboard-list text-info me-2"></i>Thông tin phiên khám
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <th class="text-muted fw-normal">Bệnh nhân</th>
                                <td class="fw-bold">{{ $consultation->patient->full_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Mã BN</th>
                                <td>{{ $consultation->patient->patient_code }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Ngày khám</th>
                                <td>{{ $consultation->consultation_date ? \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') : '--' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Lý do khám</th>
                                <td>{{ $consultation->chief_complaint ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Nhiệt độ</th>
                                <td>{{ $consultation->temperature ? $consultation->temperature . ' °C' : '--' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Huyết áp</th>
                                <td>{{ $consultation->blood_pressure ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted fw-normal">Triệu chứng</th>
                                <td><span class="text-danger">{{ $consultation->symptoms ?? '--' }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Toa thuốc hiện tại nếu đã có --}}
                @if($consultation->prescriptions && $consultation->prescriptions->count() > 0)
                    <div class="card shadow-sm border-0 border-top border-success border-3 mb-3">
                        <div class="card-header bg-white fw-bold py-3 text-success">
                            <i class="fas fa-prescription-bottle me-2"></i>Toa thuốc đã kê
                        </div>
                        <div class="card-body p-2">
                            @foreach($consultation->prescriptions as $pres)
                                <div class="d-flex mb-1">
                                    <a href="{{ route('prescriptions.show', $pres->id) }}"
                                        class="btn btn-sm btn-outline-success flex-grow-1 text-start overflow-hidden">
                                        <i class="fas fa-file-prescription me-1"></i> Toa #{{ $pres->id }} &mdash;
                                        {{ $pres->prescribed_at ? \Carbon\Carbon::parse($pres->prescribed_at)->format('d/m H:i') : '' }}
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="openSaveTemplateModal({{ $pres->id }})" title="Lưu làm toa mẫu">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    @if($pres->status == 1 && $consultation->status !== \App\Models\Consultation::STATUS_COMPLETED)
                                        <form action="{{ route('prescriptions.destroy', $pres->id) }}" method="POST" class="ms-1"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa toa thuốc này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times me-1"></i>Xóa
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- LỊCH SỬ KHÁM BỆNH NHÂN --}}
                <div class="card shadow-sm border-0 border-top border-secondary border-3 mb-3">
                    <div class="card-header bg-white fw-bold py-3 text-secondary d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-history me-2"></i>Lịch sử BN ({{ isset($patientHistory) ? $patientHistory->count() : 0 }} lần)</span>
                    </div>
                    <div class="card-body p-2">
                        @if(isset($patientHistory) && $patientHistory->count() > 0)
                            @foreach($patientHistory->take(5) as $hist)
                                <div class="mb-1">
                                    <a href="{{ route('consultations.show', $hist->id) }}?reuse_mode=1&from_consultation={{ $consultation->id }}"
                                        class="btn btn-sm btn-outline-secondary w-100 text-start overflow-hidden d-flex justify-content-between align-items-center" title="Xem chi tiết & Dùng lại toa">
                                        <span>
                                            <i class="fas fa-calendar-alt me-1"></i> {{ $hist->consultation_date ? \Carbon\Carbon::parse($hist->consultation_date)->format('d/m/Y') : '' }}
                                        </span>
                                        <span class="badge bg-secondary">
                                            {{ $hist->diagnosis_code ?? 'KXĐ' }}
                                        </span>
                                    </a>
                                </div>
                            @endforeach

                            @if($patientHistory->count() > 5)
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#moreHistoryCollapse" aria-expanded="false" aria-controls="moreHistoryCollapse">
                                        <i class="fas fa-chevron-down me-1"></i> Sổ xuống tìm kiếm thêm
                                    </button>
                                </div>
                                <div class="collapse mt-2" id="moreHistoryCollapse">
                                    <input type="text" id="patientHistorySearch" class="form-control form-control-sm mb-2" placeholder="Tìm kiếm ngày, mã bệnh, tên bệnh...">
                                    <div id="patientHistoryList" style="max-height: 250px; overflow-y: auto; overflow-x: hidden;">
                                        @foreach($patientHistory->skip(5) as $hist)
                                            <div class="mb-1 patient-history-item" data-search="{{ $hist->consultation_date ? \Carbon\Carbon::parse($hist->consultation_date)->format('d/m/Y') : '' }} {{ $hist->diagnosis_code ?? '' }} {{ $hist->diagnosis ? $hist->diagnosis->diagnosis_name : '' }}">
                                                <a href="{{ route('consultations.show', $hist->id) }}?reuse_mode=1&from_consultation={{ $consultation->id }}"
                                                    class="btn btn-sm btn-outline-secondary w-100 text-start overflow-hidden d-flex flex-column" title="Xem chi tiết & Dùng lại toa">
                                                    <div class="d-flex justify-content-between w-100">
                                                        <span><i class="fas fa-calendar-alt me-1"></i> {{ $hist->consultation_date ? \Carbon\Carbon::parse($hist->consultation_date)->format('d/m/Y') : '' }}</span>
                                                        <span class="badge bg-secondary">{{ $hist->diagnosis_code ?? 'KXĐ' }}</span>
                                                    </div>
                                                    @if($hist->diagnosis)
                                                        <div class="small text-truncate mt-1 text-muted" style="font-size: 0.75rem;">{{ $hist->diagnosis->diagnosis_name }}</div>
                                                    @endif
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted small py-2">Chưa có lịch sử khám</div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ====== CỘT PHẢI: KẾT LUẬN + KÊ ĐƠN ====== --}}
            <div class="col-xl-9 col-lg-8">

                {{-- PHẦN 1: KẾT LUẬN CHUYÊN MÔN --}}
                <div class="card shadow-sm border-0 border-top border-warning border-3 mb-4">
                    <div class="card-header bg-white fw-bold py-3">
                        <i class="fas fa-user-md text-warning me-2"></i>Kết luận chuyên môn
                    </div>
                    <div class="card-body">
                        @if($consultation->status === \App\Models\Consultation::STATUS_COMPLETED)
                            {{-- CHẾ ĐỘ CHỈ ĐỌC --}}
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" width="180">Ngày tái khám:</td>
                                    <td>{{ $consultation->follow_up_date ? \Carbon\Carbon::parse($consultation->follow_up_date)->format('d/m/Y') : '---' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Trạng thái:</td>
                                    <td><span class="badge bg-success">Hoàn thành</span></td>
                                </tr>
                            </table>

                            {{-- Chẩn đoán nổi bật --}}
                            <div class="mt-3 p-3 rounded-3 border-start border-4 border-warning" style="background:#fffbeb;">
                                <div class="fw-bold mb-1 text-warning-emphasis" style="font-size:.8rem; text-transform:uppercase; letter-spacing:.05em;">
                                    <i class="fas fa-notes-medical me-1"></i>Chẩn đoán chi tiết
                                </div>
                                <div class="fw-semibold text-dark" style="font-size:.95rem; line-height:1.5;">
                                    {{ $consultation->diagnosis_notes ?: '---' }}
                                </div>
                            </div>

                            @if($consultation->diagnosis_code)
                                <div class="mt-2">
                                    <span class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill border border-primary bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size:.85rem;">
                                        <i class="fas fa-tag"></i>
                                        ICD: {{ $consultation->diagnosis_code }}
                                        @if($consultation->diagnosis)
                                            — {{ $consultation->diagnosis->diagnosis_name }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            @if($consultation->prescriptions->count() > 0)
                                @php $bs = $consultation->prescriptions->first()?->prescriber; @endphp
                                @if($bs)
                                    <div class="mt-2 text-muted small">
                                        <i class="fas fa-user-md me-1 text-primary"></i>Bác sĩ kê đơn: <strong>{{ $bs->full_name }}</strong>
                                    </div>
                                @endif
                            @endif
                        @else
                            <form method="POST" action="{{ route('doctor.consultation.save_conclusion', $consultation->id) }}"
                                id="examineForm">
                                @csrf
                                @method('PUT')

                                {{-- Hidden fields giữ lại dữ liệu readonly --}}
                                <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">
                                <input type="hidden" name="consultation_date" value="{{ $consultation->consultation_date }}">
                                <input type="hidden" name="chief_complaint" value="{{ $consultation->chief_complaint }}">
                                <input type="hidden" name="symptoms" value="{{ $consultation->symptoms }}">
                                <input type="hidden" name="temperature" value="{{ $consultation->temperature }}">
                                <input type="hidden" name="blood_pressure" value="{{ $consultation->blood_pressure }}">

                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label fw-bold">Chẩn đoán chi tiết <span
                                                class="text-danger">*</span></label>
                                        <textarea name="diagnosis_notes" rows="3" class="form-control border-warning shadow-sm"
                                            placeholder="Nhập kết luận chẩn đoán...">{{ old('diagnosis_notes', $consultation->diagnosis_notes ?? '') }}</textarea>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Mã bệnh chuẩn (ICD)</label>
                                        <select name="diagnosis_code" id="diagnosis_code"
                                            class="form-select border-warning shadow-sm"
                                            data-placeholder="Nhập mã hoặc tên bệnh...">
                                            @if(old('diagnosis_code', $consultation->diagnosis_code ?? ''))
                                                <option value="{{ old('diagnosis_code', $consultation->diagnosis_code) }}" selected>
                                                    {{ old('diagnosis_code', $consultation->diagnosis_code) }}
                                                    {{ isset($consultation->diagnosis) ? ' - ' . $consultation->diagnosis->diagnosis_name : '' }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Ngày tái khám</label>
                                        <input type="date" name="follow_up_date" class="form-control border-warning shadow-sm"
                                            value="{{ old('follow_up_date', isset($consultation->follow_up_date) ? date('Y-m-d', strtotime($consultation->follow_up_date)) : '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Trạng thái phiên khám</label>
                                        <select name="status" class="form-select border-warning shadow-sm">
                                            <option value="0" {{ old('status', $consultation->status) == 0 ? 'selected' : '' }}>
                                                Mới tạo</option>
                                            <option value="1" {{ old('status', $consultation->status) == 1 ? 'selected' : '' }}>
                                                Chờ khám</option>
                                            <option value="2" {{ old('status', $consultation->status) == 2 ? 'selected' : '' }}>
                                                Đang khám</option>
                                            <option value="3" {{ old('status', $consultation->status) == 3 ? 'selected' : '' }}>
                                                Chờ thanh toán</option>
                                            <option value="4" {{ old('status', $consultation->status) == 4 ? 'selected' : '' }}>
                                                Hoàn thành</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-warning shadow-sm px-4 fw-bold text-dark">
                                        <i class="fas fa-save me-1"></i> Lưu Kết Luận
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- PHẦN 2: KÊ ĐƠN THUỐC --}}
                @if($consultation->status !== \App\Models\Consultation::STATUS_COMPLETED)
                    <div class="card shadow-sm border-0 border-top border-success border-3">
                        <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-pills text-success me-2"></i>Kê đơn thuốc</span>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm me-2" id="btn-use-template" data-bs-toggle="modal" data-bs-target="#templateModal">
                                    <i class="fas fa-file-prescription me-1"></i> Sử dụng toa mẫu
                                </button>
                                <button type="button" class="btn btn-sm btn-success shadow-sm" id="btn-add-medicine">
                                    <i class="fas fa-plus me-1"></i> Thêm thuốc
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm">
                                @csrf
                                <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered mb-3 align-middle" style="min-width: 700px;">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:15%">Nhóm Thuốc</th>
                                                <th style="width:20%">Tên Thuốc *</th>
                                                <th style="width:8%">Đơn vị</th>
                                                <th style="width:9%">SL/lần *</th>
                                                <th style="width:9%">Lần/ngày *</th>
                                                <th style="width:9%">Số ngày *</th>
                                                <th style="width:9%">Tổng SL *</th>
                                                <th style="width:15%">Cách dùng</th>
                                                <th style="width:6%" class="text-center">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="prescription-body">
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm category-select">
                                                        <option value="">Tất cả</option>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="medicines[]" class="form-select form-select-sm medicine-select" required>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="units[]" class="form-control form-control-sm unit-input" readonly placeholder="---"></td>
                                                <td><input type="number" class="form-control form-control-sm qty-per-time" min="0.1" step="0.1" placeholder="Vd: 1" required></td>
                                                <td><input type="number" class="form-control form-control-sm times-per-day" min="1" step="1" placeholder="Vd: 2" required></td>
                                                <td><input type="number" name="duration_days[]" class="form-control form-control-sm duration-days" min="1" step="1" placeholder="Vd: 5" required></td>
                                                <td>
                                                    <input type="number" name="quantities[]" class="form-control form-control-sm total-quantity" readonly required>
                                                    <input type="hidden" name="dosages[]" class="dosage-input">
                                                </td>
                                                <td><input type="text" name="usage_instructions[]" class="form-control form-control-sm" placeholder="Sau ăn"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="fas fa-times"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lời dặn của Bác sĩ</label>
                                    <textarea name="doctor_advice" class="form-control" rows="2"
                                        placeholder="Ghi chú chế độ ăn uống, kiêng cữ..."></textarea>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-success shadow-sm px-4 fw-bold">
                                        <i class="fas fa-file-prescription me-1"></i> Lưu Toa Thuốc
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .history-item:hover {
            background-color: #f8f9fa;
        }

        .transition {
            transition: all 0.2s ease-in-out;
        }

        .hover-underline:hover {
            text-decoration: underline !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            // Select2 Mã bệnh
            $('#diagnosis_code').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Nhập mã hoặc tên bệnh...',
                allowClear: true,
                ajax: {
                    url: "{{ route('diagnoses.ajax.search') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) { return { keyword: params.term }; },
                    processResults: function (data) {
                        return {
                            results: data.map(item => ({
                                id: item.diagnosis_code,
                                text: item.diagnosis_code + ' - ' + item.diagnosis_name,
                                code: item.diagnosis_code,
                                name: item.diagnosis_name
                            }))
                        };
                    }
                },
                templateResult: function (item) {
                    if (item.loading) return item.text;
                    let code = item.code || item.id;
                    let name = item.name;
                    if (!name && item.text && item.text.includes(' - ')) {
                        name = item.text.split(' - ').slice(1).join(' - ');
                    }
                    if (!code) return item.text;
                    return $(`<div class="d-flex align-items-center border-bottom pb-2 mb-1">
                        <span class="badge bg-success me-3" style="min-width:60px;font-size:0.9em;">${code}</span>
                        <span class="text-dark fw-medium">${name || item.text}</span>
                    </div>`);
                }
            });

            // ================= CẤU HÌNH AJAX SELECT2 CHO THUỐC =================
            function initMedicineSelect(selector) {
                $(selector).each(function() {
                    var $el = $(this);
                    $el.select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: '-- Chọn thuốc --',
                        allowClear: true,
                        ajax: {
                            url: "{{ route('medicines.ajax.search') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) { 
                                var categoryId = $el.closest('tr').find('.category-select').val();
                                return { 
                                    keyword: params.term,
                                    category_id: categoryId 
                                }; 
                            },
                            processResults: function (data) {
                                return {
                                    results: data.map(item => ({
                                        id: item.id,
                                        text: item.medicine_name,
                                        unit: item.unit
                                    }))
                                };
                            }
                        }
                    }).on('select2:select', function (e) {
                        var data = e.params.data;
                        var row = $(this).closest('tr');
                        var unitInput = row.find('.unit-input');
                        unitInput.val(data.unit || '');
                        unitInput[0].dispatchEvent(new Event('input', { bubbles: true }));
                    });
                });
            }

            // Khởi tạo dòng đầu tiên
            initMedicineSelect('.medicine-select');

            const tableBody = document.getElementById('prescription-body');
            const btnAdd = document.getElementById('btn-add-medicine');

            // Xóa thuốc đã chọn nếu đổi nhóm thuốc
            tableBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('category-select')) {
                    var $row = $(e.target).closest('tr');
                    $row.find('.medicine-select').val(null).trigger('change');
                    $row.find('.unit-input').val('');
                }
            });

            // Tự động tính toán tổng số lượng thuốc
            tableBody.addEventListener('input', function (e) {
                if (e.target.classList.contains('qty-per-time') || 
                    e.target.classList.contains('times-per-day') || 
                    e.target.classList.contains('duration-days') ||
                    e.target.classList.contains('unit-input')) {
                    
                    const row = e.target.closest('tr');
                    const qtyPerTime = parseFloat(row.querySelector('.qty-per-time').value) || 0;
                    const timesPerDay = parseFloat(row.querySelector('.times-per-day').value) || 0;
                    const durationDays = parseFloat(row.querySelector('.duration-days').value) || 0;
                    const unit = row.querySelector('.unit-input').value || 'viên';

                    if (qtyPerTime > 0 && timesPerDay > 0 && durationDays > 0) {
                        const total = Math.ceil(qtyPerTime * timesPerDay * durationDays);
                        row.querySelector('.total-quantity').value = total;
                        row.querySelector('.dosage-input').value = `${qtyPerTime} ${unit}/lần, ${timesPerDay} lần/ngày`;
                    } else {
                        row.querySelector('.total-quantity').value = '';
                    }
                }
            });

            btnAdd.addEventListener('click', function () {
                const rows = tableBody.querySelectorAll('tr');
                const firstRow = rows[0];
                const lastRow = rows[rows.length - 1]; // Lấy dòng cuối cùng để copy dữ liệu
                
                // Lấy dữ liệu từ dòng cuối cùng
                const lastTimesPerDay = lastRow.querySelector('.times-per-day').value;
                const lastDurationDays = lastRow.querySelector('.duration-days').value;
                
                // Phá hủy Select2 trước khi clone dòng đầu để tránh lỗi UI
                $(firstRow).find('.medicine-select').select2('destroy');
                
                const newRow = firstRow.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                newRow.querySelector('.category-select').selectedIndex = 0; // Reset nhóm thuốc về Tất cả
                newRow.querySelector('.medicine-select').innerHTML = ''; // Xóa option thuốc đã chọn
                newRow.querySelector('.remove-row').disabled = false;
                
                // Kế thừa Lần/ngày và Số ngày từ dòng trước đó
                newRow.querySelector('.times-per-day').value = lastTimesPerDay;
                newRow.querySelector('.duration-days').value = lastDurationDays;
                
                tableBody.appendChild(newRow);
                
                // Khởi tạo lại cả dòng cũ và mới
                initMedicineSelect($(firstRow).find('.medicine-select'));
                initMedicineSelect($(newRow).find('.medicine-select'));
                
                updateRemoveButtons();
            });

            tableBody.addEventListener('click', function (e) {
                const btn = e.target.closest('.remove-row');
                if (btn) {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        btn.closest('tr').remove();
                        updateRemoveButtons();
                    }
                }
            });

            function updateRemoveButtons() {
                const rows = tableBody.querySelectorAll('tr');
                tableBody.querySelectorAll('.remove-row').forEach((btn, i) => {
                    btn.disabled = (rows.length === 1);
                });
            }

            // 🔥 Xử lý tin nhắn "Sử dụng lại toa thuốc" từ cửa sổ con (Popup)
            window.addEventListener('message', function (event) {
                if (event.data.type === 'REUSE_PRESCRIPTION') {
                    applyReuseData(event.data);
                }
            });

            // 🔥 Kiểm tra localStorage khi load trang (Trường hợp điều hướng cùng tab)
            const savedData = localStorage.getItem('reuse_prescription_data');
            if (savedData) {
                const data = JSON.parse(savedData);
                // Chỉ áp dụng nếu dữ liệu mới (trong vòng 5 phút)
                if (Date.now() - data.timestamp < 300000) {
                    applyReuseData(data);
                }
                localStorage.removeItem('reuse_prescription_data');
            }

            function applyReuseData(data) {
                const medicinesData = data.medicines;
                const advice = data.advice;

                // Điền lời dặn
                const adviceTextarea = document.querySelector('textarea[name="doctor_advice"]');
                if (adviceTextarea) adviceTextarea.value = advice;

                // Xóa sạch các dòng hiện tại
                tableBody.innerHTML = '';

                medicinesData.forEach((med, index) => {
                    const newRow = document.createElement('tr');
                    newRow.innerHTML = `
                        <td>
                            <select class="form-select form-select-sm category-select">
                                <option value="">Tất cả</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="medicines[]" class="form-select form-select-sm medicine-select" required>
                                <option value="${med.id}" selected>${med.name}</option>
                            </select>
                        </td>
                        <td><input type="text" name="units[]" class="form-control form-control-sm unit-input" readonly value="${med.unit || ''}"></td>
                        <td><input type="number" class="form-control form-control-sm qty-per-time" min="0.1" step="0.1" placeholder="Vd: 1" required></td>
                        <td><input type="number" class="form-control form-control-sm times-per-day" min="1" step="1" placeholder="Vd: 2" required></td>
                        <td><input type="number" name="duration_days[]" class="form-control form-control-sm duration-days" min="1" step="1" value="${med.duration_days || ''}" required></td>
                        <td>
                            <input type="number" name="quantities[]" class="form-control form-control-sm total-quantity" value="${med.quantity}" readonly required>
                            <input type="hidden" name="dosages[]" class="dosage-input" value="${med.dosage}">
                        </td>
                        <td><input type="text" name="usage_instructions[]" class="form-control form-control-sm" value="${med.usage_instructions || ''}"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-times"></i></button></td>
                    `;
                    
                    // Thử bóc tách "lần/ngày" và "viên/lần" từ dosage nếu có
                    if (med.dosage) {
                        let timesMatch = med.dosage.match(/Ngày\s+(\d+)\s+lần/i);
                        let doseMatch = med.dosage.match(/mỗi lần\s+([\d.]+)/i);
                        if(timesMatch) {
                            let el = newRow.querySelector('.times-per-day');
                            if (el) el.value = timesMatch[1];
                        }
                        if(doseMatch) {
                            let el = newRow.querySelector('.qty-per-time');
                            if (el) el.value = doseMatch[1];
                        }
                    }
                    
                    tableBody.appendChild(newRow);
                });
                
                initMedicineSelect('.medicine-select');
                updateRemoveButtons();

                // Cuộn xuống phần kê đơn
                document.getElementById('prescriptionForm').scrollIntoView({ behavior: 'smooth' });
            }

            // --- TEMPLATE LOGIC ---
            let templateLoaded = false;
            let currentTemplates = [];
            $('#templateModal').on('show.bs.modal', function () {
                if (!templateLoaded) {
                    $.ajax({
                        url: '{{ route("doctor.templates.ajax") }}',
                        method: 'GET',
                        success: function(res) {
                            currentTemplates = res;
                            let html = '';
                            if (res.length === 0) {
                                html = `<tr><td colspan="4" class="text-center py-4 text-muted">Bạn chưa có toa mẫu nào. <a href="{{ route('doctor.templates.create') }}">Tạo ngay</a></td></tr>`;
                            } else {
                                res.forEach((t, index) => {
                                    html += `
                                    <tr class="template-row">
                                        <td class="ps-3 fw-bold text-primary">${t.template_name}</td>
                                        <td>${t.diagnosis_code ? `<span class="badge bg-success me-1">${t.diagnosis_code}</span>` : ''} ${t.disease_name || '--'}</td>
                                        <td><span class="badge bg-secondary">${t.details.length} thuốc</span></td>
                                        <td class="text-center pe-3">
                                            <button type="button" class="btn btn-sm btn-success btn-apply-template shadow-sm" data-index='${index}'>
                                                <i class="fas fa-check"></i> Chọn
                                            </button>
                                        </td>
                                    </tr>`;
                                });
                            }
                            $('#templateListBody').html(html);
                            templateLoaded = true;
                            
                            // Initialize search
                            $('#templateSearchInput').on('input', function() {
                                let keyword = $(this).val().toLowerCase().trim();
                                $('#templateListBody tr.template-row').each(function() {
                                    let text = $(this).text().toLowerCase();
                                    $(this).toggle(text.includes(keyword));
                                });
                            });
                        },
                        error: function() {
                            $('#templateListBody').html(`<tr><td colspan="4" class="text-center py-4 text-danger">Lỗi tải dữ liệu toa mẫu.</td></tr>`);
                        }
                    });
                }
            });

            $(document).on('click', '.btn-apply-template', function() {
                let index = $(this).data('index');
                let details = currentTemplates[index].details;
                
                if (confirm('Áp dụng toa mẫu sẽ ghi đè lên các thuốc hiện tại. Bạn có chắc chắn?')) {
                    
                    // Xóa hoàn toàn modal và backdrop để tránh lỗi treo màn hình
                    $('#templateModal').modal('hide');
                    setTimeout(() => {
                        $('.modal-backdrop').remove();
                        $('body').removeClass('modal-open').css('overflow', '').css('padding-right', '');
                    }, 100);
                    
                    const mappedData = {
                        advice: '',
                        medicines: details.map(d => ({
                            id: d.medicine.id,
                            name: d.medicine.medicine_name + ' (' + (d.medicine.trade_name||'') + ')',
                            unit: d.unit,
                            quantity: d.quantity,
                            duration_days: d.duration_days,
                            dosage: d.dosage,
                            usage_instructions: d.usage_instructions
                        }))
                    };
                    applyReuseData(mappedData);
                    
                    let htmlAlert = `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> Đã áp dụng toa thuốc mẫu!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                    $('#prescriptionForm').prepend(htmlAlert);
                }
            });
        });
    </script>
@endpush

<!-- Modal Toa Mẫu -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title"><i class="fas fa-file-prescription me-2"></i>Chọn Toa Thuốc Mẫu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="templateSearchInput" class="form-control border-start-0" placeholder="Tìm kiếm tên toa mẫu, mã bệnh, tên bệnh...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" id="templateTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Tên Toa Mẫu</th>
                                <th>Tên Bệnh</th>
                                <th>Số thuốc</th>
                                <th class="text-center pe-3">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="templateListBody">
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Đang tải dữ liệu...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
// ===== Doctor History Search =====
document.addEventListener('DOMContentLoaded', function () {
    const doctorSearchInput = document.getElementById('doctorHistorySearch');
    if (doctorSearchInput) {
        doctorSearchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.doctor-hist-item');
            const emptyMsg = document.getElementById('doctorHistoryEmpty');
            let visibleCount = 0;
            items.forEach(function (item) {
                const searchData = (item.getAttribute('data-search') || '').toLowerCase();
                const match = !keyword || searchData.includes(keyword);
                item.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            if (emptyMsg) emptyMsg.classList.toggle('d-none', visibleCount > 0);
        });
    }
    const doctorPanel = document.getElementById('doctorHistoryPanel');
    const chevron = document.getElementById('doctorHistoryChevron');
    if (doctorPanel && chevron) {
        doctorPanel.addEventListener('show.bs.collapse', () => chevron.style.transform = 'rotate(180deg)');
        doctorPanel.addEventListener('hide.bs.collapse', () => chevron.style.transform = 'rotate(0deg)');
    }
    // ===== Patient History Search =====
    const patientSearchInput = document.getElementById('patientHistorySearch');
    if (patientSearchInput) {
        patientSearchInput.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.patient-history-item');
            
            items.forEach(function (item) {
                const searchData = (item.getAttribute('data-search') || '').toLowerCase();
                const match = !keyword || searchData.includes(keyword);
                item.style.setProperty('display', match ? 'block' : 'none', 'important');
            });
        });
    }
});

function openSaveTemplateModal(prescriptionId) {
    let form = document.getElementById('saveTemplateForm');
    form.action = '{{ route("doctor.templates.storeFromPrescription", "") }}/' + prescriptionId;
    let modal = new bootstrap.Modal(document.getElementById('saveTemplateModal'));
    modal.show();
}
</script>
@endpush

<!-- Modal Lưu Toa Mẫu -->
<div class="modal fade" id="saveTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title"><i class="fas fa-save me-2"></i>Lưu Thành Toa Thuốc Mẫu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="saveTemplateForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Toa Mẫu <span class="text-danger">*</span></label>
                        <input type="text" name="template_name" class="form-control" placeholder="VD: Toa Viêm Họng Người Lớn" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mã Bệnh (ICD-10)</label>
                        <input type="text" name="diagnosis_code" class="form-control" value="{{ $consultation->diagnosis_code }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên Bệnh</label>
                        <input type="text" name="disease_name" class="form-control" value="{{ $consultation->diagnosis ? $consultation->diagnosis->diagnosis_name : '' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ghi chú thêm</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Tùy chọn..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu Toa Mẫu</button>
                </div>
            </form>
        </div>
    </div>
</div>
