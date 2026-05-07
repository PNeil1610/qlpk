@csrf

<div class="mb-3">

    <label>Mã bệnh</label>

    <input type="text" name="diagnosis_code" class="form-control"
        value="{{ old('diagnosis_code', $diagnosis->diagnosis_code ?? '') }}">

</div>

<div class="mb-3">

    <label>Tên bệnh</label>

    <input type="text" name="diagnosis_name" class="form-control"
        value="{{ old('diagnosis_name', $diagnosis->diagnosis_name ?? '') }}">

</div>

<div class="mb-3">

    <label>Phiên bản ICD</label>

    <input type="text" name="icd_version" class="form-control" placeholder="Ví dụ: ICD-10"
        value="{{ old('icd_version', $diagnosis->icd_version ?? '') }}">

</div>

<div class="mb-3">

    <label>Mô tả</label>

    <textarea name="description" class="form-control"
        rows="4">{{ old('description', $diagnosis->description ?? '') }}</textarea>

</div>

<div class="mb-3">

    <label>Trạng thái</label>

    <select name="status" class="form-control">

        <option value="1" {{ old('status', $diagnosis->status ?? 1) == 1 ? 'selected' : '' }}>
            Hoạt động
        </option>

        <option value="0" {{ old('status', $diagnosis->status ?? 1) == 0 ? 'selected' : '' }}>
            Không hoạt động
        </option>

    </select>

</div>

<button class="btn btn-success">
    Lưu
</button>

<a href="{{ route('diagnoses.index') }}" class="btn btn-secondary">
    Quay lại
</a>