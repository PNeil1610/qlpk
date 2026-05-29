@csrf

<div class="row">
    {{-- CỘT TRÁI: THÔNG TIN TỪ PHIÊN KHÁM (CHỈ ĐỌC NẾU LÀ EDIT) --}}
    <div class="{{ isset($consultation) ? 'col-lg-6 border-end pe-4' : 'col-lg-12' }}">
        @if(isset($consultation))
            <h5 class="text-primary border-bottom pb-2 mb-4">
                <i class="fas fa-clipboard-list me-2"></i>Thông tin từ hành chính
            </h5>
        @endif

        {{-- BỆNH NHÂN --}}
        <div class="mb-3">
            <label class="form-label fw-bold text-muted">Bệnh nhân</label>
            @if(isset($selectedPatient) && $selectedPatient)
                <input type="text" class="form-control bg-light" value="{{ $selectedPatient->patient_code }} - {{ $selectedPatient->full_name }}" readonly>
                <input type="hidden" name="patient_id" value="{{ $selectedPatient->id }}">
            @elseif(isset($consultation))
                <input type="text" class="form-control bg-light" value="{{ $consultation->patient->patient_code }} - {{ $consultation->patient->full_name }}" readonly>
                <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">
            @else
                <select name="patient_id" id="patient_id" class="form-control @error('patient_id') is-invalid @enderror"></select>
            @endif
        </div>

        {{-- PHÒNG KHÁM --}}
        <div class="mb-3">
            <label class="form-label fw-bold text-muted">Phòng khám</label>
            @php 
                $currentRoomId = old('room_id', isset($consultation) ? ($consultation->rooms->first()->id ?? '') : ''); 
                
                // Mặc định chọn phòng có mã PK-SINGLE nếu tạo mới và chưa chọn phòng
                if (!isset($consultation) && !old('room_id') && isset($rooms)) {
                    $defaultRoom = $rooms->firstWhere('room_code', 'PK-SINGLE');
                    if ($defaultRoom) {
                        $currentRoomId = $defaultRoom->id;
                    }
                }
            @endphp
            @if(isset($consultation))
                @php 
                    $roomText = '-- Chưa có phòng --';
                    if($currentRoomId && isset($rooms)) {
                        $matchedRoom = $rooms->firstWhere('id', $currentRoomId);
                        if($matchedRoom) $roomText = $matchedRoom->room_name . ' (' . $matchedRoom->room_code . ') - ' . $matchedRoom->room_type_text;
                    }
                @endphp
                <input type="text" class="form-control bg-light" value="{{ $roomText }}" readonly>
                <input type="hidden" name="room_id" value="{{ $currentRoomId }}">
            @else
                <select name="room_id" class="form-select @error('room_id') is-invalid @enderror">
                    <option value="">Phòng chờ</option>
                    @if(isset($rooms))
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $currentRoomId == $room->id ? 'selected' : '' }}>
                                {{ $room->room_name }} ({{ $room->room_code }}) - {{ $room->room_type_text }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @endif
        </div>

        <div class="row">
            {{-- NGÀY KHÁM --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted">Ngày khám</label>
                <input type="datetime-local" name="consultation_date" class="form-control {{ isset($consultation) ? 'bg-light' : '' }}" 
                    value="{{ old('consultation_date', isset($consultation) ? date('Y-m-d\TH:i', strtotime($consultation->consultation_date)) : now()->format('Y-m-d\TH:i')) }}" 
                    {{ isset($consultation) ? 'readonly' : '' }}>
            </div>
            
            {{-- LÝ DO KHÁM --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted">Lý do khám</label>
                <input type="text" name="chief_complaint" class="form-control {{ isset($consultation) ? 'bg-light' : '' }} @error('chief_complaint') is-invalid @enderror" 
                    value="{{ old('chief_complaint', $consultation->chief_complaint ?? '') }}" 
                    {{ isset($consultation) ? 'readonly' : '' }}>
                @error('chief_complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- TRIỆU CHỨNG --}}
        <div class="mb-3">
            <label class="form-label fw-bold text-muted">Triệu chứng ban đầu</label>
            <textarea name="symptoms" rows="3" class="form-control {{ isset($consultation) ? 'bg-light' : '' }} @error('symptoms') is-invalid @enderror" 
                {{ isset($consultation) ? 'readonly' : '' }}>{{ old('symptoms', $consultation->symptoms ?? '') }}</textarea>
            @error('symptoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="row">
            {{-- NHIỆT ĐỘ --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted">Nhiệt độ (°C)</label>
                <input type="number" step="0.1" min="30" max="45" name="temperature" class="form-control {{ isset($consultation) ? 'bg-light' : '' }} @error('temperature') is-invalid @enderror" 
                    value="{{ old('temperature', $consultation->temperature ?? '') }}" 
                    {{ isset($consultation) ? 'readonly' : '' }}>
                @error('temperature') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- HUYẾT ÁP --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-muted">Huyết áp</label>
                <input type="text" name="blood_pressure" placeholder="Ví dụ: 120/80" class="form-control {{ isset($consultation) ? 'bg-light' : '' }} @error('blood_pressure') is-invalid @enderror" 
                    value="{{ old('blood_pressure', $consultation->blood_pressure ?? '') }}" 
                    {{ isset($consultation) ? 'readonly' : '' }}>
                @error('blood_pressure') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    {{-- CỘT PHẢI: PHẦN KHÁM CỦA BÁC SĨ --}}
    @if(isset($consultation))
    <div class="col-lg-6 ps-4">
        <h5 class="text-success border-bottom pb-2 mb-4">
            <i class="fas fa-user-md me-2"></i>Kết luận chuyên môn
        </h5>

        {{-- CHẨN ĐOÁN --}}
        <div class="mb-3">
            <label class="form-label fw-bold text-dark">Chẩn đoán <span class="text-danger">*</span></label>
            <textarea name="diagnosis_notes" rows="4" class="form-control border-success shadow-sm" placeholder="Nhập chẩn đoán chi tiết...">{{ old('diagnosis_notes', $consultation->diagnosis_notes ?? '') }}</textarea>
        </div>

        <div class="row">
            {{-- MÃ BỆNH --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-dark">Mã bệnh chuẩn</label>
                <select name="diagnosis_code" id="diagnosis_code" class="form-select border-success shadow-sm" data-placeholder="Nhập mã hoặc tên bệnh để tìm...">
                    @if(old('diagnosis_code', $consultation->diagnosis_code ?? ''))
                        <option value="{{ old('diagnosis_code', $consultation->diagnosis_code ?? '') }}" selected>
                            {{ old('diagnosis_code', $consultation->diagnosis_code ?? '') }}
                            {{ isset($consultation->diagnosis) ? ' - ' . $consultation->diagnosis->diagnosis_name : '' }}
                        </option>
                    @endif
                </select>
            </div>

            {{-- NGÀY TÁI KHÁM --}}
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold text-dark">Ngày tái khám</label>
                <input type="date" name="follow_up_date" class="form-control border-success shadow-sm" value="{{ old('follow_up_date', isset($consultation->follow_up_date) ? date('Y-m-d', strtotime($consultation->follow_up_date)) : '') }}">
            </div>
        </div>

        {{-- TRẠNG THÁI --}}
        <div class="mb-4">
            <label class="form-label fw-bold text-dark">Trạng thái phiên khám</label>
            <select name="status" class="form-select border-success shadow-sm fw-bold bg-success bg-opacity-10 text-success">
                <option value="0" {{ old('status', $consultation->status) == 0 ? 'selected' : '' }}>Mới tạo</option>
                <option value="1" {{ old('status', $consultation->status) == 1 ? 'selected' : '' }}>Chờ khám</option>
                <option value="2" {{ old('status', $consultation->status) == 2 ? 'selected' : '' }}>Đang khám</option>
                <option value="3" {{ old('status', $consultation->status) == 3 ? 'selected' : '' }}>Chờ thanh toán</option>
                <option value="4" {{ old('status', $consultation->status) == 4 ? 'selected' : '' }}>Hoàn thành</option>
            </select>
        </div>
        
        <div class="alert alert-info rounded-3 shadow-sm border-0 bg-light">
            <i class="fas fa-info-circle me-2 text-info"></i>
            <small class="text-muted">Các thông tin ở cột bên trái đã được nhập liệu bởi lễ tân và không thể thay đổi để đảm bảo tính xác thực hồ sơ bệnh án.</small>
        </div>
    </div>
    @endif
</div>

{{-- BUTTONS --}}
@php
    $previousUrl = url()->previous();
    $currentUrl = url()->current();
    
    if ($previousUrl === $currentUrl || $previousUrl === url('/')) {
        if(auth()->check() && auth()->user()->hasRole('DOCTOR')) {
            $backUrl = route('consultations.doctor');
        } else {
            $backUrl = route('consultations.index');
        }
    } else {
        $backUrl = $previousUrl;
    }
@endphp
<div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
    <a href="{{ $backUrl }}" class="btn btn-secondary shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Quay lại
    </a>
    <button type="submit" class="btn btn-success shadow-sm px-4 fw-bold">
        <i class="fas fa-save me-1"></i> {{ isset($consultation) ? 'Lưu Kết Luận' : 'Tạo Phiên Khám' }}
    </button>
</div>