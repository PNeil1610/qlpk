@extends('layouts.app')

@section('content')

    <h3>Chi tiết phiên khám</h3>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th width="250">Mã PK</th>
                            <td>{{ $consultation->id }}</td>
                        </tr>
                        <tr>
                            <th>Mã BN</th>
                            <td>{{ $consultation->patient->patient_code }}</td>
                        </tr>
                        <tr>
                            <th>Bệnh nhân</th>
                            <td>{{ $consultation->patient->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Ngày khám</th>
                            <td>{{ $consultation->consultation_date }}</td>
                        </tr>
                        <tr>
                            <th>Lý do khám</th>
                            <td>{{ $consultation->chief_complaint }}</td>
                        </tr>
                        <tr>
                            <th>Triệu chứng</th>
                            <td>{{ $consultation->symptoms }}</td>
                        </tr>
                        <tr>
                            <th>Nhiệt độ</th>
                            <td>{{ $consultation->temperature }}</td>
                        </tr>
                        <tr>
                            <th>Huyết áp</th>
                            <td>{{ $consultation->blood_pressure }}</td>
                        </tr>
                        <tr>
                            <th>Chẩn đoán</th>
                            <td>{{ $consultation->diagnosis_notes }}</td>
                        </tr>
                        <tr>
                            <th>Mã bệnh chuẩn (tham khảo)</th>
                            <td>{{ $consultation->diagnosis_code }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tái khám</th>
                            <td>{{ $consultation->follow_up_date }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái</th>
                            <td>{{ $consultation->status_text }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($consultation->prescriptions->count() > 0)
                <div class="mt-4">
                    <h5 class="fw-bold text-success border-bottom pb-2">
                        <i class="fas fa-prescription-bottle-alt me-2"></i>Toa thuốc trong phiên khám này
                    </h5>
                    @foreach($consultation->prescriptions as $p)
                        <div class="mb-4 bg-white shadow-sm p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="badge bg-success py-2 px-3">Toa thuốc #{{ $p->id }}</span>
                                    <small
                                        class="text-muted ms-2">{{ \Carbon\Carbon::parse($p->prescribed_at)->format('d/m/Y H:i') }}</small>
                                </div>
                                <a href="{{ route('prescriptions.show', $p->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-print me-1"></i>In toa thuốc
                                </a>
                            </div>

                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tên thuốc / Vật tư</th>
                                        <th class="text-center" width="80">SL</th>
                                        <th width="100">Đơn vị</th>
                                        <th>Liều dùng</th>
                                        <th>Cách dùng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($p->details as $d)
                                        <tr>
                                            <td class="fw-bold">{{ $d->medicine->medicine_name }}</td>
                                            <td class="text-center fw-bold text-primary">{{ $d->quantity }}</td>
                                            <td>{{ $d->unit }}</td>
                                            <td>{{ $d->dosage }}</td>
                                            <td>{{ $d->usage_instructions }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($p->doctor_advice)
                                <div class="bg-light p-2 small border-start border-4 border-info">
                                    <strong>Lời dặn của bác sĩ:</strong> {!! nl2br(e($p->doctor_advice)) !!}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-footer bg-white py-3">
            @if(request()->has('reuse_mode') && $consultation->prescriptions->count() > 0)
                <button type="button" class="btn btn-warning shadow-sm me-2 fw-bold text-dark px-4" id="btn-reuse">
                    <i class="fas fa-copy me-1"></i> Sử dụng lại cho phiên hiện tại
                </button>
            @endif

            <a href="{{ route('consultations.doctor') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    @if(request()->has('reuse_mode'))
        @php
            $medicinesArr = [];
            foreach ($consultation->prescriptions as $pres) {
                foreach ($pres->details as $det) {
                    $medicinesArr[] = [
                        'id' => $det->medicine_id,
                        'name' => $det->medicine->medicine_name,
                        'quantity' => $det->quantity,
                        'unit' => $det->unit,
                        'dosage' => $det->dosage,
                        'usage_instructions' => $det->usage_instructions,
                        'duration_days' => $det->duration_days,
                    ];
                }
            }
            $adviceData = $consultation->prescriptions->first()->doctor_advice ?? '';
        @endphp
        <script>
            document.getElementById('btn-reuse')?.addEventListener('click', function () {
                const medicines = {!! json_encode($medicinesArr) !!};
                const advice = {!! json_encode($adviceData) !!};

                if (window.opener && !window.opener.closed) {
                    if (confirm('Bạn có muốn sao chép toàn bộ danh sách thuốc này vào phiên khám hiện tại không?')) {
                        window.opener.postMessage({
                            type: 'REUSE_PRESCRIPTION',
                            medicines: medicines,
                            advice: advice
                        }, '*');
                        window.close();
                    }
                } else {
                    // Trường hợp mở cùng một tab (không có opener)
                    if (confirm('Hệ thống sẽ chuyển bạn về trang khám bệnh và điền dữ liệu thuốc này. Bạn đồng ý chứ?')) {
                        // Lưu vào localStorage
                        localStorage.setItem('reuse_prescription_data', JSON.stringify({
                            medicines: medicines,
                            advice: advice,
                            timestamp: Date.now()
                        }));

                        // Quay lại trang trước (trang khám bệnh)
                        if (document.referrer && document.referrer.includes('/examine')) {
                            window.location.href = document.referrer;
                        } else {
                            window.history.back();
                        }
                    }
                }
            });
        </script>
    @endif

@endsection