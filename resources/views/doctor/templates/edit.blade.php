@extends('layouts.app')

@push('styles')
<style>
    .med-row { background: #fff; transition: all 0.2s; border: 1px solid #dee2e6; border-radius: 0.5rem; }
    .med-row:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .form-group-custom label { font-size: 0.85rem; font-weight: 600; color: #495057; margin-bottom: 0.25rem; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 mt-4">
    <form action="{{ route('doctor.templates.update', $template->id) }}" method="POST" id="templateForm">
        @csrf
        @method('PUT')
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-primary"><i class="fas fa-edit"></i> Toa Mẫu</h3>
            <div>
                <a href="{{ route('doctor.templates.index') }}" class="btn btn-outline-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu Thay Đổi</button>
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
                            <input type="text" name="template_name" class="form-control" required value="{{ old('template_name', $template->template_name) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên Bệnh (Mặc định)</label>
                            <input type="text" name="disease_name" class="form-control" value="{{ old('disease_name', $template->disease_name) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã bệnh chuẩn (ICD)</label>
                            <select name="diagnosis_code" id="diagnosis_code" class="form-select" data-placeholder="Nhập mã hoặc tên bệnh...">
                                @if(old('diagnosis_code', $template->diagnosis_code))
                                    <option value="{{ old('diagnosis_code', $template->diagnosis_code) }}" selected>
                                        {{ old('diagnosis_code', $template->diagnosis_code) }}
                                        {{ $template->diagnosis ? ' - ' . $template->diagnosis->diagnosis_name : '' }}
                                    </option>
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô Tả / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $template->description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold text-dark"><i class="fas fa-pills me-2 text-primary"></i>Danh Sách Thuốc</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddMedicine">
                            <i class="fas fa-plus me-1"></i>Thêm thuốc
                        </button>
                    </div>
                    <div class="card-body bg-light p-3">
                        <div id="medicinesContainer">
                            <!-- Template Row -->
                            <div class="row g-2 mb-3 p-3 med-row position-relative" id="templateRow" style="display: none;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute btnRemoveRow" style="top: -10px; right: -10px; width: 28px; height: 28px; border-radius: 50%; padding: 0; z-index: 2;" title="Xóa">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                <div class="col-md-5 form-group-custom">
                                    <label>Chọn Nhóm Thuốc</label>
                                    <select class="form-select category-select" style="width: 100%;">
                                        <option value="">-- Tất cả nhóm thuốc --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7 form-group-custom">
                                    <label>Chọn Thuốc <span class="text-danger">*</span></label>
                                    <select class="form-select medicine-select" name="medicines[]" style="width: 100%;" disabled required>
                                        <option value="">-- Chọn thuốc --</option>
                                    </select>
                                </div>

                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Lần/ngày</label>
                                    <input type="number" class="form-control calc-times" min="1" placeholder="Số lần">
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Số ngày <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_days[]" class="form-control calc-days" min="1" required placeholder="Số ngày" disabled>
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Viên/lần</label>
                                    <input type="number" class="form-control calc-dose" min="0.1" step="0.1" placeholder="Liều">
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Tổng SL <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="quantities[]" class="form-control calc-total fw-bold text-primary" required min="0.1" step="0.1" readonly disabled>
                                        <input type="hidden" name="units[]" class="unit-input" disabled>
                                        <span class="input-group-text unit-display" style="font-size: 0.8rem; background: #e9ecef;">Viên</span>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group-custom mt-2">
                                    <label>Ghi chú (Cách dùng)</label>
                                    <input type="text" name="usage_instructions[]" class="form-control usage-input" placeholder="VD: Uống sau ăn" disabled>
                                </div>
                                <input type="hidden" name="dosages[]" class="dosage-input" disabled>
                            </div>
                            
                            <!-- Existing rows -->
                            @foreach($template->details as $detail)
                            <div class="row g-2 mb-3 p-3 med-row position-relative existing-row" style="display: flex;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute btnRemoveRow" style="top: -10px; right: -10px; width: 28px; height: 28px; border-radius: 50%; padding: 0; z-index: 2;" title="Xóa">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                <div class="col-md-5 form-group-custom">
                                    <label>Chọn Nhóm Thuốc</label>
                                    <select class="form-select category-select" style="width: 100%;">
                                        <option value="">-- Tất cả nhóm thuốc --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $detail->medicine->category_id == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-7 form-group-custom">
                                    <label>Chọn Thuốc <span class="text-danger">*</span></label>
                                    <select class="form-select medicine-select" name="medicines[]" style="width: 100%;" required>
                                        <option value="{{ $detail->medicine_id }}" selected>{{ $detail->medicine->medicine_name }} ({{ $detail->medicine->trade_name }}) - {{ $detail->medicine->unit }}</option>
                                    </select>
                                </div>

                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Lần/ngày</label>
                                    <input type="number" class="form-control calc-times" min="1" placeholder="Số lần">
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Số ngày <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_days[]" class="form-control calc-days" min="1" required value="{{ $detail->duration_days }}">
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Viên/lần</label>
                                    <input type="number" class="form-control calc-dose" min="0.1" step="0.1" placeholder="Liều">
                                </div>
                                <div class="col-md-2 form-group-custom mt-2">
                                    <label>Tổng SL <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="quantities[]" class="form-control calc-total fw-bold text-primary" required min="0.1" step="0.1" readonly value="{{ $detail->quantity }}">
                                        <input type="hidden" name="units[]" class="unit-input" value="{{ $detail->unit }}">
                                        <span class="input-group-text unit-display" style="font-size: 0.8rem; background: #e9ecef;">{{ $detail->unit }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group-custom mt-2">
                                    <label>Ghi chú (Cách dùng)</label>
                                    <input type="text" name="usage_instructions[]" class="form-control usage-input" value="{{ $detail->usage_instructions }}">
                                </div>
                                <input type="hidden" name="dosages[]" class="dosage-input" value="{{ $detail->dosage }}">
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-light border-dashed text-primary w-100 py-3" id="btnAddMedicineLarge" style="border-style: dashed !important; border-width: 2px !important;">
                                <i class="fas fa-plus-circle me-2"></i>Thêm Thuốc Mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $container = $('#medicinesContainer');
    const $template = $('#templateRow');
    
    let lastDays = '';
    let lastTimes = '';

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

    function initRow($row) {
        let $catSelect = $row.find('.category-select');
        let $medSelect = $row.find('.medicine-select');

        $catSelect.select2({
            theme: 'bootstrap-5',
            dropdownParent: $row
        }).on('change', function() {
            // Only clear medicine if it wasn't pre-populated (like on load for existing rows)
            if (event && event.originalEvent) {
                $medSelect.val(null).trigger('change');
            }
            if($(this).val()) {
                $medSelect.prop('disabled', false);
            } else {
                $medSelect.prop('disabled', true);
            }
        });

        $medSelect.select2({
            theme: 'bootstrap-5',
            dropdownParent: $row,
            ajax: {
                url: '{{ route("medicines.ajax.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        category_id: $catSelect.val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.medicine_name + ' (' + item.trade_name + ') - ' + item.unit,
                                id: item.id,
                                unit: item.unit
                            }
                        })
                    };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            var data = e.params.data;
            $row.find('.unit-display').text(data.unit || 'Viên');
            $row.find('.unit-input').val(data.unit || 'Viên');
        });

        $row.find('.calc-times, .calc-days, .calc-dose').on('input', function() {
            let times = parseFloat($row.find('.calc-times').val()) || 0;
            let days = parseFloat($row.find('.calc-days').val()) || 0;
            let dose = parseFloat($row.find('.calc-dose').val()) || 0;
            
            if (times > 0 && days > 0 && dose > 0) {
                let total = times * days * dose;
                $row.find('.calc-total').val(total);
                
                let unit = $row.find('.unit-display').text() || 'Viên';
                $row.find('.dosage-input').val(`Ngày ${times} lần, mỗi lần ${dose} ${unit}`);
            }
            
            if ($(this).hasClass('calc-days') && days > 0) lastDays = days;
            if ($(this).hasClass('calc-times') && times > 0) lastTimes = times;
        });

        $row.find('.btnRemoveRow').on('click', function() {
            $row.slideUp(200, function() { $(this).remove(); });
        });
    }

    function addRow() {
        let $newRow = $template.clone().removeAttr('id').css('display', 'flex');
        
        $newRow.find('input[name="duration_days[]"]').prop('disabled', false);
        $newRow.find('input[name="quantities[]"]').prop('disabled', false);
        $newRow.find('input[name="units[]"]').prop('disabled', false);
        $newRow.find('input[name="usage_instructions[]"]').prop('disabled', false);
        $newRow.find('input[name="dosages[]"]').prop('disabled', false);
        $newRow.find('.calc-times, .calc-dose').prop('disabled', false);

        if (lastDays) $newRow.find('.calc-days').val(lastDays);
        if (lastTimes) $newRow.find('.calc-times').val(lastTimes);

        $container.append($newRow);
        initRow($newRow);
    }

    // Initialize existing rows
    $('.existing-row').each(function() {
        initRow($(this));
    });

    $('#btnAddMedicine, #btnAddMedicineLarge').click(addRow);
});
</script>
@endpush
