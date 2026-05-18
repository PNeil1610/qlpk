@extends('layouts.app')

@section('title', 'Trung Tâm Thông Báo')

@push('styles')
<style>
.notif-card {
    border-left: 4px solid #dee2e6;
    transition: all 0.2s ease;
    cursor: pointer;
}
.notif-card:hover {
    border-left-color: #0d6efd;
    background: #f8f9ff !important;
    transform: translateX(2px);
}
.notif-card.unread {
    border-left-color: #0d6efd;
    background: #f0f4ff;
}
.notif-card.unread .notif-title {
    font-weight: 700;
}
.role-badge-ADMIN        { background: #dc3545; }
.role-badge-DOCTOR       { background: #0d6efd; }
.role-badge-CASHIER      { background: #198754; }
.role-badge-RECEPTIONIST { background: #6610f2; }
.role-badge-NURSE        { background: #fd7e14; }
.role-badge-SYSTEM       { background: #6c757d; }
.filter-tab {
    border: none;
    background: none;
    padding: 8px 18px;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #6c757d;
    transition: all 0.2s;
}
.filter-tab.active {
    background: #0d6efd;
    color: #fff;
    font-weight: 600;
}
.filter-tab:hover:not(.active) {
    background: #e9ecef;
    color: #333;
}
</style>
@endpush

@section('content')
<div class="container-fluid px-4 mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 text-primary">
                <i class="fas fa-bell me-2"></i>Trung Tâm Thông Báo
            </h3>
            @if($unreadCount > 0)
                <small class="text-muted">Bạn có <span class="fw-bold text-danger">{{ $unreadCount }}</span> thông báo chưa đọc</small>
            @else
                <small class="text-muted">Tất cả đã được đọc</small>
            @endif
        </div>
        @if(!auth()->user()->hasRole('PATIENT'))
        <a href="{{ route('notifications.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-paper-plane me-1"></i> Gửi thông báo
        </a>
        @endif
    </div>

    {{-- FILTER TABS --}}
    <div class="d-flex gap-2 mb-4">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
           class="filter-tab {{ $filter === 'all' ? 'active' : '' }}">
            <i class="fas fa-inbox me-1"></i> Tất cả
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
           class="filter-tab {{ $filter === 'unread' ? 'active' : '' }}">
            <i class="fas fa-circle me-1 text-primary" style="font-size:0.6rem; vertical-align:middle;"></i>
            Chưa đọc
            @if($unreadCount > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'sent']) }}"
           class="filter-tab {{ $filter === 'sent' ? 'active' : '' }}">
            <i class="fas fa-paper-plane me-1"></i> Đã gửi
        </a>
    </div>

    {{-- DANH SÁCH THÔNG BÁO --}}
    @forelse($notifications as $n)
        @php
            $isRead = $n->isReadBy(auth()->id());
            $isSent = $n->sender_id === auth()->id();
            $roleCode = $n->senderRoleCode;
        @endphp
        <div class="card mb-2 shadow-sm border-0 notif-card {{ !$isRead && !$isSent ? 'unread' : '' }}"
             onclick="window.location='{{ route('notifications.show', $n->id) }}'">
            <div class="card-body py-3 px-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        {{-- Người gửi --}}
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($n->sender?->full_name ?? 'S') }}&size=28&background=random"
                                 class="rounded-circle" width="28" height="28" alt="">
                            <span class="fw-semibold text-dark" style="font-size:0.9rem;">
                                {{ $n->sender?->full_name ?? 'Hệ thống' }}
                            </span>
                            <span class="badge role-badge-{{ $roleCode }} text-white" style="font-size:0.7rem;">
                                {{ $n->senderRoleName }}
                            </span>
                            {{-- Đối tượng nhận --}}
                            <span class="text-muted" style="font-size:0.8rem;">
                                → <i class="fas fa-users me-1"></i>{{ $n->targetLabel }}
                            </span>
                        </div>

                        {{-- Tiêu đề --}}
                        <div class="notif-title d-flex align-items-center gap-2">
                            @if(!$isRead && !$isSent)
                                <span class="badge bg-primary rounded-pill" style="font-size:0.6rem; padding: 3px 6px;">MỚI</span>
                            @endif
                            <span class="fs-6">{{ $n->title }}</span>
                        </div>

                        {{-- Nội dung preview --}}
                        <div class="text-muted mt-1" style="font-size:0.85rem; line-height:1.4;">
                            {{ Str::limit($n->content, 120) }}
                        </div>
                    </div>

                    {{-- Meta: thời gian + trạng thái --}}
                    <div class="text-end ms-3 flex-shrink-0" style="min-width:130px;">
                        <div class="text-muted small">
                            <i class="far fa-clock me-1"></i>
                            {{ $n->notified_at?->diffForHumans() }}
                        </div>
                        @if($n->expires_at)
                            <div class="text-warning small mt-1">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Hết hạn {{ $n->expires_at->format('d/m/Y') }}
                            </div>
                        @endif
                        @if($n->status == 0)
                            <span class="badge bg-secondary mt-1">Đã thu hồi</span>
                        @endif

                        {{-- Nút thu hồi (chỉ người gửi / admin) --}}
                        @if($isSent || auth()->user()->hasRole('ADMIN'))
                            @if($n->status == 1)
                            <form action="{{ route('notifications.destroy', $n->id) }}" method="POST"
                                  onsubmit="return confirm('Thu hồi thông báo này?')" class="mt-1">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link btn-sm text-danger p-0"
                                        onclick="event.stopPropagation()">
                                    <i class="fas fa-undo-alt me-1"></i>Thu hồi
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">
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

    {{-- PAGINATION --}}
    @if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
    @endif

</div>
@endsection
