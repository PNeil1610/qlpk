@csrf

<div class="mb-3">

    <label>Mã khoa</label>

    <input type="text" name="dept_code" class="form-control"
        value="{{ old('dept_code', $department->dept_code ?? '') }}">

</div>

<div class="mb-3">

    <label>Tên khoa</label>

    <input type="text" name="dept_name" class="form-control"
        value="{{ old('dept_name', $department->dept_name ?? '') }}">

</div>

<div class="mb-3">

    <label>Mô tả</label>

    <textarea name="description" class="form-control"
        rows="4">{{ old('description', $department->description ?? '') }}</textarea>

</div>

<div class="mb-3">

    <label>Trạng thái</label>

    <select name="status" class="form-control">

        <option value="1" {{ old('status', $department->status ?? 1) == 1 ? 'selected' : '' }}>
            Hoạt động
        </option>

        <option value="0" {{ old('status', $department->status ?? 1) == 0 ? 'selected' : '' }}>
            Không hoạt động
        </option>

        <option value="2" {{ old('status', $department->status ?? 1) == 2 ? 'selected' : '' }}>
            Đang xây dựng
        </option>

    </select>

</div>

<button type="submit" class="btn btn-success">
    Lưu
</button>

<a href="{{ route('departments.index') }}" class="btn btn-secondary">
    Quay lại
</a>