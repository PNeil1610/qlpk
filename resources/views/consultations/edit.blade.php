@extends('layouts.app')

@section('content')

    <h3>Sửa phiên khám</h3>

    <form method="POST" action="{{ route('consultations.update', $consultation->id) }}">

        @csrf
        @method('PUT')

        @include('consultations.form')

    </form>

    <style>
        /* Thêm viền xanh và bóng đổ (border-success shadow-sm) cho ô Select2 Mã bệnh */
        #diagnosis_code+.select2-container .select2-selection {
            border: 1px solid #198754 !important;
            /* Màu success của Bootstrap */
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        }

        /* Giới hạn chiều cao dropdown và thêm thanh cuộn */
        .select2-container .select2-results__options {
            max-height: 250px !important;
            overflow-y: auto !important;

            /* ... (CSS hiện tại của bạn giữ nguyên) ... */

            /* 1. Đảm bảo khung select có chiều cao linh hoạt và dùng flexbox để căn giữa nội dung */
            .select2-container .select2-selection--single {
                height: auto !important;
                min-height: 38px;
                /* Tương đương form-control của Bootstrap 5 */
                display: flex !important;
                align-items: center;
                position: relative;
            }

            /* 2. Ép nội dung chữ nằm gọn trong khung, không rớt dòng */
            .select2-container .select2-selection--single .select2-selection__rendered {
                width: 100%;
                padding-left: 0.75rem;
                /* Padding trái giống input thường */
                padding-right: 2.5rem !important;
                /* Chừa khoảng trống bên phải cho nút X và mũi tên */
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.5;
            }

            /* 3. Cố định vị trí nút X (clear) sang góc bên phải */
            .select2-container .select2-selection--single .select2-selection__clear {
                position: absolute !important;
                right: 30px;
                /* Đặt cạnh nút mũi tên xổ xuống */
                top: 50%;
                transform: translateY(-50%);
                margin: 0;
                z-index: 10;
                float: none;
                /* Xóa float mặc định gây lỗi layout */
            }
        }
    </style>

@endsection


@push('scripts')
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
                width: '100%', // Đảm bảo width không bị co giãn gây giật
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
                                text: item.diagnosis_code + ' - ' + item.diagnosis_name,
                                code: item.diagnosis_code,
                                name: item.diagnosis_name
                            }))
                        };
                    }
                },
                templateResult: function (item) {
                    if (item.loading) return item.text;

                    // Xử lý cho option khởi tạo từ Blade (chỉ có id và text, không có code và name)
                    let code = item.code || item.id;
                    let name = item.name;
                    if (!name && item.text && item.text.includes(' - ')) {
                        name = item.text.split(' - ').slice(1).join(' - ');
                    }

                    // Nếu không có thông tin (vd: placeholder), hiển thị text mặc định
                    if (!code) return item.text;

                    // Giao diện list dropdown dễ nhìn hơn
                    return $(`
                    <div class="d-flex align-items-center border-bottom pb-2 mb-1">
                        <span class="badge bg-success me-3" style="min-width: 60px; font-size: 0.9em;">${code}</span>
                        <span class="text-dark fw-medium">${name || item.text}</span>
                    </div>
                `);
                }
            });

        });
    </script>
@endpush