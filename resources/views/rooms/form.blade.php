@csrf

<div class="row">

    {{-- Mã phòng --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Mã phòng
        </label>

        <input type="text" name="room_code" class="form-control" value="{{ old('room_code', $room->room_code ?? '') }}"
            placeholder="Ví dụ: P101">

        @error('room_code')

            <small class="text-danger">
                {{ $message }}
            </small>

        @enderror

    </div>

    {{-- Tên phòng --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Tên phòng
        </label>

        <input type="text" name="room_name" class="form-control" value="{{ old('room_name', $room->room_name ?? '') }}"
            placeholder="Nhập tên phòng">

        @error('room_name')

            <small class="text-danger">
                {{ $message }}
            </small>

        @enderror

    </div>

</div>


<div class="row">

    {{-- Loại phòng --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Loại phòng
        </label>

        <select name="room_type" class="form-select">

            <option value="Examination" {{ old('room_type', $room->room_type ?? '') == 'Examination' ? 'selected' : '' }}>
                Phòng khám
            </option>

            <option value="Lab" {{ old('room_type', $room->room_type ?? '') == 'Lab' ? 'selected' : '' }}>
                Phòng xét nghiệm
            </option>

            <option value="Pharmacy" {{ old('room_type', $room->room_type ?? '') == 'Pharmacy' ? 'selected' : '' }}>
                Phòng phát thuốc
            </option>

            <option value="Emergency" {{ old('room_type', $room->room_type ?? '') == 'Emergency' ? 'selected' : '' }}>
                Phòng cấp cứu
            </option>

            <option value="Waiting" {{ old('room_type', $room->room_type ?? '') == 'Waiting' ? 'selected' : '' }}>
                Phòng chờ
            </option>

            <option value="Administrative" {{ old('room_type', $room->room_type ?? '') == 'Administrative' ? 'selected' : '' }}>
                Phòng hành chính
            </option>

        </select>

        @error('room_type')

            <small class="text-danger">
                {{ $message }}
            </small>

        @enderror

    </div>

    {{-- Khoa --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Khoa
        </label>

        <select name="department_id" class="form-select">

            <option value="">
                -- Chọn khoa --
            </option>

            @foreach($departments as $department)

                <option value="{{ $department->id }}" {{ old('department_id', $room->department_id ?? '') == $department->id ? 'selected' : '' }}>

                    {{ $department->dept_code }} - {{ $department->dept_name }}

                </option>

            @endforeach

        </select>

        @error('department_id')

            <small class="text-danger">
                {{ $message }}
            </small>

        @enderror

    </div>

</div>





{{-- Trạng thái --}}
<div class="mb-3">

    <label class="form-label">
        Trạng thái
    </label>

    <select name="status" class="form-select">

        <option value="1" {{ old('status', $room->status ?? 1) == 1 ? 'selected' : '' }}>
            Hoạt động
        </option>

        <option value="0" {{ old('status', $room->status ?? '') == 0 ? 'selected' : '' }}>
            Đóng
        </option>

        <option value="2" {{ old('status', $room->status ?? '') == 2 ? 'selected' : '' }}>
            Đang sử dụng
        </option>

        <option value="3" {{ old('status', $room->status ?? '') == 3 ? 'selected' : '' }}>
            Bảo trì
        </option>

    </select>

    @error('status')

        <small class="text-danger">
            {{ $message }}
        </small>

    @enderror

</div>


<button type="submit" class="btn btn-success">

    Lưu

</button>

<a href="{{ route('rooms.index') }}" class="btn btn-secondary">

    Quay lại

</a>