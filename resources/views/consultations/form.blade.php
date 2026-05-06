@csrf

<div class="mb-3">
    <label>Bệnh nhân</label>
    <select name="patient_id" id="patient_id" class="form-control"></select>
</div>

<div class="mb-3">
    <label>Ngày khám</label>
    <input type="datetime-local" name="consultation_date" class="form-control"
        value="{{ old('consultation_date', isset($consultation) ? date('Y-m-d\TH:i', strtotime($consultation->consultation_date)) : '') }}">
</div>

<div class="mb-3">
    <label>Triệu chứng</label>
    <textarea name="symptoms" class="form-control">{{ old('symptoms', $consultation->symptoms ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label>Lý do khám</label>
    <input type="text" name="chief_complaint" class="form-control"
           value="{{ old('chief_complaint', $consultation->chief_complaint ?? '') }}">
</div>
<div class="mb-3">
    <label>Chẩn đoán</label>
    <input type="text" name="diagnosis_notes" class="form-control"
           value="{{ old('diagnosis_notes', $consultation->diagnosis_notes ?? '') }}">
</div>

<div class="mb-3">
    <label>Mã bệnh chuẩn</label>
    <input type="text" name="diagnosis_code" class="form-control"
           value="{{ old('diagnosis_code', $consultation->diagnosis_code ?? '') }}">
</div>

<div class="mb-3">
    <label>Trạng thái</label>
    <select name="status" class="form-control">
        <option value="0">Mới tạo</option>
        <option value="1">Chờ</option>
        <option value="2">Đang khám</option>
        <option value="3">Chờ thanh toán</option>
        <option value="4">Hoàn thành</option>
    </select>
</div>

<button class="btn btn-success">Lưu</button>
<a href="{{ route('consultations.index') }}" class="btn btn-secondary">Quay lại</a>