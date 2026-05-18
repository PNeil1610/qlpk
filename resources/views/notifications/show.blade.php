@extends('layouts.app')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-bell me-2"></i>Chi tiết thông báo</h3>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($notification->sender?->full_name ?? 'S') }}&size=44&background=random"
                             class="rounded-circle" width="44" height="44" alt="">
                        <div>
                            <div class="fw-bold">{{ $notification->sender?->full_name ?? 'Hệ thống' }}</div>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-primary" style="font-size:0.75rem;">
                                    {{ $notification->senderRoleName }}
                                </span>
                                <span class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $notification->notified_at?->format('d/m/Y H:i') }}
                                    ({{ $notification->notified_at?->diffForHumans() }})
                                </span>
                            </div>
                        </div>
                        <div class="ms-auto text-end">
                            <span class="badge
                                @if($notification->target_type === 'all') bg-danger
                                @elseif($notification->target_type === 'role') bg-warning text-dark
                                @else bg-info text-dark @endif">
                                <i class="fas fa-users me-1"></i>{{ $notification->targetLabel }}
                            </span>
                            @if($notification->expires_at)
                            <div class="text-muted small mt-1">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Hết hạn: {{ $notification->expires_at->format('d/m/Y H:i') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-dark">{{ $notification->title }}</h4>
                    <div class="text-dark" style="line-height:1.8; white-space: pre-line;">
                        {{ $notification->content }}
                    </div>
                </div>

                @if($notification->status == 0)
                <div class="card-footer bg-light">
                    <span class="badge bg-secondary">
                        <i class="fas fa-ban me-1"></i>Thông báo đã bị thu hồi
                    </span>
                </div>
                @endif
            </div>

            {{-- Nút thu hồi nếu là người gửi/admin --}}
            @if(($notification->sender_id === auth()->id() || auth()->user()->hasRole('ADMIN')) && $notification->status == 1)
            <div class="mt-3 text-end">
                <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                      onsubmit="return confirm('Xác nhận thu hồi thông báo này?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-undo-alt me-1"></i>Thu hồi thông báo
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
