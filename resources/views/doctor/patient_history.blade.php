@extends('layouts.app')

@section('title', 'Lịch sử khám bệnh - ' . ($patient->full_name ?? ''))

@push('styles')
<style>
    .hist-card {
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        background: #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        overflow: hidden;
        transition: box-shadow .2s, transform .15s;
        height: 100%;
    }
    .hist-card:hover {
        box-shadow: 0 6px 20px rgba(107,114,128,.15);
        transform: translateY(-2px);
    }
    .hist-card-header {
        padding: .85rem 1.1rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        flex-wrap: wrap;
    }
    .hist-card-body { padding: .85rem 1.1rem; }
    .info-row { font-size: .82rem; margin-bottom: .3rem; color: #374151; }
    .info-row i { width: 16px; color: #9ca3af; }
    .diag-box {
        background: #fffbeb;
        border-left: 3px solid #d97706;
        border-radius: 0 .375rem .375rem 0;
        padding: .5rem .85rem;
        font-size: .82rem;
        color: #1c1917;
        margin-top: .5rem;
    }
    .search-bar-wrap {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        padding: 1rem 1.25rem;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
        margin-bottom: 1.5rem;
    }
    .page-header-bar {
        background: linear-gradient(135deg, #374151 0%, #6b7280 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.25rem 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(107,114,128,.25);
    }
    .patient-avatar {
        width: 52px; height: 52px;
        border-radius: 50%;
        background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }
    #no-results {
        display: none;
        text-align: center;
        padding: 3rem 1rem;
        color: #9ca3af;
    }
</style>
@endpush

@section('content')
@php
    $fromConsultation = $fromConsultation ?? null;
@endphp

<div class="container-fluid px-4 mt-4">

    {{-- HEADER --}}
    <div class="page-header-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="patient-avatar">👤</div>
            <div>
                <h4 class="mb-1 fw-bold">{{ $patient->full_name }}</h4>
                <div class="opacity-75" style="font-size:.85rem;">
                    <i class="fas fa-id-card me-1"></i>{{ $patient->patient_code }}
                    @if($patient->date_of_birth)
                        &nbsp;·&nbsp;<i class="fas fa-birthday-cake me-1"></i>{{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d/m/Y') }}
                    @endif
                    @if($patient->phone_number)
                        &nbsp;·&nbsp;<i class="fas fa-phone me-1"></i>{{ $patient->phone_number }}
                    @endif
                    @if($fromConsultation)
                        &nbsp;·&nbsp;Xem lịch sử cho <strong>Phiếu #{{ $fromConsultation }}</strong>
                    @endif
                </div>
            </div>
        </div>
        @if($fromConsultation)
            <a href="{{ route('consultations.doctor.examine', $fromConsultation) }}"
               class="btn btn-light fw-semibold shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại khám
            </a>
        @else
            <a href="{{ url()->previous() }}" class="btn btn-light fw-semibold shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        @endif
    </div>

    {{-- SEARCH BAR --}}
    <div class="search-bar-wrap">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchInput"
                        class="form-control border-start-0 ps-0"
                        placeholder="Tìm chẩn đoán, mã ICD, phòng khám..."
                        autocomplete="off">
                    <button class="btn btn-outline-secondary" id="clearSearch" type="button" style="display:none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-auto">
                <span class="text-muted small">
                    Tổng <span id="visibleCount">{{ $consultations->total() }}</span> lần khám
                </span>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH --}}
    @if($consultations->isEmpty())
        <div style="text-align:center; padding:4rem 1rem; color:#9ca3af;">
            <i class="fas fa-folder-open" style="font-size:3rem; opacity:.3; display:block; margin-bottom:1rem;"></i>
            <div class="fw-semibold" style="font-size:1.05rem;">Bệnh nhân chưa có lịch sử khám</div>
            <div class="small mt-1">Sau khi hoàn thành phiên khám, lịch sử sẽ hiển thị tại đây.</div>
        </div>
    @else
        <div id="historyGrid" class="row g-3">
            @foreach($consultations as $c)
            @php
                $prescriber   = $c->prescriptions->first()?->prescriber;
                $room         = $c->rooms->first();
                $doctorInRoom = $room ? ($room->staff->where('staff_type', 'doctor')->first() ?? $room->staff->first()) : null;
            @endphp
            <div class="col-xl-4 col-lg-6 hist-row-item"
                 data-search="{{ strtolower($c->diagnosis_notes ?? '') }} {{ strtolower($c->diagnosis->diagnosis_name ?? '') }} {{ strtolower($c->diagnosis_code ?? '') }} {{ strtolower($room->room_name ?? '') }} {{ strtolower($c->doctor->full_name ?? $prescriber->full_name ?? $doctorInRoom->full_name ?? '') }}">
                <div class="hist-card">
                    {{-- Header --}}
                    <div class="hist-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary bg-opacity-15 text-secondary fw-bold border border-secondary border-opacity-25" style="font-size:.8rem;">
                                #{{ $c->id }}
                            </span>
                            <span class="small text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($c->consultation_date)->format('d/m/Y') }}
                            </span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.72rem;">
                            Hoàn thành
                        </span>
                    </div>
                    {{-- Body --}}
                    <div class="hist-card-body">
                        {{-- Phòng khám --}}
                        @if($room)
                            <div class="info-row">
                                <i class="fas fa-door-open"></i>
                                {{ $room->room_name }}
                                @if($room->department)
                                    <span class="text-muted ms-1">— {{ $room->department->department_name }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Bác sĩ khám --}}
                        <div class="info-row">
                            <i class="fas fa-user-md text-primary"></i>
                            @if($c->doctor)
                                BS. {{ $c->doctor->full_name }}
                            @elseif($prescriber)
                                BS. {{ $prescriber->full_name }}
                            @elseif($doctorInRoom)
                                BS. {{ $doctorInRoom->full_name }}
                            @else
                                <span class="text-muted fst-italic">Chưa rõ bác sĩ</span>
                            @endif
                        </div>

                        {{-- Chẩn đoán --}}
                        @if($c->diagnosis_notes)
                            <div class="diag-box">
                                <strong style="font-size:.74rem; text-transform:uppercase; color:#d97706;">Chẩn đoán</strong><br>
                                {{ $c->diagnosis_notes }}
                            </div>
                        @endif

                        @if($c->diagnosis_code)
                            <div class="mt-2">
                                <span class="d-inline-flex align-items-center gap-1 px-2 py-1 rounded border border-primary border-opacity-25 bg-primary bg-opacity-10 text-primary fw-semibold" style="font-size:.75rem;">
                                    <i class="fas fa-tag"></i>
                                    {{ $c->diagnosis_code }}
                                    @if($c->diagnosis)
                                        — {{ $c->diagnosis->diagnosis_name }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-3">
                            <a href="{{ route('consultations.show', $c->id) }}{{ $fromConsultation ? '?reuse_mode=1&from_consultation='.$fromConsultation : '' }}"
                               class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i>Xem phiếu khám
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- No results --}}
        <div id="no-results">
            <i class="fas fa-search-minus" style="font-size:2.5rem; opacity:.3; display:block; margin-bottom:.75rem;"></i>
            <div class="fw-semibold">Không tìm thấy kết quả</div>
            <div class="small mt-1">Thử từ khóa khác</div>
        </div>

        {{-- Pagination --}}
        @if($consultations->hasPages())
            <div id="paginationWrap" class="d-flex justify-content-center mt-4">
                {{ $consultations->links() }}
            </div>
        @endif
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput    = document.getElementById('searchInput');
    const clearBtn       = document.getElementById('clearSearch');
    const items          = document.querySelectorAll('.hist-row-item');
    const noResults      = document.getElementById('no-results');
    const countSpan      = document.getElementById('visibleCount');
    const paginationWrap = document.getElementById('paginationWrap');

    function filterItems() {
        const keyword = searchInput.value.toLowerCase().trim();
        clearBtn.style.display = keyword ? '' : 'none';
        if (paginationWrap) paginationWrap.style.display = keyword ? 'none' : '';

        let visible = 0;
        items.forEach(function (item) {
            const haystack = item.getAttribute('data-search') || '';
            const match    = !keyword || haystack.includes(keyword);
            item.style.display = match ? '' : 'none';
            if (match) visible++;
        });

        if (countSpan) countSpan.textContent = visible;
        if (noResults) noResults.style.display = (visible === 0) ? 'block' : 'none';
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterItems);
        searchInput.focus();
    }
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            filterItems();
            searchInput.focus();
        });
    }
});
</script>
@endpush
