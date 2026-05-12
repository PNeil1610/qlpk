@extends('layouts.app')

@section('content')

<h3>Sửa phiên khám</h3>

<form method="POST" action="{{ route('consultations.update', $consultation->id) }}">
    
    @csrf
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

    // 🔥 Hiển thị bệnh nhân đã chọn khi edit
    var selected = {
        id: "{{ $consultation->patient_id }}",
        text: "{{ $consultation->patient->patient_code }} - {{ $consultation->patient->full_name }}"
    };

    var option = new Option(selected.text, selected.id, true, true);

    $('#patient_id')
        .append(option)
        .trigger('change');

    // 🔥 Select2 cho Mã Bệnh
    $('#diagnosis_code').select2({
        theme: 'bootstrap-5',
        placeholder: $('#diagnosis_code').data('placeholder'),
        allowClear: true,
        ajax: {
            url: "{{ route('diagnoses.ajax.search') }}",
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
                        id: item.diagnosis_code, // Lưu mã bệnh vào value
                        text: item.diagnosis_code + ' - ' + item.diagnosis_name
                    }))
                };
            }
        }
    });

});
</script>
@endpush