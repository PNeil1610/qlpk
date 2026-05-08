<div class="mb-3">

    <label class="form-label">
        Phiên khám
    </label>

    <select name="consultation_id" id="consultationSelect" class="form-control" required>

        @if(isset($consultationRoom))

            <option value="{{ $consultationRoom->consultation_id }}" selected>

                #{{ $consultationRoom->consultation_id }}
                - {{ $consultationRoom->consultation->patient->full_name ?? 'N/A' }}

            </option>

        @endif

    </select>

</div>

<div class="mb-3">

    <label class="form-label">
        Phòng
    </label>

    <select name="room_id" class="form-control" required>

        <option value="">
            -- Chọn phòng --
        </option>

        @foreach($rooms as $room)

            <option value="{{ $room->id }}" {{ old('room_id', $consultationRoom->room_id ?? '') == $room->id ? 'selected' : '' }}>

                {{ $room->room_name }}

            </option>

        @endforeach

    </select>

</div>

<div class="mb-3">

    <label class="form-label">
        Ghi chú
    </label>

    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $consultationRoom->notes ?? '') }}</textarea>

</div>

<div class="mb-3">

    <label class="form-label">
        Trạng thái
    </label>

    <select name="status" class="form-control">

        <option value="1" {{ old('status', $consultationRoom->status ?? 1) == 1 ? 'selected' : '' }}>

            Hoạt động

        </option>

        <option value="0" {{ old('status', $consultationRoom->status ?? '') == 0 ? 'selected' : '' }}>

            Không hoạt động

        </option>

    </select>

</div>

<button type="submit" class="btn btn-success">

    Lưu

</button>

<a href="{{ route('consultation_rooms.index') }}" class="btn btn-secondary">

    Quay lại

</a>


@push('scripts')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        $(document).ready(function () {

            $('#consultationSelect').select2({

                placeholder: 'Tìm phiên khám...',

                ajax: {

                    url: "{{ route('consultations.ajax.search') }}",

                    dataType: 'json',

                    delay: 250,

                    data: function (params) {

                        return {
                            keyword: params.term
                        };

                    },

                    processResults: function (data) {

                        return {

                            results: data.map(function (item) {

                                return {

                                    id: item.id,

                                    text: '#' + item.id + ' - '
                                        + (item.patient?.full_name ?? 'N/A')

                                };

                            })

                        };

                    }

                }

            });

        });

    </script>

@endpush