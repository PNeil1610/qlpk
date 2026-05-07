@extends('layouts.app')

@section('content')

    <h3>Tạo phiên khám</h3>

    <form method="POST" action="{{ route('consultations.store') }}">
        @csrf

        @include('consultations.form')

    </form>

@endsection


@push('scripts')
    @if(!isset($selectedPatient))

        <script>
            $(document).ready(function () {

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

            });
        </script>

    @endif
@endpush