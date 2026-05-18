@extends('layouts.app')

@section('title', 'Chi tiết hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Chi Tiết Hóa Đơn</h3>
        <div class="d-flex gap-2">
            <button onclick="printInvoice()" class="btn btn-secondary shadow-sm">
                <i class="fas fa-print me-1"></i> In Hóa Đơn
            </button>
            <button onclick="printBanKe()" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-file-alt me-1"></i> In Bản Kê
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

                    {{-- CHI PHÍ THEO LOẠI HÌNH --}}
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark border-start border-primary border-4 ps-2 mb-0">
                                    Chi phí theo loại hình
                                </h5>
                                <button type="button" class="btn btn-outline-primary btn-sm d-print-none"
                                    data-bs-toggle="modal" data-bs-target="#modalBanKe">
                                    <i class="fas fa-list-alt me-1"></i> Xem bản kê chi tiết
                                </button>
                            </div>

                            <div class="row g-3 mb-4">
                                {{-- Chi phí khám bệnh --}}
                                <div class="col-sm-6 col-lg-3">
                                    <div class="border rounded-3 p-3 h-100" style="border-color: #0dcaf0 !important; background: #f0fbff;">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-info me-2">Khám</span>
                                            <small class="text-muted">Chi phí khám bệnh</small>
                                        </div>
                                        <div class="fs-5 fw-bold text-info">
                                            {{ number_format($invoice->consultation_fee, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </div>

                                {{-- Tiền thuốc --}}
                                <div class="col-sm-6 col-lg-3">
                                    <div class="border rounded-3 p-3 h-100" style="border-color: #198754 !important; background: #f0fff4;">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-success me-2">Thuốc</span>
                                            <small class="text-muted">Tiền thuốc</small>
                                        </div>
                                        <div class="fs-5 fw-bold text-success">
                                            {{ number_format($invoice->medicine_fee, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </div>

                                @if($invoice->lab_test_fee > 0)
                                {{-- Xét nghiệm --}}
                                <div class="col-sm-6 col-lg-3">
                                    <div class="border rounded-3 p-3 h-100" style="border-color: #0d6efd !important; background: #f0f4ff;">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary me-2">XN</span>
                                            <small class="text-muted">Xét nghiệm</small>
                                        </div>
                                        <div class="fs-5 fw-bold text-primary">
                                            {{ number_format($invoice->lab_test_fee, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($invoice->other_fee > 0)
                                {{-- Chi phí khác --}}
                                <div class="col-sm-6 col-lg-3">
                                    <div class="border rounded-3 p-3 h-100" style="border-color: #6c757d !important; background: #f8f9fa;">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-secondary me-2">Khác</span>
                                            <small class="text-muted">Chi phí khác</small>
                                        </div>
                                        <div class="fs-5 fw-bold text-secondary">
                                            {{ number_format($invoice->other_fee, 0, ',', '.') }} đ
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            {{-- Bảng tổng hợp --}}
                            <div class="row">
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

{{-- MODAL: Bản kê chi tiết --}}
<div class="modal fade" id="modalBanKe" tabindex="-1" aria-labelledby="modalBanKeLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalBanKeLabel">
                    <i class="fas fa-list-alt me-2"></i>Bản kê chi tiết – {{ $invoice->invoice_code }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                {{-- Nhóm: Chi phí khám bệnh --}}
                @php $consultItems = $invoice->details->where('item_type', 'Consultation'); @endphp
                @if($consultItems->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span class="badge bg-info">Khám</span> Chi phí khám bệnh
                    </h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th>Nội dung</th>
                                <th class="text-center" width="10%">SL</th>
                                <th class="text-end" width="20%">Đơn giá</th>
                                <th class="text-end" width="20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultItems as $i => $d)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $d->item_name }}</td>
                                <td class="text-center">{{ $d->quantity }}</td>
                                <td class="text-end">{{ number_format($d->unit_price, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-bold">{{ number_format($d->line_total, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng chi phí khám:</td>
                                <td class="text-end fw-bold text-info">{{ number_format($invoice->consultation_fee, 0, ',', '.') }} đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Nhóm: Thuốc --}}
                @php $medItems = $invoice->details->where('item_type', 'Medicine'); @endphp
                @if($medItems->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span class="badge bg-success">Thuốc</span> Danh sách thuốc
                    </h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th>Tên thuốc</th>
                                <th class="text-center" width="10%">SL</th>
                                <th class="text-end" width="18%">Đơn giá</th>
                                <th class="text-end" width="18%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medItems as $i => $d)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $d->item_name }}</span>
                                    @if($d->notes)
                                        <div class="text-muted small"><i class="fas fa-prescription me-1"></i>{{ $d->notes }}</div>
                                    @endif
                                </td>
                                <td class="text-center">{{ $d->quantity }}</td>
                                <td class="text-end">{{ number_format($d->unit_price, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-bold">{{ number_format($d->line_total, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng tiền thuốc:</td>
                                <td class="text-end fw-bold text-success">{{ number_format($invoice->medicine_fee, 0, ',', '.') }} đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Nhóm: Xét nghiệm --}}
                @php $labItems = $invoice->details->where('item_type', 'LabTest'); @endphp
                @if($labItems->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span class="badge bg-primary">XN</span> Xét nghiệm
                    </h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th>Tên xét nghiệm</th>
                                <th class="text-center" width="10%">SL</th>
                                <th class="text-end" width="20%">Đơn giá</th>
                                <th class="text-end" width="20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($labItems as $i => $d)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $d->item_name }}</td>
                                <td class="text-center">{{ $d->quantity }}</td>
                                <td class="text-end">{{ number_format($d->unit_price, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-bold">{{ number_format($d->line_total, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng xét nghiệm:</td>
                                <td class="text-end fw-bold text-primary">{{ number_format($invoice->lab_test_fee, 0, ',', '.') }} đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Nhóm: Chi phí khác --}}
                @php $otherItems = $invoice->details->where('item_type', 'Other'); @endphp
                @if($otherItems->count() > 0)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">Khác</span> Chi phí khác
                    </h6>
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">STT</th>
                                <th>Nội dung</th>
                                <th class="text-center" width="10%">SL</th>
                                <th class="text-end" width="20%">Đơn giá</th>
                                <th class="text-end" width="20%">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otherItems as $i => $d)
                            <tr>
                                <td class="text-center">{{ $i + 1 }}</td>
                                <td>{{ $d->item_name }}</td>
                                <td class="text-center">{{ $d->quantity }}</td>
                                <td class="text-end">{{ number_format($d->unit_price, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-bold">{{ number_format($d->line_total, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng chi phí khác:</td>
                                <td class="text-end fw-bold text-secondary">{{ number_format($invoice->other_fee, 0, ',', '.') }} đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Tổng kết --}}
                <div class="border-top pt-3">
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Chi phí khám bệnh:</td>
                                    <td class="text-end fw-bold ps-4">{{ number_format($invoice->consultation_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tiền thuốc:</td>
                                    <td class="text-end fw-bold ps-4">{{ number_format($invoice->medicine_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @if($invoice->lab_test_fee > 0)
                                <tr>
                                    <td class="text-muted">Xét nghiệm:</td>
                                    <td class="text-end fw-bold ps-4">{{ number_format($invoice->lab_test_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($invoice->other_fee > 0)
                                <tr>
                                    <td class="text-muted">Chi phí khác:</td>
                                    <td class="text-end fw-bold ps-4">{{ number_format($invoice->other_fee, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                @if($invoice->discount > 0)
                                <tr>
                                    <td class="text-muted">Giảm giá:</td>
                                    <td class="text-end fw-bold text-success ps-4">-{{ number_format($invoice->discount, 0, ',', '.') }} đ</td>
                                </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">TỔNG CỘNG:</td>
                                    <td class="text-end fw-bold fs-4 text-danger ps-4">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- VÙNG IN BẢN KÊ (ẩn trên màn hình, chỉ hiện khi print) --}}
<div id="print-banske" style="display:none;">
    <div style="font-family: Arial, sans-serif; padding: 20px;">
        {{-- Header --}}
        <div style="text-align:center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 16px;">
            <h2 style="margin:0; font-size:18px;">{{ $clinicSetting->clinic_name ?? 'PHÒNG KHÁM ĐA KHOA' }}</h2>
            <p style="margin:2px 0; font-size:12px;">{{ $clinicSetting->address ?? '' }} | ĐT: {{ $clinicSetting->phone_number ?? '' }}</p>
            <h3 style="margin:8px 0 2px; font-size:16px; text-transform:uppercase;">Bản Kê Chi Tiết</h3>
            <p style="margin:0; font-size:12px;">
                Mã HĐ: <strong>{{ $invoice->invoice_code }}</strong> &nbsp;|&nbsp;
                Ngày: <strong>{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y H:i') : '---' }}</strong>
            </p>
        </div>

        {{-- Thông tin bệnh nhân --}}
        <table style="width:100%; font-size:13px; margin-bottom:14px;">
            <tr>
                <td style="width:50%;"><strong>Bệnh nhân:</strong> {{ $invoice->consultation->patient->full_name ?? '---' }}</td>
                <td><strong>Mã BN:</strong> {{ $invoice->consultation->patient->patient_code ?? '---' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Chẩn đoán:</strong> {{ $invoice->consultation->diagnosis_notes ?? $invoice->consultation->symptoms ?? 'Không có' }}</td>
            </tr>
        </table>

        {{-- Nhóm: Chi phí khám --}}
        @php $consultItems = $invoice->details->where('item_type', 'Consultation'); @endphp
        @if($consultItems->count() > 0)
        <p style="font-weight:bold; margin:10px 0 4px; font-size:13px;">I. CHI PHÍ KHÁM BỆNH</p>
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:5%;">STT</th>
                    <th style="border:1px solid #ccc; padding:5px;">Nội dung</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:8%;">SL</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Đơn giá</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consultItems as $i => $d)
                <tr>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $i+1 }}</td>
                    <td style="border:1px solid #ccc; padding:5px;">{{ $d->item_name }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $d->quantity }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right;">{{ number_format($d->unit_price,0,',','.') }} đ</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($d->line_total,0,',','.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9f9f9;">
                    <td colspan="4" style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">Tổng:</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($invoice->consultation_fee,0,',','.') }} đ</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Nhóm: Thuốc --}}
        @php $medItems = $invoice->details->where('item_type', 'Medicine'); @endphp
        @if($medItems->count() > 0)
        <p style="font-weight:bold; margin:10px 0 4px; font-size:13px;">II. DANH SÁCH THUỐC</p>
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:5%;">STT</th>
                    <th style="border:1px solid #ccc; padding:5px;">Tên thuốc</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:8%;">SL</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Đơn giá</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medItems as $i => $d)
                <tr>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $i+1 }}</td>
                    <td style="border:1px solid #ccc; padding:5px;">
                        {{ $d->item_name }}
                        @if($d->notes)<br><span style="color:#555; font-size:11px;">Liều: {{ $d->notes }}</span>@endif
                    </td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $d->quantity }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right;">{{ number_format($d->unit_price,0,',','.') }} đ</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($d->line_total,0,',','.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9f9f9;">
                    <td colspan="4" style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">Tổng tiền thuốc:</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($invoice->medicine_fee,0,',','.') }} đ</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Nhóm: Xét nghiệm --}}
        @php $labItems = $invoice->details->where('item_type', 'LabTest'); @endphp
        @if($labItems->count() > 0)
        <p style="font-weight:bold; margin:10px 0 4px; font-size:13px;">III. XÉT NGHIỆM</p>
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:5%;">STT</th>
                    <th style="border:1px solid #ccc; padding:5px;">Tên xét nghiệm</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:8%;">SL</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Đơn giá</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($labItems as $i => $d)
                <tr>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $i+1 }}</td>
                    <td style="border:1px solid #ccc; padding:5px;">{{ $d->item_name }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $d->quantity }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right;">{{ number_format($d->unit_price,0,',','.') }} đ</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($d->line_total,0,',','.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9f9f9;">
                    <td colspan="4" style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">Tổng xét nghiệm:</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($invoice->lab_test_fee,0,',','.') }} đ</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Nhóm: Chi phí khác --}}
        @php $otherItems = $invoice->details->where('item_type', 'Other'); @endphp
        @if($otherItems->count() > 0)
        <p style="font-weight:bold; margin:10px 0 4px; font-size:13px;">IV. CHI PHÍ KHÁC</p>
        <table style="width:100%; border-collapse:collapse; font-size:12px; margin-bottom:10px;">
            <thead>
                <tr style="background:#f0f0f0;">
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:5%;">STT</th>
                    <th style="border:1px solid #ccc; padding:5px;">Nội dung</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:center; width:8%;">SL</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Đơn giá</th>
                    <th style="border:1px solid #ccc; padding:5px; text-align:right; width:18%;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($otherItems as $i => $d)
                <tr>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $i+1 }}</td>
                    <td style="border:1px solid #ccc; padding:5px;">{{ $d->item_name }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:center;">{{ $d->quantity }}</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right;">{{ number_format($d->unit_price,0,',','.') }} đ</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($d->line_total,0,',','.') }} đ</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f9f9f9;">
                    <td colspan="4" style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">Tổng chi phí khác:</td>
                    <td style="border:1px solid #ccc; padding:5px; text-align:right; font-weight:bold;">{{ number_format($invoice->other_fee,0,',','.') }} đ</td>
                </tr>
            </tfoot>
        </table>
        @endif

        {{-- Tổng kết cuối --}}
        <table style="width:100%; border-collapse:collapse; font-size:13px; margin-top:12px;">
            <tr style="background:#e8f4e8;">
                <td style="border:2px solid #333; padding:8px; font-weight:bold; font-size:15px;">TỔNG CỘNG PHẢI THANH TOÁN:</td>
                <td style="border:2px solid #333; padding:8px; text-align:right; font-weight:bold; font-size:16px; color:#c00;">{{ number_format($invoice->total_amount,0,',','.') }} đ</td>
            </tr>
        </table>

        {{-- Chữ ký --}}
        <div style="display:flex; justify-content:flex-end; margin-top:30px; font-size:12px;">
            <div style="text-align:center; min-width:180px;">
                <p style="margin:0;">Ngày ..... tháng ..... năm 20...</p>
                <p style="font-weight:bold; margin:8px 0 40px;">Thu ngân</p>
                <p style="margin:0;">(Ký và ghi rõ họ tên)</p>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body.print-invoice * { visibility: hidden; }
    body.print-invoice #print-area,
    body.print-invoice #print-area * { visibility: visible; }
    body.print-invoice #print-area {
        position: absolute;
        left: 0; top: 0;
        width: 100%;
        border: none !important;
        box-shadow: none !important;
    }

    body.print-banske * { visibility: hidden; }
    body.print-banske #print-banske,
    body.print-banske #print-banske * { visibility: visible; }
    body.print-banske #print-banske {
        display: block !important;
        position: absolute;
        left: 0; top: 0;
        width: 100%;
    }

    .d-print-none { display: none !important; }
}
</style>

<script>
function printInvoice() {
    document.body.classList.remove('print-banske');
    document.body.classList.add('print-invoice');
    window.print();
    setTimeout(function() {
        document.body.classList.remove('print-invoice');
    }, 1000);
}
function printBanKe() {
    document.body.classList.remove('print-invoice');
    document.body.classList.add('print-banske');
    window.print();
    setTimeout(function() {
        document.body.classList.remove('print-banske');
    }, 1000);
}
</script>
@endsection
