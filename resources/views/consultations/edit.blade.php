@extends('layouts.app')

@section('content')

<h3>Sửa phiên khám</h3>

<form method="POST" action="{{ route('consultations.update', $consultation->id) }}">
    @method('PUT')

    @include('consultations.form')
</form>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    $('#patient_id').select2({
        placeholder: "Tìm bệnh nhân...",
        allowClear: true,
        ajax: {
            url: "/patients/search",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.patient_code + ' - ' + item.full_name
                    }))
                };
            }
        }
    });

    // 🔥 FIX EDIT
    @if(isset($consultation))
        var selected = {
            id: "{{ $consultation->patient_id }}",
            text: "{{ $consultation->patient->patient_code }} - {{ $consultation->patient->full_name }}"
        };

        var option = new Option(selected.text, selected.id, true, true);
        $('#patient_id').append(option).trigger('change');
    @endif

});
</script>
@endpush