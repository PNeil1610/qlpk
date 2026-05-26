@extends('layouts.app')

@section('title', 'Trung Tâm Thông Báo')

@section('content')
<div class="container-fluid px-3 px-md-4 mt-4">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
        <div>
            <h3 class="mb-0 text-primary fw-bold">
                <i class="fas fa-bell me-2"></i>Trung Tâm Thông Báo
            </h3>
            @if($unreadCount > 0)
                <small class="text-muted">Bạn có <span class="fw-bold text-danger">{{ $unreadCount }}</span> thông báo chưa đọc</small>
            @else
                <small class="text-muted">Tất cả đã được đọc</small>
            @endif
        </div>
        @if(!auth()->user()->hasRole('PATIENT'))
        <a href="{{ route('notifications.create') }}" class="btn btn-primary shadow-sm w-100 w-sm-auto">
            <i class="fas fa-paper-plane me-1"></i> Gửi thông báo
        </a>
        @endif
    </div>

    {{-- FILTER TABS --}}
    <div class="filter-tabs-wrapper">
        <div class="filter-tabs-container">
            <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
               class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">
                <i class="fas fa-inbox me-1"></i> Tất cả
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="filter-tab {{ $filter === 'unread' ? 'active' : '' }}">
                <i class="fas fa-circle me-1 text-primary-indicator" style="font-size:0.5rem; vertical-align:middle;"></i>
                Chưa đọc
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill ms-1 font-weight-bold">{{ $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'sent']) }}"
               class="filter-tab {{ $filter === 'sent' ? 'active' : '' }}">
                <i class="fas fa-paper-plane me-1"></i> Đã gửi
            </a>
        </div>
    </div>

    {{-- DANH SÁCH THÔNG BÁO --}}
    <div class="notif-list-container">
        @forelse($notifications as $n)
            @php
                $isRead = $n->isReadBy(auth()->id());
                $isSent = $n->sender_id === auth()->id();
                $roleCode = $n->senderRoleCode;
            @endphp
            <div class="card mb-3 shadow-sm border-0 notif-card {{ !$isRead && !$isSent ? 'unread' : '' }}"
                 onclick="window.location='{{ route('notifications.show', $n->id) }}'">
                <div class="card-body py-3 px-3 px-md-4">
                    <div class="notif-content-wrapper">
                        <div class="notif-main-body">
                            {{-- Người gửi & Đối tượng nhận --}}
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($n->sender?->full_name ?? 'S') }}&size=28&background=random"
                                     class="rounded-circle" width="28" height="28" alt="">
                                <span class="fw-semibold text-dark" style="font-size:0.9rem;">
                                    {{ $n->sender?->full_name ?? 'Hệ thống' }}
                                </span>
                                <span class="badge role-badge-{{ $roleCode }} text-white" style="font-size:0.7rem; font-weight: 600;">
                                    {{ $n->senderRoleName }}
                                </span>
                                {{-- Đối tượng nhận --}}
                                <span class="text-muted d-inline-flex align-items-center" style="font-size:0.8rem; white-space: nowrap;">
                                    <i class="fas fa-arrow-right mx-1 text-black-50" style="font-size: 0.75rem;"></i>
                                    <i class="fas fa-users me-1 text-secondary"></i>{{ $n->targetLabel }}
                                </span>
                            </div>

                            {{-- Tiêu đề --}}
                            <div class="notif-title d-flex align-items-center gap-2">
                                @if(!$isRead && !$isSent)
                                    <span class="badge bg-primary rounded-pill" style="font-size:0.6rem; padding: 4px 8px; font-weight: 700; letter-spacing: 0.05em;">MỚI</span>
                                @endif
                                <span class="fs-6 fw-bold text-dark">{{ $n->title }}</span>
                            </div>

                            {{-- Nội dung preview --}}
                            <div class="text-muted mt-2" style="font-size:0.85rem; line-height:1.5;">
                                {{ Str::limit($n->content, 120) }}
                            </div>
                        </div>

                        {{-- Meta: thời gian + trạng thái --}}
                        <div class="notif-side-meta">
                            <div class="text-muted small">
                                <i class="far fa-clock me-1 text-secondary"></i>
                                {{ $n->notified_at?->diffForHumans() }}
                            </div>
                            
                            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                                @if($n->expires_at)
                                    <div class="text-warning small d-inline-flex align-items-center" style="font-weight: 500;">
                                        <i class="fas fa-hourglass-half me-1"></i>
                                        Hết hạn {{ $n->expires_at->format('d/m/Y') }}
                                    </div>
                                @endif
                                @if($n->status == 0)
                                    <span class="badge bg-secondary">Đã thu hồi</span>
                                @endif
                                
                                {{-- Nút thu hồi (chỉ người gửi / admin) --}}
                                @if(($isSent || auth()->user()->hasRole('ADMIN')) && $n->status == 1)
                                    <form action="{{ route('notifications.destroy', $n->id) }}" method="POST"
                                          onsubmit="return confirm('Thu hồi thông báo này?')" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0 d-inline-flex align-items-center gap-1 border-0 bg-transparent text-decoration-none fw-bold"
                                                onclick="event.stopPropagation()" style="font-size: 0.8rem;">
                                            <i class="fas fa-undo-alt"></i> Thu hồi
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-3 shadow-sm border border-light">
                <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3 fw-semibold">
                    @if($filter === 'unread')
                        Không có thông báo chưa đọc
                    @elseif($filter === 'sent')
                        Bạn chưa gửi thông báo nào
                    @else
                        Không có thông báo nào
                    @endif
                </h5>
            </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
