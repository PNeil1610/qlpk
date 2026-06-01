@extends('layouts.app')

@section('title', 'Lịch sử khám bệnh của tôi')

@push('styles')
<style>
    .hist-card {
        border: 1px solid #e5e7eb;
        border-radius: .875rem;
        background: #fff;
        box-shadow: 0 1px 6px rgba(0,0,0,.06);
        overflow: hidden;
        transition: box-shadow .2s, transform .15s;
    }
    .hist-card:hover {
        box-shadow: 0 6px 20px rgba(37,99,235,.12);
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
        background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%);
        color: #fff;
        border-radius: 1rem;
        padding: 1.25rem 1.75rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(37,99,235,.2);
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
    $fromConsultation = request('from_consultation');
@endphp

<div class="container-fluid px-4 mt-4">

    {{-- HEADER --}}
    <div class="page-header-bar d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold text-white">
                <i class="fas fa-history me-2"></i>Lịch sử khám bệnh của tôi
            </h4>   
            <div class="opacity-75" style="font-size:.88rem;">
                <strong>{{ $doctorName }}</strong>
                @if($fromConsultation)
                    &nbsp;·&nbsp; Chọn toa để sử dụng lại cho <strong>Phiếu #{{ $fromConsultation }}</strong>
                @endif
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
                        placeholder="Tìm tên BN, mã BN, số điện thoại, chẩn đoán..."
                        autocomplete="off"
                        value="{{ $keyword ?? '' }}">
                    <button class="btn btn-outline-secondary" id="clearSearch" type="button" title="Xóa tìm kiếm" style="display:none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="col-auto">
                <span class="text-muted small">
                    <span id="visibleCount">{{ $consultations->total() }}</span> phiếu
                </span>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH --}}
    @if($consultations->isEmpty())
        <div style="text-align:center; padding:4rem 1rem; color:#9ca3af;">
            <i class="fas fa-folder-open" style="font-size:3rem; opacity:.3; display:block; margin-bottom:1rem;"></i>
            <div class="fw-semibold" style="font-size:1.05rem;">Bạn chưa kê đơn cho phiếu khám nào</div>
            <div class="small mt-1">Sau khi kê toa thuốc cho bệnh nhân, lịch sử sẽ hiển thị tại đây.</div>
        </div>
    @else
        <div id="historyGrid" class="row g-3">
            @foreach($consultations as $c)
            <div class="col-xl-4 col-lg-6 hist-row-item"
                 data-search="{{ strtolower($c->patient->full_name ?? '') }} {{ strtolower($c->patient->patient_code ?? '') }} {{ strtolower($c->patient->phone_number ?? '') }} {{ strtolower($c->diagnosis_notes ?? '') }}">
                <div class="hist-card h-100">
                    {{-- Header --}}
                    <div class="hist-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:.8rem;">
                                #{{ $c->id }}
                            </span>
                            <span class="small text-muted">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($c->consultation_date)->format('d/m/Y') }}
                            </span>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:.72rem;">Hoàn thành</span>
                    </div>
                    {{-- Body --}}
                    <div class="hist-card-body">
                        <div class="fw-bold mb-1">{{ $c->patient->full_name ?? '---' }}</div>
                        <div class="info-row">
                            <i class="fas fa-id-card"></i>
                            {{ $c->patient->patient_code ?? '--' }}
                            @if($c->patient->phone_number ?? false)
                                &nbsp;·&nbsp;<i class="fas fa-phone"></i> {{ $c->patient->phone_number }}
                            @endif
                        </div>
                        @if($c->diagnosis_notes)
                            <div class="diag-box">
                                <strong style="font-size:.74rem; text-transform:uppercase; color:#d97706;">Chẩn đoán</strong><br>
                                {{ $c->diagnosis_notes }}
                            </div>
                        @endif
                        {{-- Actions --}}
                        <div class="mt-3 d-flex gap-2 flex-wrap">
                            <a href="{{ route('consultations.show', $c->id) }}{{ $fromConsultation ? '?reuse_mode=1&from_consultation='.$fromConsultation : '' }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> phiếu khám
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- No results message --}}
        <div id="no-results">
            <i class="fas fa-search-minus" style="font-size:2.5rem; opacity:.3; display:block; margin-bottom:.75rem;"></i>
            <div class="fw-semibold">Không tìm thấy kết quả</div>
            <div class="small mt-1">Thử tìm kiếm với từ khóa khác</div>
        </div>

        {{-- Pagination (server-side, only shown when no keyword active) --}}
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
    // ===== CLIENT-SIDE SEARCH =====
    const searchInput  = document.getElementById('searchInput');
    const clearBtn     = document.getElementById('clearSearch');
    const items        = document.querySelectorAll('.hist-row-item');
    const noResults    = document.getElementById('no-results');
    const countSpan    = document.getElementById('visibleCount');
    const paginationWrap = document.getElementById('paginationWrap');

    function filterItems() {
        const keyword = searchInput.value.toLowerCase().trim();
        clearBtn.style.display = keyword ? '' : 'none';

        // Hide pagination when filtering
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
        // Auto focus
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
