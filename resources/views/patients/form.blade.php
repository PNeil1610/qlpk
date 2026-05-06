@csrf

<div class="row">

    <div class="col-md-6 mb-3">
        <label>Họ tên *</label>
        <input type="text" name="full_name" class="form-control"
               value="{{ old('full_name', $patient->full_name ?? '') }}">
    </div>

    <div class="col-md-3 mb-3">
        <label>Giới tính</label>
        <select name="gender" class="form-control">
    <option value="Male" {{ old('gender', $patient->gender ?? '') == 'Male' ? 'selected' : '' }}>
        Nam
    </option>
    <option value="Female" {{ old('gender', $patient->gender ?? '') == 'Female' ? 'selected' : '' }}>
        Nữ
    </option>
    <option value="Other" {{ old('gender', $patient->gender ?? '') == 'Other' ? 'selected' : '' }}>
        Khác
    </option>
</select>
    </div>

    <div class="col-md-3 mb-3">
        <label>Tuổi</label>
        <input type="number" name="age" class="form-control"
               value="{{ old('age', $patient->age ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>SĐT</label>
        <input type="text" name="phone_number" class="form-control"
               value="{{ old('phone_number', $patient->phone_number ?? '') }}">
    </div>

    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control"
               value="{{ old('email', $patient->email ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label>Địa chỉ</label>
        <input type="text" name="address" class="form-control"
               value="{{ old('address', $patient->address ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label>CCCD</label>
        <input type="text" name="national_id" class="form-control"
               value="{{ old('national_id', $patient->national_id ?? '') }}">
    </div>


    <div class="col-md-4 mb-3">
        <label>Bảo hiểm</label>
        <input type="text" name="insurance_number" class="form-control"
               value="{{ old('insurance_number', $patient->insurance_number ?? '') }}">
    </div>
    <div class="col-md-4 mb-3">
        <label>Trạng thái</label>
    <select name="status" class="form-control">
    <option value="1" {{ old('status', $patient->status ?? 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
    <option value="0" {{ old('status', $patient->status ?? 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
</select>
    </div>
</div>

<button class="btn btn-success">💾 Lưu</button>