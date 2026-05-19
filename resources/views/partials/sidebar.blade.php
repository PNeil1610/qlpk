<style>
    .sidebar {
        background: linear-gradient(180deg, #1A2980 0%, #26D0CE 100%) !important;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
    }
    .sidebar::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
    }
    
    .clinic-brand-info {
        background: rgba(0,0,0,0.15);
        border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    }
    .clinic-contact-info {
        background: rgba(0,0,0,0.2);
        border-top: 1px solid rgba(255,255,255,0.1) !important;
    }
    
    .sidebar-menu-title {
        color: rgba(255,255,255,0.6);
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        padding: 1.2rem 1.5rem 0.5rem;
        margin: 0;
    }

    .sidebar a {
        color: rgba(255,255,255,0.85) !important;
        padding: 0.8rem 1.5rem !important;
        display: flex;
        align-items: center;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        margin-bottom: 2px;
    }
    
    .sidebar a:hover, .sidebar a.active {
        color: #ffffff !important;
        background: rgba(255,255,255,0.1) !important;
        border-left-color: #ffc107 !important;
    }
    
    .sidebar a i.menu-icon {
        width: 28px;
        font-size: 1.1rem;
        margin-right: 8px;
        text-align: center;
        color: rgba(255,255,255,0.7);
        transition: color 0.2s;
    }
    
    .sidebar a:hover i.menu-icon, .sidebar a.active i.menu-icon {
        color: #ffc107;
    }
</style>

