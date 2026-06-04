@extends('layouts.app')
@section('title', 'Chi tiết hóa đơn')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-3 d-print-none px-3">
    <h5 class="mb-0 text-primary fw-bold">
        <i class="fas fa-file-invoice-dollar me-2"></i>Hóa Đơn {{ $invoice->invoice_code }}
    </h5>
    <div class="d-flex gap-2">
        <button onclick="printInvoice()" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-print me-1"></i> In Hóa Đơn (A5)
        </button>
        <button onclick="printBanKe()" class="btn btn-outline-primary btn-sm shadow-sm">
            <i class="fas fa-file-alt me-1"></i> In Bản Kê
        </button>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Danh sách
        </a>
    </div>
</div>

{{-- Layout 2 cột: Hóa đơn | Bản kê --}}
<div class="hd-layout">

    {{-- ===== CỘT TRÁI: HÓA ĐƠN A5 ===== --}}
    <div class="hd-col-invoice">
        <div class="inv-page" id="print-invoice">

            {{-- HEADER --}}
            <div class="inv-header">
                <div class="inv-header-left">
                    {{-- Logo phòng khám hoặc icon fallback --}}
                    @if($clinicSetting?->logo_data)
                        <img src="{{ $clinicSetting->logo_data }}"
                             alt="{{ $clinicSetting->clinic_name }}"
                             class="inv-clinic-logo-img">
                    @else
                        <div class="inv-clinic-logo"><i class="fas fa-hospital-alt"></i></div>
                    @endif
                    <div>
                        <div class="inv-clinic-name">{{ $clinicSetting->clinic_name ?? 'PHÒNG KHÁM ĐA KHOA' }}</div>
                        @if($clinicSetting?->address)<div class="inv-clinic-sub">{{ $clinicSetting->address }}</div>@endif
                        @if($clinicSetting?->phone_number)<div class="inv-clinic-sub">ĐT: {{ $clinicSetting->phone_number }}</div>@endif
                        @if($clinicSetting?->email)<div class="inv-clinic-sub">Email: {{ $clinicSetting->email }}</div>@endif
                    </div>
                </div>
                <div class="inv-header-right">
                    <div class="inv-doc-row"><span class="inv-lbl">Mã HĐ:</span><span class="inv-val fw-bold text-primary">{{ $invoice->invoice_code }}</span></div>
                    <div class="inv-doc-row"><span class="inv-lbl">Ngày:</span><span class="inv-val">{{ $invoice->invoice_date?->format('d/m/Y') ?? '---' }}</span></div>
                </div>
            </div>

            {{-- TIÊU ĐỀ + BARCODE --}}
            <div class="inv-title-block">
                <div class="inv-title">HÓA ĐƠN THANH TOÁN</div>
                <div class="inv-barcode-wrap">
                    <svg id="inv-barcode"></svg>
                </div>
            </div>

            {{-- THÔNG TIN BỆNH NHÂN --}}
            @php
                $patient = $invoice->consultation->patient;
                $genderText = match($patient->gender??'') { 'Male'=>'Nam','Female'=>'Nữ',default=>'Khác' };
            @endphp
            <div class="inv-patient-section">
                <div class="inv-patient-row">
                    <span class="inv-field-label">Họ và tên:</span>
                    <span class="inv-patient-name">{{ $patient->full_name ?? '---' }}</span>
                    <span class="inv-field-label ms-auto">Giới tính:</span>
                    <span class="inv-field-value">{{ $genderText }}</span>
                    @if($patient->age)<span class="inv-field-label ms-2">Tuổi:</span><span class="inv-field-value">{{ $patient->age }}</span>@endif
                </div>
                <div class="inv-patient-row">
                    <span class="inv-field-label">Mã BN:</span>
                    <span class="inv-field-value">{{ $patient->patient_code ?? '---' }}</span>
                    @if($patient->phone_number)
                        <span class="inv-field-label ms-auto">SĐT:</span>
                        <span class="inv-field-value">{{ $patient->phone_number }}</span>
                    @endif
                </div>
                <div class="inv-patient-row">
                    <span class="inv-field-label">Chẩn đoán:</span>
                    <span class="inv-field-value fw-bold text-danger">{{ $invoice->consultation->diagnosis_notes ?? $invoice->consultation->symptoms ?? 'Không có' }}</span>
                </div>
                @if($invoice->status == 1)
                <div class="inv-patient-row">
                    <span class="inv-field-label">Thanh toán:</span>
                    <span class="inv-field-value">{{ $invoice->paid_at?->format('d/m/Y H:i') ?? '---' }}</span>
                    <span class="inv-field-label ms-auto">Hình thức:</span>
                    <span class="inv-field-value fw-bold">{{ $invoice->payment_method_text }}</span>
                </div>
                @endif
            </div>

            <div class="inv-divider"></div>

            {{-- BẢNG TỔNG HỢP (chỉ tổng theo nhóm, không chi tiết) --}}
            <table class="inv-summary-table">
                <thead>
                    <tr>
                        <th>Loại dịch vụ</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Chi phí khám bệnh</td>
                        <td class="text-end fw-bold">{{ number_format($invoice->consultation_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @if($invoice->medicine_fee > 0)
                    <tr>
                        <td>Tiền thuốc</td>
                        <td class="text-end fw-bold">{{ number_format($invoice->medicine_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @endif
                    @if($invoice->lab_test_fee > 0)
                    <tr>
                        <td>Xét nghiệm</td>
                        <td class="text-end fw-bold">{{ number_format($invoice->lab_test_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @endif
                    @if($invoice->other_fee > 0)
                    <tr>
                        <td>Chi phí khác</td>
                        <td class="text-end fw-bold">{{ number_format($invoice->other_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    @endif
                    @if($invoice->discount > 0)
                    <tr>
                        <td class="text-muted fst-italic">Giảm giá</td>
                        <td class="text-end text-success fw-bold">-{{ number_format($invoice->discount, 0, ',', '.') }}đ</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="inv-total-row">
                        <td class="fw-bold">TỔNG CỘNG</td>
                        <td class="text-end fw-bold" style="font-size:13pt">{{ number_format($invoice->total_amount, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            </table>

            <div class="inv-divider"></div>

            {{-- CHỮ KÝ --}}
            <div class="inv-sign-row">
                <div class="inv-sign-col-right">
                    @php $signDate = $invoice->paid_at ?? $invoice->invoice_date ?? now(); @endphp
                    <div class="inv-sign-date">Ngày {{ $signDate->format('d') }} tháng {{ $signDate->format('m') }} năm {{ $signDate->format('Y') }}</div>
                    <div class="inv-sign-title">Thu ngân</div>
                    <div class="inv-sign-space"></div>
                    <div class="inv-sign-name">{{ $invoice->cashier?->full_name ?? '---' }}</div>
                </div>
            </div>

        </div>
    </div>{{-- end col-invoice --}}

    {{-- ===== CỘT PHẢI: BẢN KÊ CHI TIẾT ===== --}}
    <div class="hd-col-banske">
        <div class="bk-page" id="print-banske">
            <div class="bk-header">
                {{-- Logo bản kê --}}
                @if($clinicSetting?->logo_data)
                <div style="margin-bottom:6px;">
                    <img src="{{ $clinicSetting->logo_data }}"
                         alt="{{ $clinicSetting->clinic_name }}"
                         class="bk-clinic-logo-img">
                </div>
                @endif
                <div class="bk-clinic-name">{{ $clinicSetting->clinic_name ?? 'PHÒNG KHÁM ĐA KHOA' }}</div>
                @if($clinicSetting)<div class="bk-clinic-sub">{{ $clinicSetting->address }} | ĐT: {{ $clinicSetting->phone_number }}</div>@endif
                <div class="bk-title">BẢN KÊ CHI TIẾT DỊCH VỤ</div>
                <div class="bk-meta">Mã HĐ: <strong>{{ $invoice->invoice_code }}</strong> &nbsp;|&nbsp; Ngày: <strong>{{ $invoice->invoice_date?->format('d/m/Y H:i') ?? '---' }}</strong></div>
            </div>

            <table class="bk-info">
                <tr>
                    <td><strong>Bệnh nhân:</strong> {{ $patient->full_name ?? '---' }}</td>
                    <td><strong>Mã BN:</strong> {{ $patient->patient_code ?? '---' }}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Chẩn đoán:</strong> {{ $invoice->consultation->diagnosis_notes ?? $invoice->consultation->symptoms ?? 'Không có' }}</td>
                </tr>
            </table>

            @php
                $consultItems = $invoice->details->where('item_type','Consultation')->where('status', 1);
                $medItems     = $invoice->details->where('item_type','Medicine')->where('status', 1);
                $labItems     = $invoice->details->where('item_type','LabTest')->where('status', 1);
                $otherItems   = $invoice->details->where('item_type','Other')->where('status', 1);
                $sections = [];
                if($consultItems->count()) $sections[] = ['label'=>'I','title'=>'CHI PHÍ KHÁM BỆNH','items'=>$consultItems,'total'=>$invoice->consultation_fee];
                if($medItems->count())     $sections[] = ['label'=>'II','title'=>'DANH SÁCH THUỐC','items'=>$medItems,'total'=>$invoice->medicine_fee];
                if($labItems->count())     $sections[] = ['label'=>'III','title'=>'XÉT NGHIỆM','items'=>$labItems,'total'=>$invoice->lab_test_fee];
                if($otherItems->count())   $sections[] = ['label'=>'IV','title'=>'CHI PHÍ KHÁC','items'=>$otherItems,'total'=>$invoice->other_fee];
            @endphp

            @foreach($sections as $sec)
            <p class="bk-section-title">{{ $sec['label'] }}. {{ $sec['title'] }}</p>
            <table class="bk-table">
                <thead>
                    <tr>
                        <th style="width:5%;text-align:center">STT</th>
                        <th>Nội dung</th>
                        <th style="width:8%;text-align:center">SL</th>
                        <th style="width:20%;text-align:right">Đơn giá</th>
                        <th style="width:20%;text-align:right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sec['items'] as $i => $d)
                    <tr>
                        <td style="text-align:center">{{ $i+1 }}</td>
                        <td>{{ $d->item_name }}
                            @if($d->notes)<br><span style="color:#555;font-size:10px;font-style:italic">{{ $d->notes }}</span>@endif
                        </td>
                        <td style="text-align:center">{{ $d->quantity }}</td>
                        <td style="text-align:right">{{ number_format($d->unit_price,0,',','.') }}đ</td>
                        <td style="text-align:right;font-weight:bold">{{ number_format($d->line_total,0,',','.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;font-weight:bold">Tổng:</td>
                        <td style="text-align:right;font-weight:bold">{{ number_format($sec['total'],0,',','.') }}đ</td>
                    </tr>
                </tfoot>
            </table>
            @endforeach

            <table class="bk-total">
                @if($invoice->discount > 0)
                <tr><td>Giảm giá:</td><td style="text-align:right;color:green">-{{ number_format($invoice->discount,0,',','.') }}đ</td></tr>
                @endif
                <tr><td class="fw-bold" style="font-size:13px">TỔNG CỘNG PHẢI THANH TOÁN:</td><td style="text-align:right;font-weight:bold;font-size:14px;color:#000">{{ number_format($invoice->total_amount,0,',','.') }}đ</td></tr>
            </table>

            <div class="bk-sign-row">
                <div style="flex:1"></div>
                <div class="bk-sign-box">
                    <p style="margin:0;font-size:11px">Ngày {{ $signDate->format('d') }} tháng {{ $signDate->format('m') }} năm {{ $signDate->format('Y') }}</p>
                    <p style="font-weight:bold;margin:6px 0 36px;font-size:12px">Thu ngân</p>
                    <p style="margin:0;font-weight:bold;font-size:12px">{{ $invoice->cashier?->full_name ?? '(Ký và ghi rõ họ tên)' }}</p>
                </div>
            </div>
        </div>
    </div>{{-- end col-banske --}}

</div>{{-- end hd-layout --}}

{{-- NÚT THANH TOÁN --}}
@if($invoice->status == 0)
<div class="card shadow-sm border-0 mt-4 d-print-none">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-3"><i class="fas fa-cash-register me-2 text-success"></i>Xác nhận thanh toán</h5>
        <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="d-flex align-items-end gap-3">
            @csrf @method('PUT')
            <div class="flex-grow-1">
                <label class="form-label fw-semibold">Phương thức thanh toán</label>
                <select name="payment_method" class="form-select" required>
                    <option value="">-- Chọn phương thức --</option>
                    <option value="Cash" {{ $invoice->payment_method=='Cash'?'selected':'' }}>💵 Tiền mặt</option>
                    <option value="Card" {{ $invoice->payment_method=='Card'?'selected':'' }}>💳 Thẻ</option>
                    <option value="Transfer" {{ $invoice->payment_method=='Transfer'?'selected':'' }}>🏦 Chuyển khoản</option>
                    <option value="Insurance" {{ $invoice->payment_method=='Insurance'?'selected':'' }}>🏥 Bảo hiểm</option>
                    <option value="Other" {{ $invoice->payment_method=='Other'?'selected':'' }}>📝 Khác</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-lg shadow px-4"
                onclick="return confirm('Xác nhận thanh toán {{ $invoice->invoice_code }}?')">
                <i class="fas fa-check-circle me-2"></i>Thanh toán {{ number_format($invoice->total_amount,0,',','.') }}đ
            </button>
        </form>
    </div>
</div>
@endif

<style>
/* ===== LAYOUT 2 CỘT ===== */
.hd-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    padding: 0 12px 40px;
}
.hd-col-invoice {
    flex: 0 0 auto;
    width: 148mm;
}
.hd-col-banske {
    flex: 1;
    min-width: 0;
}

/* ===== HÓA ĐƠN ===== */
.inv-page {
    background: #fff;
    width: 148mm;
    min-height: 210mm;
    padding: 8mm 10mm;
    border-radius: 4px;
    box-shadow: 0 4px 24px rgba(0,0,0,.13);
    font-family: 'Times New Roman', Times, serif;
    font-size: 11pt;
    line-height: 1.5;
    color: #111;
}
.inv-header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:1.5px solid #222; padding-bottom:4mm; margin-bottom:3mm; }
.inv-header-left { display:flex; align-items:flex-start; gap:8px; flex:1; }
.inv-clinic-logo { font-size:26px; color:#1a56a0; margin-top:2px; flex-shrink:0; }
.inv-clinic-logo-img { height:42px; max-width:90px; object-fit:contain; flex-shrink:0; margin-top:2px; }
.bk-clinic-logo-img { height:36px; max-width:100px; object-fit:contain; }
.inv-clinic-name { font-size:11.5pt; font-weight:700; color:#1a3a6b; line-height:1.2; text-transform:uppercase; }
.inv-clinic-sub { font-size:8pt; color:#444; line-height:1.4; }
.inv-header-right { text-align:right; font-size:8.5pt; color:#333; flex-shrink:0; min-width:42mm; }
.inv-doc-row { display:flex; justify-content:flex-end; gap:4px; line-height:1.6; }
.inv-lbl { color:#555; }
.inv-val { font-weight:600; }
.inv-title-block { text-align:center; margin:2mm 0 3mm; }
.inv-title { font-size:15pt; font-weight:900; letter-spacing:2px; text-transform:uppercase; color:#1a3a6b; text-decoration:underline; text-underline-offset:3px; }
.inv-barcode-wrap { margin-top:2mm; }
.inv-barcode-wrap svg { max-width:100%; height:auto; }
.inv-patient-section { margin-bottom:2mm; }
.inv-patient-row { display:flex; flex-wrap:wrap; align-items:baseline; gap:4px; margin-bottom:1.5mm; font-size:10.5pt; }
.inv-field-label { font-weight:700; white-space:nowrap; }
.inv-field-value { color:#111; }
.inv-patient-name { font-size:13pt; font-weight:700; text-transform:uppercase; }
.inv-divider { border:none; border-top:1px solid #aaa; margin:3mm 0; }
.inv-summary-table { width:100%; border-collapse:collapse; font-size:10pt; }
.inv-summary-table th { background:#f5f5f5; border:1px solid #ccc; padding:4px 6px; font-size:9.5pt; }
.inv-summary-table td { border:1px solid #ddd; padding:4px 6px; vertical-align:middle; }
.inv-summary-table tfoot td { border:1px solid #ccc; background:#ffffff; padding:5px 6px; }
.inv-total-row td { font-size:12pt; }
.inv-badge { display:inline-block; font-size:7pt; padding:1px 4px; border-radius:3px; color:#fff; margin-right:3px; }
.inv-badge-info { background:#0dcaf0; color:#000; }
.inv-badge-success { background:#198754; }
.inv-badge-primary { background:#0d6efd; }
.inv-badge-secondary { background:#6c757d; }
.inv-sign-row { margin-top:4mm; overflow:hidden; }
.inv-sign-col-left { float:left; width:50%; font-size:9.5pt; }
.inv-notes-text { font-size:9pt; color:#555; font-style:italic; margin-top:1mm; }
.inv-sign-col-right { float:right; text-align:center; width:45%; font-size:10pt; }
.inv-sign-date { font-size:9pt; color:#333; margin-bottom:1mm; }
.inv-sign-title { font-weight:700; }
.inv-sign-space { height:17mm; }
.inv-sign-name { font-weight:700; font-size:10.5pt; text-transform:uppercase; border-top:1px dashed #999; padding-top:1mm; }

/* ===== BẢN KÊ ===== */
.bk-page {
    background: #fff;
    border-radius: 4px;
    box-shadow: 0 2px 12px rgba(0,0,0,.1);
    padding: 16px 18px;
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #111;
}
.bk-header { text-align:center; border-bottom:2px solid #333; padding-bottom:10px; margin-bottom:12px; }
.bk-clinic-name { font-size:15px; font-weight:bold; margin:0; }
.bk-clinic-sub { font-size:11px; margin:2px 0; color:#444; }
.bk-title { font-size:14px; font-weight:bold; text-transform:uppercase; margin:8px 0 2px; color:#1a3a6b; }
.bk-meta { font-size:11px; margin:0; }
.bk-info { width:100%; font-size:12px; margin-bottom:12px; border-collapse:collapse; }
.bk-info td { padding:2px 0; }
.bk-section-title { font-weight:bold; margin:10px 0 4px; font-size:12px; border-left:3px solid #1a56a0; padding-left:6px; }
.bk-table { width:100%; border-collapse:collapse; font-size:11px; margin-bottom:8px; }
.bk-table th, .bk-table td { border:1px solid #ccc; padding:4px 5px; }
.bk-table th { background:#f0f0f0; font-size:10.5px; }
.bk-table tfoot td { background:#f9f9f9; font-weight:bold; }
.bk-total { width:100%; border-collapse:collapse; font-size:12px; margin-top:10px; }
.bk-total td { border:2px solid #333; padding:6px 8px; }
.bk-total tr:last-child { background:#ffffff; }
.bk-sign-row { margin-top:20px; overflow:hidden; }
.bk-sign-box { float:right; text-align:center; min-width:160px; }

/* ===== PRINT ===== */
@media print {
    @page { size: A5 portrait; margin: 8mm 10mm; }

    /* Ẩn các menu, header, footer của layout mặc định */
    body > *:not(#app) { display: none !important; }
    .navbar, .sidebar, .topbar, header, footer, aside { display: none !important; }
    .d-print-none { display: none !important; }
    
    /* Gỡ bỏ khoảng cách của layout */
    body, html, main, #app, .content-wrapper, .container, .container-fluid {
        margin: 0 !important; padding: 0 !important; background: #fff !important; width: 100% !important; max-width: 100% !important;
    }

    .hd-layout { display: block !important; padding: 0 !important; margin: 0 !important; }

    /* Khi in Hóa đơn */
    body.print-invoice .hd-col-banske { display: none !important; }
    body.print-invoice .hd-col-invoice { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body.print-invoice .inv-page { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; }

    /* Khi in Bản kê */
    body.print-banske .hd-col-invoice { display: none !important; }
    body.print-banske .hd-col-banske { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    body.print-banske .bk-page { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; }

    .text-success { color:#198754 !important; }
    .text-danger  { color:#dc3545 !important; }
    -webkit-print-color-adjust: exact; print-color-adjust: exact;
}

/* Responsive: stack khi màn hình nhỏ */
@media (max-width: 1100px) {
    .hd-layout { flex-direction: column; }
    .hd-col-invoice { width: 100%; }
    .inv-page { width: 100%; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    JsBarcode("#inv-barcode", "{{ $invoice->invoice_code }}", {
        format: "CODE128",
        width: 1.4,
        height: 32,
        displayValue: false,
        margin: 0,
        background: "#ffffff",
        lineColor: "#000000"
    });
});
function printInvoice() {
    document.body.classList.remove('print-banske');
    document.body.classList.add('print-invoice');
    window.print();
    setTimeout(() => document.body.classList.remove('print-invoice'), 1000);
}
function printBanKe() {
    document.body.classList.remove('print-invoice');
    document.body.classList.add('print-banske');
    window.print();
    setTimeout(() => document.body.classList.remove('print-banske'), 1000);
}
</script>
@endsection
