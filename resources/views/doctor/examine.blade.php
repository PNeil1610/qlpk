@extends('layouts.app')

@section('content')
    <div class="container-fluid px-4 mt-4">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                @if($consultation->status === \App\Models\Consultation::STATUS_COMPLETED)
                    <h3 class="mb-0 text-secondary"><i class="fas fa-eye"></i> phiếu khám (Chỉ đọc)</h3>
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
            <!-- Mobile Toggle Button for Patient Details & History -->
            <div class="col-12 d-lg-none mb-2">
                <button class="btn btn-outline-primary w-100 shadow-sm d-flex justify-content-between align-items-center py-2.5 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#leftColumnCollapse" aria-expanded="false">
                    <span class="fw-bold"><i class="fas fa-user-injured me-2"></i>Thông tin bệnh nhân & Lịch sử khám</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>

            {{-- ====== CỘT TRÁI: THÔNG TIN HÀNH CHÍNH (CHỈ ĐỌC) ====== --}}
            <div class="col-xl-3 col-lg-4 collapse d-lg-block" id="leftColumnCollapse">
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
            <div class="col-xl-9 col-lg-8 col-12">

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
                    <div class="card shadow-sm border-0 border-top border-success border-3" id="prescriptionSection">
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

                                <div id="prescription-cards-container"></div>

                                <div class="mb-3 mt-3">
                                    <label class="form-label fw-bold"><i class="fas fa-comment-medical me-1 text-muted"></i>Lời dặn của Bác sĩ</label>
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
        .history-item:hover { background-color: #f8f9fa; }
        .transition { transition: all 0.2s ease-in-out; }
        .hover-underline:hover { text-decoration: underline !important; }

        /* === COMPACT RX CARD === */
        .rx-card { border-color: #dee2e6 !important; }
        .rx-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .rx-row { min-height: 0; }
        .rx-label { font-size: .65rem; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: .03em; margin-bottom: 2px; white-space: nowrap; }
        .dose-input { font-weight: 600; font-size: .82rem; height: 28px !important; }
        .dose-input:focus { border-color: #198754; box-shadow: 0 0 0 .12rem rgba(25,135,84,.2); }
        .rx-card .form-control-sm, .rx-card .form-select-sm { font-size: .82rem; height: 28px !important; padding-top: .2rem; padding-bottom: .2rem; }
        .rx-card .btn-sm { font-size: .75rem; padding: .2rem .45rem; }
        .btn-timing { font-size: .72rem !important; padding: .18rem .35rem !important; }
        .remove-card:disabled { opacity: .3; cursor: not-allowed; }
        .rx-unit-lbl { line-height: 1; margin-top: 1px; }
        #prescription-cards-container .select2-container { font-size: .82rem; }
    </style>
@endpush

@push('scripts')
<script>
    window.MEDICINE_AJAX_URL  = "{{ route('medicines.ajax.search') }}";
    window.CATEGORY_AJAX_URL  = "{{ route('medicine_categories.ajax.search') }}";
    window.currentTemplates   = [];
    window.CATEGORIES_HTML    = '';
</script>
<script src="{{ asset('js/prescription-form.js') }}"></script>
<script>
    $(document).ready(function () {
        // ===== Select2 Mã bệnh ICD =====
        $('#diagnosis_code').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Nhập mã hoặc tên bệnh...',
            allowClear: true,
            ajax: {
                url: "{{ route('diagnoses.ajax.search') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) { return { keyword: params.term }; },
                processResults: function(data) {
                    return { results: data.map(function(item) { return { id: item.diagnosis_code, text: item.diagnosis_code + ' - ' + item.diagnosis_name, code: item.diagnosis_code, name: item.diagnosis_name }; }) };
                }
            },
            templateResult: function(item) {
                if (item.loading) return item.text;
                var code = item.code || item.id;
                var name = item.name || (item.text.indexOf(' - ') > -1 ? item.text.split(' - ').slice(1).join(' - ') : item.text);
                return $('<div class="d-flex align-items-center border-bottom pb-1 mb-1"><span class="badge bg-success me-2" style="min-width:55px;">' + code + '</span><span>' + name + '</span></div>');
            }
        });

        // ===== Template Modal =====
        var templateLoaded = false;
        $('#templateModal').on('show.bs.modal', function () {
            if (templateLoaded) return;
            $.ajax({
                url: '{{ route("doctor.templates.ajax") }}',
                method: 'GET',
                success: function(res) {
                    window.currentTemplates = res;
                    var html = res.length === 0
                        ? '<tr><td colspan="4" class="text-center py-4 text-muted">Bạn chưa có toa mẫu nào.</td></tr>'
                        : res.map(function(t, i) {
                            return '<tr class="template-row">'
                                + '<td class="ps-3 fw-bold text-primary">' + t.template_name + '</td>'
                                + '<td>' + (t.diagnosis_code ? '<span class="badge bg-success me-1">' + t.diagnosis_code + '</span>' : '') + ' ' + (t.disease_name || '--') + '</td>'
                                + '<td><span class="badge bg-secondary">' + t.details.length + ' thuốc</span></td>'
                                + '<td class="text-center pe-3"><button type="button" class="btn btn-sm btn-success btn-apply-template" data-index="' + i + '">Chọn</button></td>'
                            + '</tr>';
                        }).join('');
                    $('#templateListBody').html(html);
                    templateLoaded = true;
                    $('#templateSearchInput').on('input', function() {
                        var kw = $(this).val().toLowerCase();
                        $('#templateListBody tr.template-row').each(function() { $(this).toggle($(this).text().toLowerCase().indexOf(kw) > -1); });
                    });
                },
                error: function() { $('#templateListBody').html('<tr><td colspan="4" class="text-center py-4 text-danger">Lỗi tải dữ liệu.</td></tr>'); }
            });
        });

        // ===== Áp dụng toa mẫu =====
        $(document).on('click', '.btn-apply-template', function() {
            var index = $(this).data('index');
            var details = (window.currentTemplates[index] || {}).details || [];
            if (!confirm('Áp dụng toa mẫu sẽ ghi đè lên các thuốc hiện tại. Bạn có chắc chắn?')) return;

            $('#templateModal').modal('hide');
            setTimeout(function() { $('.modal-backdrop').remove(); $('body').removeClass('modal-open').css({ overflow: '', 'padding-right': '' }); }, 150);

            window.applyReuseData({
                advice: '',
                medicines: details.map(function(d) {
                    return {
                        id: d.medicine.id,
                        name: d.medicine.medicine_name + (d.medicine.trade_name ? ' (' + d.medicine.trade_name + ')' : ''),
                        unit: d.unit,
                        quantity: d.quantity,
                        duration_days: d.duration_days,
                        dosage: d.dosage,
                        usage_instructions: d.usage_instructions
                    };
                })
            });

            $('#prescriptionForm').prepend('<div class="alert alert-success alert-dismissible fade show">Đã áp dụng toa thuốc mẫu!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
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
