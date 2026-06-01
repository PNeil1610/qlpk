@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary"><i class="fas fa-edit"></i> Phiên Khám</h3>
            <span class="text-muted">Bệnh nhân: <strong>{{ $consultation->patient->full_name }}</strong></span>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-clipboard-list text-primary me-2"></i>Thông tin Phiên Khám
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('consultations.update', $consultation->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Giữ lại các field không cho sửa --}}
                        <input type="hidden" name="patient_id" value="{{ $consultation->patient_id }}">
                        <input type="hidden" name="consultation_date" value="{{ $consultation->consultation_date }}">
                        <input type="hidden" name="chief_complaint" value="{{ $consultation->chief_complaint }}">
                        <input type="hidden" name="symptoms" value="{{ $consultation->symptoms }}">
                        <input type="hidden" name="temperature" value="{{ $consultation->temperature }}">
                        <input type="hidden" name="blood_pressure" value="{{ $consultation->blood_pressure }}">

                        {{-- BỆNH NHÂN (chỉ đọc) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Bệnh nhân</label>
                            <input type="text" class="form-control bg-light" value="{{ $consultation->patient->patient_code }} - {{ $consultation->patient->full_name }}" readonly>
                        </div>

                        {{-- NGÀY KHÁM (chỉ đọc) --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Ngày khám</label>
                                <input type="text" class="form-control bg-light" value="{{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Lý do khám</label>
                                <input type="text" class="form-control bg-light" value="{{ $consultation->chief_complaint ?? '---' }}" readonly>
                            </div>
                        </div>

                        {{-- TRIỆU CHỨNG (chỉ đọc) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Triệu chứng ban đầu</label>
                            <textarea class="form-control bg-light" rows="2" readonly>{{ $consultation->symptoms ?? '---' }}</textarea>
                        </div>

                        {{-- NHIỆT ĐỘ & HUYẾT ÁP (chỉ đọc) --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Nhiệt độ (°C)</label>
                                <input type="text" class="form-control bg-light" value="{{ $consultation->temperature ?? '---' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Huyết áp</label>
                                <input type="text" class="form-control bg-light" value="{{ $consultation->blood_pressure ?? '---' }}" readonly>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary fw-bold mb-3"><i class="fas fa-cog me-2"></i>Thông tin có thể chỉnh sửa</h6>

                        {{-- PHÒNG KHÁM (cho phép sửa) --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Phòng khám</label>
                            @php
                                $currentRoomId = old('room_id', $consultation->rooms->first()->id ?? '');
                                
                                // Mặc định chọn phòng có mã PK-SINGLE nếu chưa phân phòng và chưa có old('room_id')
                                if (!$currentRoomId && !old('room_id') && isset($rooms)) {
                                    $defaultRoom = $rooms->firstWhere('room_code', 'PK-SINGLE');
                                    if ($defaultRoom) {
                                        $currentRoomId = $defaultRoom->id;
                                    }
                                }
                            @endphp
                            <select name="room_id" class="form-select shadow-sm @error('room_id') is-invalid @enderror">
                                <option value="">-- Chưa phân phòng --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $currentRoomId == $room->id ? 'selected' : '' }}>
                                        {{ $room->room_name }} ({{ $room->room_code }}) — {{ $room->room_type_text }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- TRẠNG THÁI (cho phép sửa) --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Trạng thái phiên khám</label>
                            <select name="status" class="form-select shadow-sm @error('status') is-invalid @enderror">
                                <option value="0" {{ old('status', $consultation->status) == 0 ? 'selected' : '' }}>Mới tạo</option>
                                <option value="1" {{ old('status', $consultation->status) == 1 ? 'selected' : '' }}>Chờ khám</option>
                                <option value="2" {{ old('status', $consultation->status) == 2 ? 'selected' : '' }}>Đang khám</option>
                                <option value="3" {{ old('status', $consultation->status) == 3 ? 'selected' : '' }}>Chờ thanh toán</option>
                                <option value="4" {{ old('status', $consultation->status) == 4 ? 'selected' : '' }}>Hoàn thành</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="text-end pt-3 border-top">
                            <a href="{{ route('consultations.index') }}" class="btn btn-secondary shadow-sm me-2">
                                <i class="fas fa-times me-1"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-primary shadow-sm px-5 fw-bold">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection