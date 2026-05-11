@csrf

{{-- ========================
BỆNH NHÂN
======================== --}}
<div class="mb-3">

    <label class="form-label">
        Bệnh nhân
    </label>

    {{-- ========================
    CREATE TỪ PATIENT
    ======================== --}}
    @if(isset($selectedPatient) && $selectedPatient)

        <input type="text" class="form-control"
            value="{{ $selectedPatient->patient_code }} - {{ $selectedPatient->full_name }}" readonly>

        <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">

        {{-- ========================
        EDIT CONSULTATION
        ======================== --}}
    @elseif(isset($consultation))

        <input type="text" class="form-control"
            value="{{ $consultation->patient->patient_code }} - {{ $consultation->patient->full_name }}" readonly>

        <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">

        {{-- ========================
        CREATE THƯỜNG
        ======================== --}}
    @else

        <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror">
        </select>

    @endif

</div>


{{-- ========================
CHỌN PHÒNG KHÁM (Khi tạo mới)
======================== --}}
@if(!isset($consultation))
<div class="mb-3">
    <label class="form-label">Phòng khám</label>
    <select name="room_id" class="form-control @error('room_id') is-invalid @enderror">
        <option value="">-- Chọn phòng khám (Tùy chọn) --</option>
        @if(isset($rooms))
            @foreach($rooms as $room)
                <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                    {{ $room->room_name }} ({{ $room->room_code }}) - {{ $room->room_type_text }}
                </option>
            @endforeach
        @endif
    </select>
    @error('room_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
@endif


{{-- ========================
NGÀY KHÁM
======================== --}}
<div class="mb-3">
    <label class="form-label">Ngày khám</label>

    <input type="datetime-local" name="consultation_date" class="form-control" value="{{ old(
    'consultation_date',
    isset($consultation)
    ? date('Y-m-d\TH:i', strtotime($consultation->consultation_date))
    : now()->format('Y-m-d\TH:i')
) }}" {{ isset($consultation) ? 'readonly' : '' }}>
</div>


{{-- ========================
LÝ DO KHÁM
======================== --}}
<div class="mb-3">
    <label class="form-label">Lý do khám</label>

    <input type="text" name="chief_complaint" class="form-control @error('chief_complaint') is-invalid @enderror"
        value="{{ old('chief_complaint', $consultation->chief_complaint ?? '') }}">

    @error('chief_complaint')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- ========================
TRIỆU CHỨNG
======================== --}}
<div class="mb-3">
    <label class="form-label">Triệu chứng</label>

    <textarea name="symptoms" rows="3"
        class="form-control @error('symptoms') is-invalid @enderror">{{ old('symptoms', $consultation->symptoms ?? '') }}</textarea>

    @error('symptoms')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- ========================
NHIỆT ĐỘ
======================== --}}
<div class="mb-3">
    <label class="form-label">Nhiệt độ (°C)</label>

    <input type="number" step="0.1" min="30" max="45" name="temperature"
        class="form-control @error('temperature') is-invalid @enderror"
        value="{{ old('temperature', $consultation->temperature ?? '') }}">

    @error('temperature')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>


{{-- ========================
HUYẾT ÁP
======================== --}}
<div class="mb-3">
    <label class="form-label">Huyết áp</label>

    <input type="text" name="blood_pressure" placeholder="Ví dụ: 120/80"
        class="form-control @error('blood_pressure') is-invalid @enderror"
        value="{{ old('blood_pressure', $consultation->blood_pressure ?? '') }}">

    @error('blood_pressure')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

{{-- ========================
PHẦN BÁC SĨ
======================== --}}
@if(isset($consultation))

    {{-- CHẨN ĐOÁN --}}
    <div class="mb-3">
        <label class="form-label">
            Chẩn đoán
        </label>

        <textarea name="diagnosis_notes" rows="3"
            class="form-control">{{ old('diagnosis_notes', $consultation->diagnosis_notes ?? '') }}</textarea>
    </div>


    {{-- MÃ BỆNH --}}
    <div class="mb-3">
        <label class="form-label">
            Mã bệnh chuẩn
        </label>

        <input type="text" name="diagnosis_code" class="form-control"
            value="{{ old('diagnosis_code', $consultation->diagnosis_code ?? '') }}">
    </div>


    {{-- NGÀY TÁI KHÁM --}}
    <div class="mb-3">
        <label class="form-label">
            Ngày tái khám
        </label>

        <input type="date" name="follow_up_date" class="form-control" value="{{ old(
            'follow_up_date',
            isset($consultation->follow_up_date)
            ? date('Y-m-d', strtotime($consultation->follow_up_date))
            : ''
        ) }}">
    </div>

@endif

{{-- ========================
TRẠNG THÁI
======================== --}}
<div class="mb-3">
    @if(isset($consultation))

        <div class="mb-3">

            <label class="form-label">
                Trạng thái
            </label>

            <select name="status" class="form-control">

                <option value="0" {{ old('status', $consultation->status) == 0 ? 'selected' : '' }}>
                    Mới tạo
                </option>

                <option value="1" {{ old('status', $consultation->status) == 1 ? 'selected' : '' }}>
                    Chờ
                </option>

                <option value="2" {{ old('status', $consultation->status) == 2 ? 'selected' : '' }}>
                    Đang khám
                </option>

                <option value="3" {{ old('status', $consultation->status) == 3 ? 'selected' : '' }}>
                    Chờ thanh toán
                </option>

                <option value="4" {{ old('status', $consultation->status) == 4 ? 'selected' : '' }}>
                    Hoàn thành
                </option>

            </select>

        </div>

    @endif
</div>


{{-- ========================
BUTTON
======================== --}}
<button class="btn btn-success">
    Lưu
</button>

<a href="{{ route('consultations.index') }}" class="btn btn-secondary">
    Quay lại
</a>