@extends('layouts.app')

@push('styles')
<style>
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

@section('content')
<div class="container-fluid px-4 mt-4">
    <form action="{{ route('doctor.templates.store') }}" method="POST" id="examineForm">
        @csrf
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-notes-medical me-2"></i>Tạo Toa Mẫu Mới</h3>
            <div>
                <a href="{{ route('doctor.templates.index') }}" class="btn btn-outline-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu Toa Mẫu</button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 80px; z-index: 1;">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fas fa-info-circle me-2 text-primary"></i>Thông Tin Chung</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Toa Mẫu <span class="text-danger">*</span></label>
                            <input type="text" name="template_name" class="form-control" required placeholder="VD: Toa Cảm Cúm Trẻ Em" value="{{ old('template_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Bệnh (Mặc định)</label>
                            <input type="text" name="disease_name" class="form-control" placeholder="VD: Viêm họng cấp" value="{{ old('disease_name') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã bệnh chuẩn (ICD)</label>
                            <select name="diagnosis_code" id="diagnosis_code" class="form-select" data-placeholder="Nhập mã hoặc tên bệnh...">
                                @if(old('diagnosis_code'))
                                    <option value="{{ old('diagnosis_code') }}" selected>{{ old('diagnosis_code') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Ghi chú thêm về toa thuốc này...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4" id="prescriptionSection">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fas fa-pills me-2 text-primary"></i>Danh Sách Thuốc</h5>
                        <button type="button" class="btn btn-sm btn-success shadow-sm" id="btn-add-medicine">
                            <i class="fas fa-plus me-1"></i> Thêm thuốc
                        </button>
                    </div>
                    <div class="card-body bg-light p-3">
                        <div id="prescription-cards-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.MEDICINE_AJAX_URL  = "{{ route('medicines.ajax.search') }}";
    window.CATEGORY_AJAX_URL  = "{{ route('medicine_categories.ajax.search') }}";
</script>
<script src="{{ asset('js/prescription-form.js') }}"></script>
<script>
$(document).ready(function() {
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

    // Add first empty card
    addMedicineCard();
});
</script>
@endpush
