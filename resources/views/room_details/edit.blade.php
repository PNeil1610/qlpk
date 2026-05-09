@extends('layouts.app')

@section('content')
    <h3>Sửa chi tiết phòng</h3>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('room_details.update', $roomDetail->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Phòng <span class="text-danger">*</span></label>
                    <select name="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">-- Chọn phòng --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $roomDetail->room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->room_name }} ({{ $room->room_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Danh sách thiết bị</label>
                    <textarea name="equipment_list" class="form-control @error('equipment_list') is-invalid @enderror" rows="3">{{ old('equipment_list', $roomDetail->equipment_list) }}</textarea>
                    @error('equipment_list') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Diện tích (m2)</label>
                    <input type="number" step="0.01" name="area_sqm" class="form-control @error('area_sqm') is-invalid @enderror" value="{{ old('area_sqm', $roomDetail->area_sqm) }}">
                    @error('area_sqm') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $roomDetail->notes) }}</textarea>
                    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Lần dọn dẹp cuối</label>
                    <input type="datetime-local" name="last_cleaned_at" class="form-control @error('last_cleaned_at') is-invalid @enderror" value="{{ old('last_cleaned_at', $roomDetail->last_cleaned_at ? $roomDetail->last_cleaned_at->format('Y-m-d\TH:i') : '') }}">
                    @error('last_cleaned_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
@endsection
