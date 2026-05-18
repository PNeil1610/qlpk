<div class="sidebar d-flex flex-column" style="height: 100vh; background: #222;">
    <div class="clinic-brand-info text-center p-3 border-bottom border-secondary mb-3">
        @if(isset($clinicSetting) && $clinicSetting->logo_url)
            <img src="{{ asset($clinicSetting->logo_url) }}" alt="Logo" class="img-fluid rounded-circle mb-2 bg-white p-1"
                style="width: 70px; height: 70px; object-fit: contain;">
        @endif

        <h5 class="text-white fw-bold mb-2" style="line-height: 1.4;">
            {{ $clinicSetting->clinic_name ?? 'Đang cập nhật tên...' }}
        </h5>
    </div>

    <h4 class="p-3">MENU</h4>

    {{-- ===== ADMIN & RECEPTIONIST: Quản lý bệnh nhân, phiên khám, xếp phòng ===== --}}
    @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('RECEPTIONIST')))
        <a href="{{ route('patients.index') }}">👤 Bệnh nhân</a>

        <a data-bs-toggle="collapse" href="#consultationsMenu" role="button" aria-expanded="false" aria-controls="consultationsMenu" class="d-flex justify-content-between align-items-center">
            <span>🩺 Phiên khám</span>
            <i class="fas fa-chevron-down ms-2" style="font-size: 0.75rem;"></i>
        </a>
        <div class="collapse" id="consultationsMenu">
            <a href="{{ route('consultations.index') }}" class="ps-4 py-2 text-white-50" style="font-size: 0.9rem; border-left: 2px solid #555; margin-left: 15px;">
                Phiên khám tổng
            </a>
            <a href="{{ route('consultations.index', ['filter' => 'no_room']) }}" class="ps-4 py-2 text-warning" style="font-size: 0.9rem; border-left: 2px solid #ffc107; margin-left: 15px;">
                Phiên khám chờ
            </a>
        </div>

        <a href="{{ route('consultation_rooms.index') }}">🏥 Sắp xếp phòng khám</a>
    @endif

    {{-- ===== ADMIN & DOCTOR: Ca khám của tôi ===== --}}
    @if(auth()->check() && auth()->user()->hasRole('DOCTOR'))
        <a href="{{ route('consultations.doctor') }}">👨‍⚕️ Ca khám của tôi</a>
        <a href="{{ route('doctor.templates.index') }}">📋 Toa thuốc mẫu</a>
        <a href="{{ route('diagnoses.index') }}">📖 Danh mục mã bệnh</a>
        <a href="{{ route('medicine_categories.index') }}">💊 Danh mục loại thuốc</a>
        <a href="{{ route('medicines.index') }}">💊 Danh mục thuốc</a>
    @endif

    {{-- ===== ADMIN: Quản lý nhân sự ===== --}}
    @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
        <a href="{{ route('staff.index') }}">👩‍⚕️ Nhân viên</a>
        <a href="{{ route('users.index') }}">🔑 Quản lý tài khoản</a>
        <a href="{{ route('settings.index') }}">⚙️ Cấu hình phòng khám</a>
    @endif

    {{-- ===== ADMIN: Danh mục hệ thống ===== --}}
    @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
        <a href="{{ route('diagnoses.index') }}">📖 Danh mục mã bệnh</a>
        <a href="{{ route('prescriptions.index') }}">💊 Đơn thuốc</a>
        <a href="{{ route('medicine_categories.index') }}">💊 Danh mục loại thuốc</a>
        <a href="{{ route('medicines.index') }}">💊 Danh mục thuốc</a>
        <a href="{{ route('departments.index') }}">🗂️ Danh mục khoa</a>
        <a href="{{ route('rooms.index') }}">🏥 Quản lý phòng</a>
        <a href="{{ route('room_details.index') }}">🏥 Chi tiết phòng</a>
    @endif

    {{-- ===== ADMIN & CASHIER: Hóa đơn ===== --}}
    @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('CASHIER')))
        <a href="{{ route('invoices.index') }}">💰 Hóa đơn</a>
    @endif

    {{-- ===== TẤT CẢ (trừ PATIENT): Thông báo ===== --}}
    @if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
        <a href="{{ route('notifications.index') }}" id="sidebar-notif-link" class="d-flex justify-content-between align-items-center">
            <span>🔔 Thông báo</span>
            <span class="badge bg-danger rounded-pill" id="notif-badge" style="display:none; font-size:0.7rem;"></span>
        </a>
    @endif

    {{-- ===== PATIENT: Hồ sơ bệnh án ===== --}}
    @if(auth()->check() && auth()->user()->hasRole('PATIENT'))
        <a href="{{ route('patients.my_record') }}">📋 Hồ sơ bệnh án</a>
    @endif

    <div class="mt-auto clinic-contact-info p-3 border-top border-secondary">
        <div style="font-size: 0.85rem; color: #adb5bd; line-height: 1.5; text-align: left;">
            @if(isset($clinicSetting) && $clinicSetting->address)
                <div class="mb-1">📍 {{ $clinicSetting->address }}</div>
            @endif

            @if(isset($clinicSetting) && $clinicSetting->phone_number)
                <div>📞 <strong>{{ $clinicSetting->phone_number }}</strong></div>
            @endif
        </div>
    </div>
</div>

@if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
<script>
function refreshNotifBadge() {
    $.getJSON('{{ route('notifications.ajax.unread') }}', function(data) {
        var count = data.count || 0;
        var badge = document.getElementById('notif-badge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
    }).fail(function() {
        // Silently ignore
    });
}
// Gọi ngay khi load
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        refreshNotifBadge();
        setInterval(refreshNotifBadge, 60000); // Polling mỗi 60 giây
    });
}
</script>
@endif