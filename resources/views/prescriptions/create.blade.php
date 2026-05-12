@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-prescription me-2"></i>Kê Toa Thuốc</h3>
        <a href="{{ route('consultations.show', $consultation->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Trở về phiên khám
        </a>
    </div>

    <div class="card shadow-sm border-0 border-top border-info border-3 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p class="text-muted mb-1">Bệnh nhân:</p>
                    <h5 class="fw-bold">{{ $consultation->patient->full_name }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1">Ngày khám:</p>
                    <h5 class="fw-bold">{{ $consultation->consultation_date->format('d/m/Y H:i') }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1">Chẩn đoán (Triệu chứng):</p>
                    <h5 class="fw-bold text-danger">{{ $consultation->symptoms ?? 'Chưa cập nhật' }}</h5>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('prescriptions.store') }}" method="POST">
        @csrf
        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
                <span><i class="fas fa-pills text-success me-1"></i> Danh sách thuốc kê đơn</span>
                <button type="button" class="btn btn-sm btn-success shadow-sm" id="btn-add-medicine">
                    <i class="fas fa-plus"></i> Thêm Thuốc
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Tên Thuốc *</th>
                                <th style="width: 10%;">SL *</th>
                                <th style="width: 10%;">Đơn vị</th>
                                <th style="width: 20%;">Liều dùng *</th>
                                <th style="width: 20%;">Cách dùng</th>
                                <th style="width: 10%;">Số ngày</th>
                                <th style="width: 5%;" class="text-center">Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="prescription-body">
                            <tr>
                                <td>
                                    <select name="medicines[]" class="form-select medicine-select" required>
                                        <option value="">-- Chọn thuốc --</option>
                                        @foreach($medicines as $med)
                                            <option value="{{ $med->id }}" data-unit="{{ $med->details->first()->unit ?? 'Viên' }}">
                                                {{ $med->medicine_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="quantities[]" class="form-control" min="1" placeholder="0" required>
                                </td>
                                <td>
                                    <input type="text" name="units[]" class="form-control unit-input" readonly placeholder="---">
                                </td>
                                <td>
                                    <input type="text" name="dosages[]" class="form-control" placeholder="VD: 1 viên/lần" required>
                                </td>
                                <td>
                                    <input type="text" name="usage_instructions[]" class="form-control" placeholder="VD: Sau ăn">
                                </td>
                                <td>
                                    <input type="number" name="duration_days[]" class="form-control" min="1" placeholder="Ngày">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-row" disabled><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <label class="form-label fw-bold">Lời dặn của Bác sĩ (Tùy chọn)</label>
                <textarea name="doctor_advice" class="form-control" rows="3" placeholder="Ghi chú thêm về chế độ ăn uống, kiêng cữ..."></textarea>
            </div>
        </div>

        <div class="text-end mb-5">
            <button type="submit" class="btn btn-primary btn-lg px-5 shadow">
                <i class="fas fa-save me-1"></i> Lưu Toa Thuốc & Hoàn Tất Khám
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('prescription-body');
    const btnAdd = document.getElementById('btn-add-medicine');

    // 1. Tự động điền "Đơn vị tính" khi chọn thuốc
    tableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('medicine-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit');
            const row = e.target.closest('tr');
            const unitInput = row.querySelector('.unit-input');
            
            if (unit) {
                unitInput.value = unit;
            } else {
                unitInput.value = '';
            }
        }
    });

    // 2. Thêm một hàng mới khi bấm nút
    btnAdd.addEventListener('click', function() {
        const firstRow = tableBody.querySelector('tr');
        const newRow = firstRow.cloneNode(true); // Nhân bản dòng đầu tiên

        // Reset lại giá trị các ô nhập liệu trong dòng mới
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelector('select').selectedIndex = 0;
        
        // Mở khóa nút Xóa cho dòng mới
        const removeBtn = newRow.querySelector('.remove-row');
        removeBtn.disabled = false;

        tableBody.appendChild(newRow);
        updateRemoveButtons();
    });

    // 3. Xóa hàng khi bấm nút X (thùng rác)
    tableBody.addEventListener('click', function(e) {
        // Tìm element button dù người dùng có bấm vào icon <i> bên trong
        const btn = e.target.closest('.remove-row');
        if (btn) {
            const rowCount = tableBody.querySelectorAll('tr').length;
            if (rowCount > 1) { // Phải chừa lại ít nhất 1 dòng
                btn.closest('tr').remove();
                updateRemoveButtons();
            }
        }
    });

    // Cập nhật trạng thái nút Xóa (Nếu chỉ có 1 dòng thì khóa nút Xóa lại)
    function updateRemoveButtons() {
        const rows = tableBody.querySelectorAll('tr');
        const removeBtns = tableBody.querySelectorAll('.remove-row');
        
        if (rows.length === 1) {
            removeBtns[0].disabled = true;
        } else {
            removeBtns.forEach(btn => btn.disabled = false);
        }
    }
});
</script>
@endsection