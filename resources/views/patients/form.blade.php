@csrf

<div class="row">

    <div class="col-md-6 mb-3">
        <label>Họ tên *</label>
        <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
               value="{{ old('full_name', $patient->full_name ?? '') }}">
        @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label>Giới tính</label>
        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
            <option value="Male" {{ old('gender', $patient->gender ?? '') == 'Male' ? 'selected' : '' }}>Nam</option>
            <option value="Female" {{ old('gender', $patient->gender ?? '') == 'Female' ? 'selected' : '' }}>Nữ</option>
            <option value="Other" {{ old('gender', $patient->gender ?? '') == 'Other' ? 'selected' : '' }}>Khác</option>
        </select>
        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label>Tuổi</label>
        <input type="number" name="age" class="form-control @error('age') is-invalid @enderror"
               value="{{ old('age', $patient->age ?? '') }}">
        @error('age') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label>SĐT</label>
        <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror"
               value="{{ old('phone_number', $patient->phone_number ?? '') }}">
        @error('phone_number') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $patient->email ?? '') }}">
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-12 mb-3">
        <label>Địa chỉ</label>
        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $patient->address ?? '') }}">
        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label>CCCD</label>
        <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
               value="{{ old('national_id', $patient->national_id ?? '') }}">
        @error('national_id') <small class="text-danger">{{ $message }}</small> @enderror
    </div>


    <div class="col-md-4 mb-3">
        <label>Bảo hiểm</label>
        <input type="text" name="insurance_number" class="form-control @error('insurance_number') is-invalid @enderror"
               value="{{ old('insurance_number', $patient->insurance_number ?? '') }}">
        @error('insurance_number') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label>Trạng thái</label>
        <select name="status" class="form-control @error('status') is-invalid @enderror">
            <option value="1" {{ old('status', $patient->status ?? 1) == 1 ? 'selected' : '' }}>Hoạt động</option>
            <option value="0" {{ old('status', $patient->status ?? 1) == 0 ? 'selected' : '' }}>Không hoạt động</option>
        </select>
        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('patients.index') }}" class="btn btn-secondary me-2">Quay lại</a>
    <button class="btn btn-success">Lưu</button>
</div>