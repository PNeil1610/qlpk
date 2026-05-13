@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Chi Tiết Hóa Đơn</h3>
        <div>
            <button onclick="window.print()" class="btn btn-secondary shadow-sm me-2">
                <i class="fas fa-print me-1"></i> In Hóa Đơn
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Danh sách hóa đơn
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0" id="print-area">
                <div class="card-body p-5">

                    {{-- HEADER --}}
                    <div class="row border-bottom pb-4 mb-4">
                        <div class="col-sm-6">
                            <h2 class="text-primary fw-bold mb-0">
                                {{ $clinicSetting->clinic_name ?? 'PHÒNG KHÁM ĐA KHOA' }}
                            </h2>
                            <p class="text-muted mb-0">
                                Địa chỉ: {{ $clinicSetting->address ?? 'Đang cập nhật...' }}
                            </p>
                            <p class="text-muted">
                                Hotline: {{ $clinicSetting->phone_number ?? 'Đang cập nhật...' }}
                            </p>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <h4 class="fw-bold text-uppercase text-secondary">Hóa Đơn</h4>
                            <p class="mb-0">
                                <strong>Mã HĐ:</strong>
                                <span class="text-primary fw-bold">{{ $invoice->invoice_code }}</span>
                            </p>
                            <p class="mb-0">
                                <strong>Ngày lập:</strong>
                                {{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y H:i') : '---' }}
                            </p>
                            <p class="mb-0">
                                <strong>Trạng thái:</strong>
                                @if($invoice->status == 0)
                                    <span class="badge bg-warning text-dark fs-6">Chờ thanh toán</span>
                                @elseif($invoice->status == 1)
                                    <span class="badge bg-success fs-6">Đã thanh toán</span>
                                @elseif($invoice->status == 2)
                                    <span class="badge bg-danger fs-6">Đã hủy</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- THÔNG TIN BỆNH NHÂN --}}
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">
                                Thông tin Bệnh nhân
                            </h5>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="20%" class="text-muted">Họ và tên:</td>
                                    <td class="fw-bold fs-5">
                                        {{ $invoice->consultation->patient->full_name ?? '---' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mã Bệnh nhân:</td>
                                    <td class="fw-bold">
                                        {{ $invoice->consultation->patient->patient_code ?? '---' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Mã phiên khám:</td>
                                    <td class="fw-bold text-primary">
                                        #{{ $invoice->consultation->id ?? '---' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Chẩn đoán:</td>
                                    <td class="fw-bold text-danger">
                                        {{ $invoice->consultation->diagnosis_notes ?? $invoice->consultation->symptoms ?? 'Không có' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- CHI TIẾT HÓA ĐƠN --}}
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-3">
                                Chi tiết hóa đơn
                            </h5>
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">STT</th>
                                        <th width="10%">Loại</th>
                                        <th width="30%">Tên mục</th>
                                        <th class="text-center" width="8%">SL</th>
                                        <th class="text-end" width="17%">Đơn giá</th>
                                        <th class="text-end" width="15%">Giảm giá</th>
                                        <th class="text-end" width="15%">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoice->details as $index => $detail)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                @if($detail->item_type == 'Consultation')
                                                    <span class="badge bg-info">Khám</span>
                                                @elseif($detail->item_type == 'Medicine')
                                                    <span class="badge bg-success">Thuốc</span>
                                                @elseif($detail->item_type == 'LabTest')
                                                    <span class="badge bg-primary">XN</span>
                                                @else
                                                    <span class="badge bg-secondary">Khác</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">
                                                {{ $detail->item_name }}
                                                @if($detail->notes)
                                                    <div class="text-muted small">{{ $detail->notes }}</div>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $detail->quantity }}</td>
                                            <td class="text-end">{{ number_format($detail->unit_price, 0, ',', '.') }} đ</td>
                                            <td class="text-end">
                                                @if($detail->discount > 0)
                                                    {{ number_format($detail->discount, 0, ',', '.') }} đ
                                                @else
                                                    ---
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">{{ number_format($detail->line_total, 0, ',', '.') }} đ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TỔNG HỢP --}}
                    <div class="row mb-4">
                        <div class="col-sm-7"></div>
                        <div class="col-sm-5">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Chi phí khám bệnh:</td>
                                    <td class="text-end fw-bold">{{ number_format($invoice->consultation_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tiền thuốc:</td>
                                    <td class="text-end fw-bold">{{ number_format($invoice->medicine_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @if($invoice->lab_test_fee > 0)
                                <tr>
                                    <td class="text-muted">Xét nghiệm:</td>
                                    <td class="text-end fw-bold">{{ number_format($invoice->lab_test_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($invoice->other_fee > 0)
                                <tr>
                                    <td class="text-muted">Chi phí khác:</td>
                                    <td class="text-end fw-bold">{{ number_format($invoice->other_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($invoice->discount > 0)
                                <tr>
                                    <td class="text-muted">Giảm giá:</td>
                                    <td class="text-end fw-bold text-success">-{{ number_format($invoice->discount, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">TỔNG CỘNG:</td>
                                    <td class="text-end fw-bold fs-4 text-danger">
                                        {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- THÔNG TIN THANH TOÁN --}}
                    @if($invoice->status == 1)
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-3 fs-4"></i>
                                <div>
                                    <strong>Đã thanh toán</strong> vào
                                    {{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i') : '---' }}
                                    | Phương thức: <strong>{{ $invoice->payment_method_text }}</strong>
                                    @if($invoice->cashier)
                                        | Thu ngân: <strong>{{ $invoice->cashier->full_name }}</strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- CHỮ KÝ --}}
                    <div class="row mt-5">
                        <div class="col-sm-6">
                            <p class="text-muted small">
                                <em>Hóa đơn được lập bởi hệ thống quản lý phòng khám.</em>
                            </p>
                        </div>
                        <div class="col-sm-6 text-center">
                            <p class="mb-5">Ngày ..... tháng ..... năm 20...</p>
                            <h6 class="fw-bold mb-5">Thu ngân</h6>
                            <p class="text-muted">(Ký và ghi rõ họ tên)</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- NÚT THANH TOÁN (nếu chưa thanh toán) --}}
            @if($invoice->status == 0)
            <div class="card shadow-sm border-0 mt-4 d-print-none">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-cash-register me-2 text-success"></i>Xác nhận thanh toán
                    </h5>
                    <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST"
                        id="pay-form" class="d-flex align-items-end gap-3">
                        @csrf
                        @method('PUT')

                        <div class="flex-grow-1">
                            <label class="form-label fw-semibold">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">-- Chọn phương thức --</option>
                                <option value="Cash" {{ $invoice->payment_method == 'Cash' ? 'selected' : '' }}>
                                    💵 Tiền mặt
                                </option>
                                <option value="Card" {{ $invoice->payment_method == 'Card' ? 'selected' : '' }}>
                                    💳 Thẻ
                                </option>
                                <option value="Transfer" {{ $invoice->payment_method == 'Transfer' ? 'selected' : '' }}>
                                    🏦 Chuyển khoản
                                </option>
                                <option value="Insurance" {{ $invoice->payment_method == 'Insurance' ? 'selected' : '' }}>
                                    🏥 Bảo hiểm
                                </option>
                                <option value="Other" {{ $invoice->payment_method == 'Other' ? 'selected' : '' }}>
                                    📝 Khác
                                </option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg shadow px-4"
                            onclick="return confirm('Xác nhận thanh toán hóa đơn {{ $invoice->invoice_code }} với số tiền {{ number_format($invoice->total_amount, 0, ',', '.') }} đ?')">
                            <i class="fas fa-check-circle me-2"></i>
                            Thanh toán {{ number_format($invoice->total_amount, 0, ',', '.') }} đ
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        border: none !important;
        box-shadow: none !important;
    }
    .d-print-none { display: none !important; }
}
</style>
@endsection
