@extends('layouts.app')

@section('content')
{{-- ===== NÚT ĐIỀU KHIỂN (ẩn khi in) ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3 d-print-none px-3">
    <h5 class="mb-0 text-primary fw-bold">
        <i class="fas fa-prescription-bottle-alt me-2"></i>
        Chi Tiết Toa Thuốc #{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}
    </h5>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-print me-1"></i> In Đơn Thuốc (A5)
        </button>
        <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>
</div>

{{-- ===== KHU VỰC IN ===== --}}
<div class="prescription-wrapper">
    <div class="rx-page" id="print-area">

        {{-- ===== HEADER: LOGO / TÊN PHÒNG KHÁM ===== --}}
        <div class="rx-header">
            <div class="rx-header-left">
                <div class="rx-clinic-logo">
                    <i class="fas fa-hospital-alt"></i>
                </div>
                <div class="rx-clinic-info">
                    @if($setting)
                        <div class="rx-clinic-name">{{ $setting->clinic_name }}</div>
                        @if($setting->address)
                            <div class="rx-clinic-sub">{{ $setting->address }}</div>
                        @endif
                        @if($setting->phone_number)
                            <div class="rx-clinic-sub">ĐT: {{ $setting->phone_number }}</div>
                        @endif
                        @if($setting->email)
                            <div class="rx-clinic-sub">Email: {{ $setting->email }}</div>
                        @endif
                    @else
                        <div class="rx-clinic-name">PHÒNG KHÁM ĐA KHOA</div>
                        <div class="rx-clinic-sub">Địa chỉ: ---</div>
                    @endif
                </div>
            </div>
            <div class="rx-header-right">
                <div class="rx-doc-info-row">
                    <span class="rx-label">Số phiếu:</span>
                    <span class="rx-value">#{{ str_pad($prescription->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="rx-doc-info-row">
                    <span class="rx-label">Ngày kê:</span>
                    <span class="rx-value">{{ \Carbon\Carbon::parse($prescription->prescribed_at)->format('d/m/Y') }}</span>
                </div>
                <div class="rx-doc-info-row">
                    <span class="rx-label">Trạng thái:</span>
                    <span class="rx-value {{ $prescription->status == 2 ? 'text-success' : 'text-danger' }}">
                        {{ $prescription->status == 2 ? 'Đã phát thuốc' : 'Chưa phát thuốc' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== TIÊU ĐỀ ===== --}}
        <div class="rx-title-block">
            <div class="rx-title">ĐƠN THUỐC</div>
        </div>

        {{-- ===== THÔNG TIN BỆNH NHÂN ===== --}}
        @php
            $patient = $prescription->consultation->patient;
            $genderText = match($patient->gender ?? '') {
                'Male'   => 'Nam',
                'Female' => 'Nữ',
                default  => 'Khác',
            };
        @endphp

        <div class="rx-patient-section">
            <div class="rx-patient-row">
                <span class="rx-field-label">Họ và tên:</span>
                <span class="rx-patient-name">{{ $patient->full_name }}</span>
                <span class="rx-field-label ms-auto">Giới tính:</span>
                <span class="rx-field-value">{{ $genderText }}</span>
                @if($patient->age)
                    <span class="rx-field-label ms-2">Tuổi:</span>
                    <span class="rx-field-value">{{ $patient->age }}</span>
                @endif
            </div>
            <div class="rx-patient-row">
                <span class="rx-field-label">Mã BN:</span>
                <span class="rx-field-value">{{ $patient->patient_code ?? '---' }}</span>
                @if($patient->phone_number)
                    <span class="rx-field-label ms-auto">SĐT:</span>
                    <span class="rx-field-value">{{ $patient->phone_number }}</span>
                @endif
            </div>
            @if($patient->address)
            <div class="rx-patient-row">
                <span class="rx-field-label">Địa chỉ:</span>
                <span class="rx-field-value">{{ $patient->address }}</span>
            </div>
            @endif
            <div class="rx-patient-row">
                <span class="rx-field-label">Chẩn đoán:</span>
                <span class="rx-field-value fw-bold text-danger">
                    {{ $prescription->consultation->symptoms ?? 'Không có chẩn đoán' }}
                </span>
            </div>
        </div>

        <div class="rx-divider"></div>

        {{-- ===== DANH SÁCH THUỐC ===== --}}
        <div class="rx-medicines">
            @foreach($prescription->details as $index => $detail)
            <div class="rx-med-item">
                <div class="rx-med-header">
                    <span class="rx-med-index">{{ $index + 1 }}.</span>
                    <span class="rx-med-name">
                        {{ $detail->medicine->medicine_name }}
                        @if($detail->medicine->concentration ?? null)
                            <span class="rx-med-concentration">({{ $detail->medicine->concentration }})</span>
                        @endif
                    </span>
                    <span class="rx-med-qty">
                        <strong>{{ $detail->quantity }}</strong>
                        <span class="rx-med-unit">{{ $detail->unit }}</span>
                    </span>
                </div>
                <div class="rx-med-usage">
                    @if($detail->dosage)
                        <span>{{ $detail->dosage }}</span>
                    @endif
                    @if($detail->usage_instructions)
                        <span class="ms-2">— {{ $detail->usage_instructions }}</span>
                    @endif
                    @if($detail->duration_days)
                        <span class="ms-2">— Dùng {{ $detail->duration_days }} ngày</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="rx-divider"></div>

        {{-- ===== TỔNG SỐ THUỐC ===== --}}
        <div class="rx-summary">
            Cộng khoản: <strong>{{ $prescription->details->count() }}</strong> loại thuốc
        </div>

        {{-- ===== CHỮ KÝ + LỜI DẶN (layout 2 cột như mẫu) ===== --}}
        <div class="rx-sign-row">
            {{-- Cột trái: lời dặn + tái khám --}}
            <div class="rx-sign-col-left">
                <div class="rx-advice-block">
                    <div class="rx-field-label">Lời dặn :</div>
                    <div class="rx-advice-text">
                        @if($prescription->doctor_advice)
                            {!! nl2br(e($prescription->doctor_advice)) !!}
                        @else
                            <span class="text-muted fst-italic">(Không có)</span>
                        @endif
                    </div>
                </div>

                @if($prescription->consultation->follow_up_date)
                <div class="rx-followup-block">
                    <span class="rx-field-label">Tái khám: </span>
                    <span>{{ \Carbon\Carbon::parse($prescription->consultation->follow_up_date)->format('d/m/Y') }}</span>
                </div>
                @endif
            </div>

            {{-- Cột phải: ngày + chữ ký bác sĩ --}}
            <div class="rx-sign-col-right">
                <div class="rx-sign-date">
                    Ngày {{ \Carbon\Carbon::parse($prescription->prescribed_at)->format('d') }}
                    tháng {{ \Carbon\Carbon::parse($prescription->prescribed_at)->format('m') }}
                    năm {{ \Carbon\Carbon::parse($prescription->prescribed_at)->format('Y') }}
                </div>
                <div class="rx-sign-title">Bác sĩ điều trị</div>
                <div class="rx-sign-space"></div>
                <div class="rx-sign-name">
                    @if($prescription->prescriber)
                        {{ $prescription->prescriber->full_name }}
                    @elseif($prescription->consultation->doctor)
                        {{ $prescription->consultation->doctor->full_name }}
                    @else
                        ---
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== GHI CHÚ DƯỚI CHỮ KÝ ===== --}}
        <div class="rx-footer-note">
            Khám lại xin mang theo đơn này.
            @if($prescription->consultation->follow_up_date == null && !$prescription->doctor_advice)
            @endif
        </div>

        {{-- ===== GHI CHÚ CUỐI (additional_info) ===== --}}
        @if($setting && $setting->additional_info)
        <div class="rx-bottom-note">
            <em>{{ $setting->additional_info }}</em>
        </div>
        @endif

    </div>{{-- end #print-area --}}
</div>{{-- end .prescription-wrapper --}}

<style>
/* =======================================
   PRESCRIPTION SCREEN STYLES
   ======================================= */
.prescription-wrapper {
    display: flex;
    justify-content: center;
    padding: 1rem 1rem 3rem;
    background: #f0f4f8;
    min-height: calc(100vh - 80px);
}

.rx-page {
    background: #ffffff;
    width: 148mm;           /* A5 width */
    min-height: 210mm;      /* A5 height */
    padding: 8mm 10mm 8mm;
    border-radius: 4px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.13);
    font-family: 'Times New Roman', Times, serif;
    font-size: 11pt;
    line-height: 1.5;
    color: #111;
    position: relative;
}

/* ----- HEADER ----- */
.rx-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1.5px solid #222;
    padding-bottom: 4mm;
    margin-bottom: 3mm;
}