<div class="d-flex flex-column h-100">
    <div class="clinic-brand-info text-center p-4 mb-2">
        @if(isset($clinicSetting) && $clinicSetting->logo_url)
            <img src="{{ asset($clinicSetting->logo_url) }}" alt="Logo" class="img-fluid rounded-circle mb-3 bg-white p-1 shadow-sm"
                style="width: 80px; height: 80px; object-fit: contain;">
        @else
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-3 text-primary" style="width: 80px; height: 80px; font-size: 2rem;">
                <i class="fas fa-hospital-alt"></i>
            </div>
        @endif

        <h6 class="text-white fw-bold mb-0" style="line-height: 1.4; letter-spacing: 0.5px;">
            {{ $clinicSetting->clinic_name ?? 'QL PHÒNG KHÁM' }}
        </h6>
    </div>

    <div class="flex-grow-1 pb-4">
        {{-- ===== ADMIN & RECEPTIONIST: Quản lý bệnh nhân, phiên khám, xếp phòng ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('RECEPTIONIST')))
            <h6 class="sidebar-menu-title">Lễ tân & Khám Bệnh</h6>
            <a href="{{ route('patients.index') }}">
                <i class="fas fa-users menu-icon"></i> Bệnh nhân
            </a>

            <a data-bs-toggle="collapse" href="#consultationsMenu" role="button" aria-expanded="false" aria-controls="consultationsMenu" class="d-flex justify-content-between align-items-center">
                <div><i class="fas fa-stethoscope menu-icon"></i> Phiên khám</div>
                <i class="fas fa-chevron-down" style="font-size: 0.75rem;"></i>
            </a>
            <div class="collapse" id="consultationsMenu">
                <a href="{{ route('consultations.index') }}" style="padding-left: 3.5rem !important; font-size: 0.85rem;">
                    Tất cả phiên khám
                </a>
                <a href="{{ route('consultations.index', ['filter' => 'no_room']) }}" class="text-warning" style="padding-left: 3.5rem !important; font-size: 0.85rem;">
                    Phiên khám chờ
                </a>
            </div>

            <a href="{{ route('consultation_rooms.index') }}">
                <i class="fas fa-door-open menu-icon"></i> Sắp xếp phòng khám
            </a>
        @endif

        {{-- ===== ADMIN & DOCTOR: Ca khám của tôi ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('DOCTOR'))
            <h6 class="sidebar-menu-title">Khu Vực Bác Sĩ</h6>
            <a href="{{ route('consultations.doctor') }}">
                <i class="fas fa-user-md menu-icon"></i> Ca khám của tôi
            </a>
            <a href="{{ route('doctor.my_history') }}">
                <i class="fas fa-history menu-icon"></i> Lịch sử khám
            </a>
            <a href="{{ route('doctor.templates.index') }}">
                <i class="fas fa-file-prescription menu-icon"></i> Toa thuốc mẫu
            </a>
            
            {{-- Cho bác sĩ xem danh mục nếu không phải admin --}}
            @if(!auth()->user()->hasRole('ADMIN'))
                <a href="{{ route('diagnoses.index') }}">
                    <i class="fas fa-book-medical menu-icon"></i> Danh mục mã bệnh
                </a>
                <a href="{{ route('medicine_categories.index') }}">
                    <i class="fas fa-pills menu-icon"></i> Nhóm thuốc
                </a>
                <a href="{{ route('medicines.index') }}">
                    <i class="fas fa-capsules menu-icon"></i> Danh mục thuốc
                </a>
            @endif
        @endif

        {{-- ===== ADMIN: Quản lý nhân sự ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
            <h6 class="sidebar-menu-title">Quản Lý Nhân Sự</h6>
            <a href="{{ route('staff.index') }}">
                <i class="fas fa-user-nurse menu-icon"></i> Nhân viên
            </a>
            <a href="{{ route('users.index') }}">
                <i class="fas fa-users-cog menu-icon"></i> Tài khoản
            </a>
            <a href="{{ route('settings.index') }}">
                <i class="fas fa-cogs menu-icon"></i> Cấu hình hệ thống
            </a>
        @endif

        {{-- ===== ADMIN: Danh mục hệ thống ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
            <h6 class="sidebar-menu-title">Dashboard</h6>
            <a href="{{ route('dashboard') }}">
                <i class="fas fa-chart-line menu-icon"></i> Thống kê phòng khám
            </a>
            <h6 class="sidebar-menu-title">Danh Mục Hệ Thống</h6>
            <a href="{{ route('diagnoses.index') }}">
                <i class="fas fa-book-medical menu-icon"></i> Mã bệnh ICD-10
            </a>
            <a href="{{ route('prescriptions.index') }}">
                <i class="fas fa-receipt menu-icon"></i> Danh sách đơn thuốc
            </a>
            <a href="{{ route('medicine_categories.index') }}">
                <i class="fas fa-pills menu-icon"></i> Loại hình thuốc
            </a>
            <a href="{{ route('medicines.index') }}">
                <i class="fas fa-capsules menu-icon"></i> Danh mục thuốc
            </a>
            <a href="{{ route('departments.index') }}">
                <i class="fas fa-layer-group menu-icon"></i> Khoa phòng
            </a>
            <a href="{{ route('rooms.index') }}">
                <i class="fas fa-hospital menu-icon"></i> Quản lý phòng
            </a>
            <a href="{{ route('room_details.index') }}">
                <i class="fas fa-bed menu-icon"></i> Chi tiết phòng
            </a>
        @endif

        {{-- ===== ADMIN & CASHIER: Hóa đơn ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('CASHIER')))
            <h6 class="sidebar-menu-title">Tài Chính</h6>
            <a href="{{ route('invoices.index') }}">
                <i class="fas fa-file-invoice-dollar menu-icon"></i> Hóa đơn thanh toán
            </a>
        @endif

        {{-- ===== PHARMACIST: Kho Dược & Cấp Phát ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('PHARMACIST'))
            <h6 class="sidebar-menu-title">Khu Vực Dược Sĩ</h6>
            <a href="{{ route('prescriptions.index') }}">
                <i class="fas fa-receipt menu-icon"></i> Danh sách đơn thuốc
            </a>
            <a href="{{ route('medicine_categories.index') }}">
                <i class="fas fa-pills menu-icon"></i> Loại hình thuốc
            </a>
            <a href="{{ route('medicines.index') }}">
                <i class="fas fa-capsules menu-icon"></i> Danh mục thuốc
            </a>
        @endif

        {{-- ===== TẤT CẢ (trừ PATIENT): Thông báo ===== --}}
        @if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
            <h6 class="sidebar-menu-title">Khác</h6>
            <a href="{{ route('notifications.index') }}" id="sidebar-notif-link" class="d-flex justify-content-between align-items-center">
                <div><i class="fas fa-bell menu-icon"></i> Thông báo</div>
                <span class="badge bg-danger rounded-pill" id="notif-badge" style="display:none; font-size:0.7rem;"></span>
            </a>
        @endif

        {{-- ===== PATIENT: Hồ sơ bệnh án ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('PATIENT'))
            <h6 class="sidebar-menu-title">Bệnh Nhân</h6>
            <a href="{{ route('patients.my_record') }}">
                <i class="fas fa-notes-medical menu-icon"></i> Hồ sơ bệnh án
            </a>
        @endif
    </div>

    <div class="clinic-contact-info p-3 mt-auto">
        <div style="font-size: 0.8rem; color: rgba(255,255,255,0.7); line-height: 1.6; text-align: left;">
            @if(isset($clinicSetting) && $clinicSetting->address)
                <div class="d-flex mb-1">
                    <i class="fas fa-map-marker-alt mt-1 me-2" style="width: 12px; text-align: center;"></i> 
                    <span>{{ $clinicSetting->address }}</span>
                </div>
            @endif

            @if(isset($clinicSetting) && $clinicSetting->phone_number)
                <div class="d-flex">
                    <i class="fas fa-phone-alt mt-1 me-2" style="width: 12px; text-align: center;"></i>
                    <strong class="text-white">{{ $clinicSetting->phone_number }}</strong>
                </div>
            @endif
        </div>
    </div>
</div>

@if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
<script>
function refreshNotifBadge() {
    $.getJSON('{{ route("notifications.ajax.unread") }}', function(data) {
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