.notif-card {
    border-left: 4px solid var(--border-color) !important;
    transition: var(--transition-smooth) !important;
    cursor: pointer;
}
.notif-card:hover {
    border-left-color: var(--primary-color) !important;
    background: #f8fafc !important;
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover) !important;
}
.notif-card.unread {
    border-left-color: var(--info-color) !important;
    background: rgba(14, 165, 233, 0.03) !important;
}
.notif-card.unread .notif-title {
    font-weight: 700;
}

/* Premium Badge Colors */
.role-badge-ADMIN        { background: var(--danger-color) !important; }
.role-badge-DOCTOR       { background: var(--primary-color) !important; }
.role-badge-CASHIER      { background: var(--success-color) !important; }
.role-badge-RECEPTIONIST { background: #8b5cf6 !important; }
.role-badge-NURSE        { background: var(--warning-color) !important; }
.role-badge-SYSTEM       { background: var(--secondary-color) !important; }

/* Filter Tabs Styling */
.filter-tabs-wrapper {
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.filter-tabs-container {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    white-space: nowrap;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 8px;
    scrollbar-width: none; /* Hide scrollbar for Firefox */
}
.filter-tabs-container::-webkit-scrollbar {
    display: none; /* Hide scrollbar for Chrome, Safari, Opera */
}
.filter-tab {
    white-space: nowrap; /* Prevent word wrapping within a tab */
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border-color);
    background: #ffffff;
    padding: 8px 18px;
    border-radius: 30px;
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 500;
    transition: var(--transition-smooth);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}
.filter-tab.active {
    background: var(--primary-gradient);
    color: #fff !important;
    font-weight: 600;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(26, 41, 128, 0.15);
}
.filter-tab:hover:not(.active) {
    background: #f1f5f9;
    color: var(--text-main);
    border-color: #cbd5e1;
}
.text-primary-indicator {
    color: var(--info-color);
}

/* Semantic Layout Structure */
.notif-content-wrapper {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.notif-main-body {
    width: 100%;
}
.notif-side-meta {
    width: 100%;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid var(--border-color);
    padding-top: 10px;
    gap: 8px;
}

/* Desktop layout styles */
@media (min-width: 768px) {
    .notif-content-wrapper {
        flex-direction: row;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }
    .notif-main-body {
        flex: 1;
        width: auto;
    }
    .notif-side-meta {
        width: auto;
        min-width: 160px;
        flex-direction: column;
        align-items: flex-end;
        justify-content: flex-start;
        border-top: none !important;
        padding-top: 0 !important;
        gap: 6px;
    }
}
</style>
@endpush


