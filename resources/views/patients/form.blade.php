@csrf

<div class="row">
    <div class="col-md-6 mb-3">
        <label>Họ tên *</label>
        <input type="text" name="full_name" class="form-control"
               value="{{ old('full_name', $patient->full_name ?? '') }}">
        @error('full_name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label>Giới tính *</label>
        <select name="gender" class="form-control">
            <option value="Male" {{ old('gender', $patient->gender ?? '') == 'Male' ? 'selected' : '' }}>Nam</option>
            <option value="Female" {{ old('gender', $patient->gender ?? '') == 'Female' ? 'selected' : '' }}>Nữ</option>
            <option value="Other" {{ old('gender', $patient->gender ?? '') == 'Other' ? 'selected' : '' }}>Khác</option>
        </select>
    </div>

    <div class="col-md-3 mb-3">
        <label>Ngày sinh</label>
        <input type="date" name="date_of_birth" class="form-control"
               value="{{ old('date_of_birth', $patient->date_of_birth ?? '') }}">
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
        <label>Nhóm máu</label>
        <select name="blood_type" class="form-control">
            @foreach(['A','B','AB','O','Unknown'] as $type)
                <option value="{{ $type }}" 
                    {{ old('blood_type', $patient->blood_type ?? '') == $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
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
</div>

<button class="btn btn-success">💾 Lưu</button>
<a href="{{ route('patients.index') }}" class="btn btn-secondary">Quay lại</a>