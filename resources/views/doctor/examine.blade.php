@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary"><i class="fas fa-stethoscope me-2"></i>Khám bệnh & Kê đơn thuốc</h3>
            <span class="text-muted">Bệnh nhân: <strong>{{ $consultation->patient->full_name }}</strong> &mdash; Mã PK: <strong>#{{ $consultation->id }}</strong></span>
        </div>
        <a href="{{ route('consultations.doctor') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
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
                        <tr><th class="text-muted fw-normal">Bệnh nhân</th><td class="fw-bold">{{ $consultation->patient->full_name }}</td></tr>
                        <tr><th class="text-muted fw-normal">Mã BN</th><td>{{ $consultation->patient->patient_code }}</td></tr>
                        <tr><th class="text-muted fw-normal">Ngày khám</th><td>{{ $consultation->consultation_date ? \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') : '--' }}</td></tr>
                        <tr><th class="text-muted fw-normal">Lý do khám</th><td>{{ $consultation->chief_complaint ?? '--' }}</td></tr>
                        <tr><th class="text-muted fw-normal">Nhiệt độ</th><td>{{ $consultation->temperature ? $consultation->temperature . ' °C' : '--' }}</td></tr>
                        <tr><th class="text-muted fw-normal">Huyết áp</th><td>{{ $consultation->blood_pressure ?? '--' }}</td></tr>
                        <tr>
                            <th class="text-muted fw-normal">Triệu chứng</th>
                            <td><span class="text-danger">{{ $consultation->symptoms ?? '--' }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Toa thuốc hiện tại nếu đã có --}}
            @if($consultation->prescriptions && $consultation->prescriptions->count() > 0)
                <div class="card shadow-sm border-0 border-top border-success border-3">
                    <div class="card-header bg-white fw-bold py-3 text-success">
                        <i class="fas fa-prescription-bottle me-2"></i>Toa thuốc đã kê
                    </div>
                    <div class="card-body p-2">
                        @foreach($consultation->prescriptions as $pres)
                            <div class="d-flex mb-1">
                                <a href="{{ route('prescriptions.show', $pres->id) }}" class="btn btn-sm btn-outline-success flex-grow-1 text-start overflow-hidden">
                                    <i class="fas fa-file-prescription me-1"></i> Toa #{{ $pres->id }} &mdash; {{ $pres->prescribed_at ? \Carbon\Carbon::parse($pres->prescribed_at)->format('d/m H:i') : '' }}
                                </a>
                                @if($pres->status == 1)
                                    <form action="{{ route('prescriptions.destroy', $pres->id) }}" method="POST" class="ms-1" onsubmit="return confirm('Bạn có chắc chắn muốn xóa toa thuốc này không?')">
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
        </div>

        {{-- ====== CỘT PHẢI: KẾT LUẬN + KÊ ĐƠN ====== --}}
        <div class="col-xl-9 col-lg-8">

            {{-- PHẦN 1: KẾT LUẬN CHUYÊN MÔN --}}
            <div class="card shadow-sm border-0 border-top border-warning border-3 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-user-md text-warning me-2"></i>Kết luận chuyên môn
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('consultations.update', $consultation->id) }}" id="examineForm">
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
                                <label class="form-label fw-bold">Chẩn đoán chi tiết <span class="text-danger">*</span></label>
                                <textarea name="diagnosis_notes" rows="3" class="form-control border-warning shadow-sm" placeholder="Nhập kết luận chẩn đoán...">{{ old('diagnosis_notes', $consultation->diagnosis_notes ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Mã bệnh chuẩn (ICD)</label>
                                <select name="diagnosis_code" id="diagnosis_code" class="form-select border-warning shadow-sm" data-placeholder="Nhập mã hoặc tên bệnh...">
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
                                    <option value="0" {{ old('status', $consultation->status) == 0 ? 'selected' : '' }}>Mới tạo</option>
                                    <option value="1" {{ old('status', $consultation->status) == 1 ? 'selected' : '' }}>Chờ khám</option>
                                    <option value="2" {{ old('status', $consultation->status) == 2 ? 'selected' : '' }}>Đang khám</option>
                                    <option value="3" {{ old('status', $consultation->status) == 3 ? 'selected' : '' }}>Chờ thanh toán</option>
                                    <option value="4" {{ old('status', $consultation->status) == 4 ? 'selected' : '' }}>Hoàn thành</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning shadow-sm px-4 fw-bold text-dark">
                                <i class="fas fa-save me-1"></i> Lưu Kết Luận
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- PHẦN 2: KÊ ĐƠN THUỐC --}}
            <div class="card shadow-sm border-0 border-top border-success border-3">
                <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-pills text-success me-2"></i>Kê đơn thuốc</span>
                    <button type="button" class="btn btn-sm btn-success shadow-sm" id="btn-add-medicine">
                        <i class="fas fa-plus me-1"></i> Thêm thuốc
                    </button>
                </div>
                <div class="card-body">
                    <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm">
                        @csrf
                        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered mb-3 align-middle" style="min-width: 700px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:28%">Tên Thuốc *</th>
                                        <th style="width:8%">SL *</th>
                                        <th style="width:10%">Đơn vị</th>
                                        <th style="width:18%">Liều dùng *</th>
                                        <th style="width:20%">Cách dùng</th>
                                        <th style="width:8%">Số ngày</th>
                                        <th style="width:8%" class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="prescription-body">
                                    <tr>
                                        <td>
                                            <select name="medicines[]" class="form-select form-select-sm medicine-select" required>
                                                <option value="">-- Chọn thuốc --</option>
                                                @foreach($medicines as $med)
                                                    <option value="{{ $med->id }}" data-unit="{{ $med->details->first()->unit ?? 'Viên' }}">
                                                        {{ $med->medicine_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="quantities[]" class="form-control form-control-sm" min="1" placeholder="0" required></td>
                                        <td><input type="text" name="units[]" class="form-control form-control-sm unit-input" readonly placeholder="---"></td>
                                        <td><input type="text" name="dosages[]" class="form-control form-control-sm" placeholder="1 viên/lần" required></td>
                                        <td><input type="text" name="usage_instructions[]" class="form-control form-control-sm" placeholder="Sau ăn"></td>
                                        <td><input type="number" name="duration_days[]" class="form-control form-control-sm" min="1" placeholder="Ngày"></td>
                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="fas fa-times"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lời dặn của Bác sĩ</label>
                            <textarea name="doctor_advice" class="form-control" rows="2" placeholder="Ghi chú chế độ ăn uống, kiêng cữ..."></textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success shadow-sm px-4 fw-bold">
                                <i class="fas fa-file-prescription me-1"></i> Lưu Toa Thuốc
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

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

    // Tự động điền đơn vị khi chọn thuốc
    const tableBody = document.getElementById('prescription-body');
    const btnAdd = document.getElementById('btn-add-medicine');

    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('medicine-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit');
            const unitInput = e.target.closest('tr').querySelector('.unit-input');
            unitInput.value = unit || '';
        }
    });

    btnAdd.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelector('select').selectedIndex = 0;
        newRow.querySelector('.remove-row').disabled = false;
        tableBody.appendChild(newRow);
        updateRemoveButtons();
    });

    tableBody.addEventListener('click', function(e) {
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
});
</script>
@endpush