.rx-header-left {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    flex: 1;
}

.rx-clinic-logo {
    font-size: 28px;
    color: #1a56a0;
    margin-top: 2px;
    flex-shrink: 0;
}

.rx-clinic-name {
    font-size: 13pt;
    font-weight: 700;
    color: #1a3a6b;
    line-height: 1.2;
    text-transform: uppercase;
}

.rx-clinic-sub {
    font-size: 8.5pt;
    color: #444;
    line-height: 1.4;
}

.rx-header-right {
    text-align: right;
    font-size: 8.5pt;
    color: #333;
    flex-shrink: 0;
    min-width: 44mm;
}

.rx-doc-info-row {
    display: flex;
    justify-content: flex-end;
    gap: 4px;
    line-height: 1.6;
}

.rx-doc-info-row .rx-label { color: #555; }
.rx-doc-info-row .rx-value { font-weight: 600; color: #111; }

/* ----- TITLE ----- */
.rx-title-block {
    text-align: center;
    margin: 3mm 0 4mm;
}

.rx-title {
    font-size: 17pt;
    font-weight: 900;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #1a3a6b;
    text-decoration: underline;
    text-underline-offset: 3px;
}

/* ----- PATIENT ----- */
.rx-patient-section {
    margin-bottom: 2mm;
}

.rx-patient-row {
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 1.5mm;
    font-size: 10.5pt;
}

.rx-field-label {
    font-weight: 700;
    white-space: nowrap;
    color: #111;
}

.rx-field-value {
    color: #111;
}

.rx-patient-name {
    font-size: 13pt;
    font-weight: 700;
    text-transform: uppercase;
    color: #111;
}

/* ----- DIVIDER ----- */
.rx-divider {
    border: none;
    border-top: 1px solid #aaa;
    margin: 3mm 0;
}

/* ----- MEDICINE LIST ----- */
.rx-medicines {
    margin-bottom: 1mm;
}

.rx-med-item {
    margin-bottom: 2.5mm;
}

.rx-med-header {
    display: flex;
    align-items: baseline;
    gap: 4px;
    font-size: 10.5pt;
}

.rx-med-index {
    font-weight: 700;
    min-width: 16px;
    flex-shrink: 0;
}

.rx-med-name {
    font-weight: 700;
    flex: 1;
    color: #1a1a1a;
}

.rx-med-concentration {
    font-weight: 400;
    font-style: italic;
    font-size: 9.5pt;
    color: #444;
}

.rx-med-qty {
    text-align: right;
    flex-shrink: 0;
    min-width: 36px;
    font-size: 11pt;
    color: #1a3a6b;
}

.rx-med-qty strong {
    font-size: 12pt;
}

.rx-med-unit {
    font-size: 9pt;
    color: #444;
    margin-left: 2px;
}

.rx-med-usage {
    font-size: 9.5pt;
    color: #333;
    padding-left: 20px;
    font-style: italic;
    margin-top: 0.5mm;
}

/* ----- SUMMARY ----- */
.rx-summary {
    font-size: 10pt;
    margin-bottom: 2mm;
    color: #222;
}

/* ----- SIGN ROW (2 cột: lời dặn + chữ ký) ----- */
.rx-sign-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-top: 4mm;
    gap: 6mm;
}

.rx-sign-col-left {
    flex: 1;
    font-size: 10pt;
}

.rx-advice-block {
    margin-bottom: 2mm;
}

.rx-advice-text {
    font-size: 10pt;
    color: #222;
    margin-top: 1mm;
    min-height: 10mm;
}

.rx-followup-block {
    font-size: 10pt;
    color: #222;
    margin-top: 1.5mm;
}

/* ----- FOOTER NOTE (dưới cùng) ----- */
.rx-footer-note {
    font-size: 9pt;
    color: #444;
    margin-top: 3mm;
    font-style: italic;
}

/* ----- SIGN COL RIGHT ----- */
.rx-sign-col-right {
    text-align: center;
    min-width: 55mm;
    font-size: 10pt;
    flex-shrink: 0;
}

.rx-sign-date {
    font-size: 9.5pt;
    color: #333;
    margin-bottom: 1mm;
}

.rx-sign-title {
    font-weight: 700;
    margin-bottom: 0;
}

.rx-sign-space {
    height: 18mm;
}

.rx-sign-name {
    font-weight: 700;
    font-size: 11pt;
    text-transform: uppercase;
    border-top: 1px dashed #999;
    padding-top: 1mm;
}


/* ----- BOTTOM NOTE ----- */
.rx-bottom-note {
    margin-top: 4mm;
    font-size: 8.5pt;
    color: #555;
    border-top: 1px solid #ddd;
    padding-top: 2mm;
    text-align: center;
}

/* =======================================
   PRINT STYLES  —  A5 khổ đứng
   ======================================= */
@media print {
    @page {
        size: A5 portrait;
        margin: 8mm 10mm;
    }

    /* --- Kỹ thuật visibility: ẩn TOÀN BỘ trang, rồi chỉ hiện #print-area --- */
    body * {
        visibility: hidden !important;
    }

    #print-area,
    #print-area * {
        visibility: visible !important;
    }

    /* Đẩy #print-area ra vị trí góc trên-trái của trang in */
    #print-area {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        min-height: auto !important;
        font-size: 10.5pt !important;
        background: #fff !important;
    }

    /* Màu sắc cần giữ nguyên khi in */
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;

    .text-success,
    #print-area .text-success { color: #198754 !important; }
    .text-danger,
    #print-area .text-danger  { color: #dc3545 !important; }

    #print-area .rx-clinic-name,
    #print-area .rx-title,
    #print-area .rx-med-qty { color: #1a3a6b !important; }
}
</style>

@endsection