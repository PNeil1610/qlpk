@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h3 class="mb-0 text-primary"><i class="fas fa-receipt me-2"></i>Chi Tiết Toa Thuốc #{{ $prescription->id }}</h3>
        <div>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm me-2">
                <i class="fas fa-print me-1"></i> In Toa
            </button>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Trở về danh sách
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0" id="print-area">
                <div class="card-body p-5">
                    
                    <div class="row border-bottom pb-4 mb-4">
                        <div class="col-sm-6">
                            <h2 class="text-primary fw-bold mb-0">PHÒNG KHÁM ĐA KHOA</h2>
                            <p class="text-muted mb-0">Địa chỉ: 123 Đường ABC, Quận X, TP. Y</p>
                            <p class="text-muted">Hotline: 0123.456.789</p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h4 class="fw-bold text-uppercase text-secondary">Đơn Thuốc</h4>
                            <p class="mb-0"><strong>Số toa:</strong> #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</p>
                            <p class="mb-0"><strong>Ngày kê:</strong> {{ \Carbon\Carbon::parse($prescription->prescribed_at)->format('d/m/Y H:i') }}</p>
                            <p class="mb-0">
                                <strong>Trạng thái:</strong> 
                                @if($prescription->status == 1)
                                    <span class="text-danger fw-bold">Chưa phát thuốc</span>
                                @elseif($prescription->status == 2)
                                    <span class="text-success fw-bold">Đã phát thuốc</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">Thông tin Bệnh nhân</h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="20%" class="text-muted">Họ và tên:</td>
                                    <td class="fw-bold fs-5">{{ $prescription->consultation->patient->full_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mã Bệnh nhân:</td>
                                    <td class="fw-bold">{{ $prescription->consultation->patient->patient_code ?? '---' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Chẩn đoán:</td>
                                    <td class="fw-bold text-danger">{{ $prescription->consultation->symptoms ?? 'Không có chẩn đoán' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">Chỉ định dùng thuốc</h5>
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">STT</th>
                                        <th width="35%">Tên thuốc / Vật tư</th>
                                        <th class="text-center" width="10%">Số lượng</th>
                                        <th width="50%">Cách dùng / Liều dùng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($prescription->details as $index => $detail)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-bold fs-6">
                                                {{ $detail->medicine->medicine_name }}
                                                <div class="fw-normal text-muted small mt-1">Đơn vị: {{ $detail->unit }}</div>
                                            </td>
                                            <td class="text-center fw-bold fs-5 text-primary">{{ $detail->quantity }}</td>
                                            <td>
                                                <div><strong>Liều:</strong> {{ $detail->dosage }}</div>
                                                @if($detail->usage_instructions)
                                                    <div class="text-muted small"><strong>HD:</strong> {{ $detail->usage_instructions }}</div>
                                                @endif
                                                @if($detail->duration_days)
                                                    <div class="text-muted small"><strong>Thời gian:</strong> {{ $detail->duration_days }} ngày</div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-5">
                        <div class="col-sm-7">
                            <h6 class="fw-bold">Lời dặn của Bác sĩ:</h6>
                            <p class="fst-italic text-muted">{!! nl2br(e($prescription->doctor_advice)) ?: 'Không có lời dặn thêm.' !!}</p>
                        </div>
                        <div class="col-sm-5 text-center mt-3 mt-sm-0">
                            <p class="mb-5">Ngày ..... tháng ..... năm 20...</p>
                            <h6 class="fw-bold mb-5">Bác sĩ điều trị</h6>
                            <p class="text-muted">(Ký và ghi rõ họ tên)</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: absolute; left: 0; top: 0; width: 100%; border: none !important; box-shadow: none !important; }
}
</style>
@endsection