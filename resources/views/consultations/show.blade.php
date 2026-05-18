@extends('layouts.app')

@push('styles')
<style>
    /* ========= VARIABLES ========= */
    :root {
        --rx-green:     #16a34a;
        --rx-green-bg:  #f0fdf4;
        --rx-green-lt:  #bbf7d0;
        --rx-blue:      #2563eb;
        --rx-blue-bg:   #eff6ff;
        --rx-amber:     #d97706;
        --rx-amber-bg:  #fffbeb;
        --rx-amber-lt:  #fde68a;
        --rx-indigo:    #4f46e5;
        --rx-gray:      #6b7280;
        --rx-border:    #e5e7eb;
    }

    /* ========= PAGE WRAPPER ========= */
    .rx-page { background: #f8fafc; min-height: 100vh; }

    /* ========= HEADER BANNER ========= */
    .rx-page-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(37,99,235,.25);
        position: relative;
        overflow: hidden;
    }
    .rx-page-header::before {
        content: '\f0f1';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 1.5rem; top: 50%;
        transform: translateY(-50%);
        font-size: 5rem;
        opacity: .08;
    }
    .rx-page-header .badge-status {
        background: rgba(255,255,255,.18);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 2rem;
        padding: .3rem .9rem;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .05em;
    }

    /* ========= INFO CARDS ========= */
    .rx-card {
        background: #fff;
        border-radius: .875rem;
        border: 1px solid var(--rx-border);
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        overflow: hidden;
        margin-bottom: 1.25rem;
    }
    .rx-card-header {
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--rx-border);
        display: flex;
        align-items: center;
        gap: .6rem;
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: .01em;
    }
    .rx-card-header .icon-wrap {
        width: 32px; height: 32px;
        border-radius: .5rem;
        display: flex; align-items: center; justify-content: center;
        font-size: .85rem;
        flex-shrink: 0;
    }
    .rx-card-body { padding: 1.25rem; }

    /* ========= INFO TABLE ========= */
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table tr { border-bottom: 1px solid #f3f4f6; }
    .info-table tr:last-child { border-bottom: none; }
    .info-table td { padding: .55rem .25rem; vertical-align: top; font-size: .875rem; }
    .info-table td:first-child {
        width: 38%; color: var(--rx-gray);
        font-weight: 500; padding-right: .75rem;
        white-space: nowrap;
    }
    .info-table td:last-child { color: #111827; font-weight: 500; }

    /* ========= PRESCRIPTION BLOCK ========= */
    .pres-block {
        border: 1px solid #d1fae5;
        border-radius: 1rem;
        background: #fff;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(22,163,74,.08);
    }

    /* ---- Doctor strip ---- */
    .pres-doctor-strip {
        background: linear-gradient(90deg, var(--rx-green-bg) 0%, #fff 100%);
        border-bottom: 1px solid var(--rx-green-lt);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: .75rem;
    }
    .pres-doctor-avatar {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--rx-green) 0%, #059669 100%);
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-size: 1.25rem;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(22,163,74,.3);
    }
    .pres-doctor-info .doctor-name {
        font-weight: 700; font-size: .975rem; color: #065f46; line-height: 1.2;
    }
    .pres-doctor-info .doctor-meta {
        font-size: .78rem; color: var(--rx-gray); margin-top: .2rem;
    }
    .pres-id-badge {
        background: var(--rx-green);
        color: #fff;
        border-radius: 2rem;
        padding: .3rem .9rem;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .04em;
    }

    /* ---- Med table ---- */
    .med-table-wrap { padding: 1.25rem; }
    .med-table {
        width: 100%; border-collapse: collapse;
        font-size: .85rem;
    }
    .med-table thead tr {
        background: #f0fdf4;
    }
    .med-table thead th {
        padding: .65rem .85rem;
        font-weight: 700;
        color: var(--rx-green);
        border-bottom: 2px solid var(--rx-green-lt);
        white-space: nowrap;
    }
    .med-table tbody tr { border-bottom: 1px solid #f3f4f6; }
    .med-table tbody tr:last-child { border-bottom: none; }
    .med-table tbody tr:hover { background: #fafffe; }
    .med-table tbody td { padding: .7rem .85rem; vertical-align: middle; }
    .med-name { font-weight: 600; color: #111827; }
    .med-idx {
        display: inline-flex; align-items: center; justify-content: center;
        width: 24px; height: 24px;
        border-radius: 50%;
        background: var(--rx-green-lt);
        color: var(--rx-green);
        font-size: .75rem; font-weight: 700;
        flex-shrink: 0;
    }
    .qty-pill {
        display: inline-block;
        background: #eff6ff;
        color: var(--rx-blue);
        border-radius: 2rem;
        padding: .15rem .6rem;
        font-weight: 700;
        font-size: .82rem;
    }
    .dosage-tag {
        display: inline-block;
        background: var(--rx-amber-bg);
        color: var(--rx-amber);
        border: 1px solid var(--rx-amber-lt);
        border-radius: .375rem;
        padding: .15rem .5rem;
        font-size: .78rem;
        font-weight: 600;
    }

    /* ---- Advice box ---- */
    .advice-box {
        margin: 0 1.25rem 1.25rem;
        background: #fffbeb;
        border-left: 4px solid var(--rx-amber);
        border-radius: 0 .5rem .5rem 0;
        padding: .85rem 1rem;
        font-size: .85rem;
        color: #78350f;
    }
    .advice-box .advice-label {
        font-weight: 700; font-size: .82rem; color: var(--rx-amber);
        text-transform: uppercase; letter-spacing: .06em; margin-bottom: .35rem;
    }

    /* ========= EMPTY STATE ========= */
    .empty-rx {
        text-align: center; padding: 2.5rem 1rem; color: var(--rx-gray);
    }
    .empty-rx i { font-size: 2.5rem; opacity: .3; display: block; margin-bottom: .75rem; }

    /* ========= ACTION FOOTER ========= */
    .rx-footer {
        background: #fff;
        border: 1px solid var(--rx-border);
        border-radius: .875rem;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
    }

    /* ========= DIAGNOSIS CHIP ========= */
    .diag-chip {
        display: inline-flex; align-items: center; gap: .5rem;
        background: #eff6ff; border: 1px solid #bfdbfe;
        border-radius: .5rem; padding: .4rem .85rem;
        font-size: .85rem; color: var(--rx-blue); font-weight: 600;
    }

    /* ========= STATUS COLORS ========= */
    .status-completed { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-wait      { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .status-other     { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
</style>
@endpush

@section('content')
@php
    /* ---- Lấy bác sĩ từ phòng khám được gán ---- */
    $assignedRoom   = $consultation->rooms->first();
    $doctorInRoom   = $assignedRoom ? $assignedRoom->staff->where('staff_type', 'doctor')->first()
                        ?? $assignedRoom->staff->first()
                        : null;

    /* ---- Màu badge trạng thái ---- */
    $statusMap = [
        \App\Models\Consultation::STATUS_NEW          => ['label' => 'Mới tạo',          'class' => 'status-other'],
        \App\Models\Consultation::STATUS_PENDING      => ['label' => 'Chờ khám',          'class' => 'status-wait'],
        \App\Models\Consultation::STATUS_PROCESSING   => ['label' => 'Đang khám',         'class' => 'status-wait'],
        \App\Models\Consultation::STATUS_WAIT_PAYMENT => ['label' => 'Chờ thanh toán',    'class' => 'status-wait'],
        \App\Models\Consultation::STATUS_COMPLETED    => ['label' => 'Hoàn thành',        'class' => 'status-completed'],
    ];
    $statusInfo = $statusMap[$consultation->status] ?? ['label' => $consultation->status_text, 'class' => 'status-other'];
@endphp

<div class="container-fluid px-4 mt-4 rx-page">

    {{-- ===== HEADER ===== --}}
    <div class="rx-page-header mb-4">
        <div class="d-flex align-items-center gap-3 mb-2">
            <span class="badge-status">
                <i class="fas fa-file-medical me-1"></i>Phiên khám #{{ $consultation->id }}
            </span>
            <span class="badge-status {{ $statusInfo['class'] }}" style="background:rgba(255,255,255,.22);">
                {{ $statusInfo['label'] }}
            </span>
        </div>
        <h4 class="mb-0 fw-bold" style="font-size:1.35rem;">
            {{ $consultation->patient->full_name }}
        </h4>
        <small class="opacity-75">
            <i class="fas fa-id-card me-1"></i>{{ $consultation->patient->patient_code }}
            &nbsp;·&nbsp;
            <i class="far fa-calendar-alt me-1"></i>
            {{ $consultation->consultation_date ? \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') : '--' }}
            @if($assignedRoom)
                &nbsp;·&nbsp;<i class="fas fa-door-open me-1"></i>{{ $assignedRoom->room_name }}
            @endif
        </small>
    </div>

    <div class="row g-4">

        {{-- ===== CỘT TRÁI: THÔNG TIN PHIÊN KHÁM ===== --}}
        <div class="col-xl-4 col-lg-5">

            {{-- Thông tin hành chính --}}
            <div class="rx-card">
                <div class="rx-card-header" style="color:#1e40af; border-left: 3px solid #2563eb;">
                    <div class="icon-wrap" style="background:#eff6ff; color:#2563eb;">
                        <i class="fas fa-user-injured"></i>
                    </div>
                    Thông tin bệnh nhân
                </div>
                <div class="rx-card-body">
                    <table class="info-table">
                        <tr>
                            <td>Họ tên</td>
                            <td class="fw-bold">{{ $consultation->patient->full_name }}</td>
                        </tr>
                        <tr>
                            <td>Mã BN</td>
                            <td><code class="text-primary">{{ $consultation->patient->patient_code }}</code></td>
                        </tr>
                        <tr>
                            <td>Ngày sinh</td>
                            <td>{{ $consultation->patient->date_of_birth ? \Carbon\Carbon::parse($consultation->patient->date_of_birth)->format('d/m/Y') : '--' }}</td>
                        </tr>
                        <tr>
                            <td>Giới tính</td>
                            <td>{{ $consultation->patient->gender ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td>SĐT</td>
                            <td>{{ $consultation->patient->phone_number ?? '--' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Kết luận lâm sàng --}}
            <div class="rx-card">
                <div class="rx-card-header" style="color:#92400e; border-left: 3px solid #d97706;">
                    <div class="icon-wrap" style="background:#fffbeb; color:#d97706;">
                        <i class="fas fa-stethoscope"></i>
                    </div>
                    Kết luận lâm sàng
                </div>
                <div class="rx-card-body">
                    <table class="info-table">
                        <tr>
                            <td>Lý do khám</td>
                            <td>{{ $consultation->chief_complaint ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td>Triệu chứng</td>
                            <td><span class="text-danger">{{ $consultation->symptoms ?? '--' }}</span></td>
                        </tr>
                        <tr>
                            <td>Nhiệt độ</td>
                            <td>{{ $consultation->temperature ? $consultation->temperature . ' °C' : '--' }}</td>
                        </tr>
                        <tr>
                            <td>Huyết áp</td>
                            <td>{{ $consultation->blood_pressure ?? '--' }}</td>
                        </tr>
                        <tr>
                            <td>Ngày tái khám</td>
                            <td>{{ $consultation->follow_up_date ? \Carbon\Carbon::parse($consultation->follow_up_date)->format('d/m/Y') : '--' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold" style="color:#92400e;">
                                <i class="fas fa-notes-medical me-1 text-warning"></i>Chẩn đoán
                            </td>
                            <td>
                                @if($consultation->diagnosis_notes)
                                    <span class="fw-semibold" style="color:#1c1917;">{{ $consultation->diagnosis_notes }}</span>
                                @else
                                    <span class="text-muted fst-italic">Chưa có kết luận</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-semibold" style="color:#1e40af;">
                                <i class="fas fa-tag me-1 text-primary"></i>Mã ICD
                            </td>
                            <td>
                                @if($consultation->diagnosis_code)
                                    <span class="diag-chip">
                                        <i class="fas fa-tag"></i>
                                        {{ $consultation->diagnosis_code }}
                                        @if($consultation->diagnosis)
                                            — {{ $consultation->diagnosis->diagnosis_name }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">--</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        {{-- ===== CỘT PHẢI: TOA THUỐC ===== --}}
        <div class="col-xl-8 col-lg-7">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0" style="color:#065f46;">
                    <i class="fas fa-prescription-bottle-alt me-2 text-success"></i>
                    Toa thuốc trong phiên khám
                    @if($consultation->prescriptions->count() > 0)
                        <span class="badge bg-success rounded-pill ms-2" style="font-size:.72rem;">
                            {{ $consultation->prescriptions->count() }} toa
                        </span>
                    @endif
                </h5>
            </div>

            @if($consultation->prescriptions->count() === 0)
                <div class="rx-card">
                    <div class="empty-rx">
                        <i class="fas fa-prescription-bottle"></i>
                        <div class="fw-semibold">Chưa có toa thuốc nào</div>
                        <div class="small text-muted mt-1">Phiên khám này chưa được kê đơn thuốc.</div>
                    </div>
                </div>
            @else
                @foreach($consultation->prescriptions as $index => $p)
                @php
                    /* -- Tìm bác sĩ kê đơn:
                         Ưu tiên: dispenser (nếu có) → doctor trong phòng → fallback --*/
                    $prescribingDoctor = $doctorInRoom;
                    $prescribingRoom   = $assignedRoom;
                @endphp
                <div class="pres-block">

                    {{-- ---- Doctor strip ---- --}}
                    <div class="pres-doctor-strip">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pres-doctor-avatar">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="pres-doctor-info">
                                <div class="doctor-name">
                                    @if($prescribingDoctor)
                                        {{ $prescribingDoctor->academic_degree ? $prescribingDoctor->academic_degree . '. ' : '' }}{{ $prescribingDoctor->full_name }}
                                    @else
                                        <span class="text-muted fst-italic">Chưa xác định bác sĩ</span>
                                    @endif
                                </div>
                                <div class="doctor-meta">
                                    @if($prescribingDoctor)
                                        <i class="fas fa-id-badge me-1"></i>{{ $prescribingDoctor->staff_code ?? '' }}
                                        @if($prescribingDoctor->specialty)
                                            &nbsp;·&nbsp;<i class="fas fa-heartbeat me-1"></i>{{ $prescribingDoctor->specialty }}
                                        @endif
                                        <br>
                                    @endif
                                    @if($prescribingRoom)
                                        <i class="fas fa-door-open me-1 text-success"></i>{{ $prescribingRoom->room_name }}
                                        @if($prescribingRoom->department)
                                            &nbsp;·&nbsp;{{ $prescribingRoom->department->department_name ?? '' }}
                                        @endif
                                        &nbsp;·&nbsp;
                                    @endif
                                    <i class="far fa-clock me-1"></i>
                                    {{ $p->prescribed_at ? \Carbon\Carbon::parse($p->prescribed_at)->format('d/m/Y H:i') : '--' }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="pres-id-badge">
                                <i class="fas fa-file-prescription me-1"></i>Toa #{{ $p->id }}
                            </span>
                            <a href="{{ route('prescriptions.show', $p->id) }}"
                               class="btn btn-sm btn-outline-success"
                               title="In toa thuốc">
                                <i class="fas fa-print me-1"></i>In toa
                            </a>
                        </div>
                    </div>

                    {{-- ---- Medicine table ---- --}}
                    <div class="med-table-wrap">
                        <table class="med-table">
                            <thead>
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th>Tên thuốc</th>
                                    <th style="width:90px;" class="text-center">Số lượng</th>
                                    <th style="width:80px;">Đơn vị</th>
                                    <th>Liều dùng</th>
                                    <th>Cách dùng</th>
                                    <th style="width:75px;" class="text-center">Số ngày</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($p->details as $i => $d)
                                    <tr>
                                        <td>
                                            <span class="med-idx">{{ $i + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="med-name">
                                                <i class="fas fa-capsules me-1" style="color:#16a34a; font-size:.75rem;"></i>
                                                {{ $d->medicine->medicine_name ?? '--' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="qty-pill">{{ $d->quantity }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $d->unit ?? '--' }}</span>
                                        </td>
                                        <td>
                                            @if($d->dosage)
                                                <span class="dosage-tag">{{ $d->dosage }}</span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td>{{ $d->usage_instructions ?? '--' }}</td>
                                        <td class="text-center">
                                            @if($d->duration_days)
                                                <span class="badge bg-light text-dark border" style="font-size:.78rem;">{{ $d->duration_days }}n</span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">
                                            <i class="fas fa-inbox me-2 opacity-50"></i>Toa thuốc không có chi tiết thuốc.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- ---- Lời dặn ---- --}}
                    @if($p->doctor_advice)
                        <div class="advice-box">
                            <div class="advice-label">
                                <i class="fas fa-comment-medical me-1"></i>Lời dặn của bác sĩ
                            </div>
                            {!! nl2br(e($p->doctor_advice)) !!}
                        </div>
                    @endif

                    {{-- ---- Thông tin phát thuốc (nếu có) ---- --}}
                    @if($p->dispenser)
                        <div class="d-flex align-items-center gap-2 px-4 pb-3"
                             style="font-size:.8rem; color:var(--rx-gray);">
                            <i class="fas fa-pills text-info"></i>
                            <span>Phát thuốc bởi: <strong>{{ $p->dispenser->full_name }}</strong></span>
                            @if($p->dispensed_at)
                                &nbsp;·&nbsp;
                                <i class="far fa-clock"></i>
                                {{ \Carbon\Carbon::parse($p->dispensed_at)->format('d/m/Y H:i') }}
                            @endif
                        </div>
                    @endif

                </div>
                @endforeach
            @endif

        </div>
    </div>

    {{-- ===== FOOTER ACTIONS ===== --}}
    <div class="rx-footer mt-2 mb-4">
        @if(request()->has('reuse_mode') && $consultation->prescriptions->count() > 0)
            <button type="button" class="btn btn-warning shadow-sm fw-bold px-4" id="btn-reuse">
                <i class="fas fa-copy me-1"></i> Sử dụng lại toa thuốc này
            </button>
        @endif

        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

</div>

{{-- ===== REUSE SCRIPT ===== --}}
@if(request()->has('reuse_mode'))
    @php
        $medicinesArr = [];
        foreach ($consultation->prescriptions as $pres) {
            foreach ($pres->details as $det) {
                $medicinesArr[] = [
                    'id'                 => $det->medicine_id,
                    'name'               => $det->medicine->medicine_name ?? '',
                    'quantity'           => $det->quantity,
                    'unit'               => $det->unit,
                    'dosage'             => $det->dosage,
                    'usage_instructions' => $det->usage_instructions,
                    'duration_days'      => $det->duration_days,
                ];
            }
        }
        $adviceData = $consultation->prescriptions->first()->doctor_advice ?? '';
    @endphp
    @push('scripts')
    <script>
        document.getElementById('btn-reuse')?.addEventListener('click', function () {
            const medicines = {!! json_encode($medicinesArr) !!};
            const advice    = {!! json_encode($adviceData) !!};

            if (window.opener && !window.opener.closed) {
                if (confirm('Bạn có muốn sao chép toàn bộ danh sách thuốc này vào phiên khám hiện tại không?')) {
                    window.opener.postMessage({ type: 'REUSE_PRESCRIPTION', medicines, advice }, '*');
                    window.close();
                }
            } else {
                if (confirm('Hệ thống sẽ chuyển bạn về trang khám bệnh và điền dữ liệu thuốc này. Bạn đồng ý chứ?')) {
                    localStorage.setItem('reuse_prescription_data', JSON.stringify({
                        medicines,
                        advice,
                        timestamp: Date.now()
                    }));
                    if (document.referrer && document.referrer.includes('/examine')) {
                        window.location.href = document.referrer;
                    } else {
                        window.history.back();
                    }
                }
            }
        });
    </script>
    @endpush
@endif
@endsection