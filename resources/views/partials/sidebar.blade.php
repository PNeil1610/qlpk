<style>
    .sidebar, .offcanvas-sidebar {
        background: linear-gradient(180deg, #1A2980 0%, #26D0CE 100%) !important;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        overflow-y: auto;
    }
    .sidebar::-webkit-scrollbar, .offcanvas-sidebar::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar::-webkit-scrollbar-thumb, .offcanvas-sidebar::-webkit-scrollbar-thumb {
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

    .sidebar a, .offcanvas-sidebar a {
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
    
    .sidebar a:hover, .sidebar a.active, .offcanvas-sidebar a:hover, .offcanvas-sidebar a.active {
        color: #ffffff !important;
        background: rgba(255,255,255,0.1) !important;
        border-left-color: #ffc107 !important;
    }
    
    .sidebar a i.menu-icon, .offcanvas-sidebar a i.menu-icon {
        width: 28px;
        font-size: 1.1rem;
        margin-right: 8px;
        text-align: center;
        color: rgba(255,255,255,0.7);
        transition: color 0.2s;
    }
    
    .sidebar a:hover i.menu-icon, .sidebar a.active i.menu-icon, .offcanvas-sidebar a:hover i.menu-icon, .offcanvas-sidebar a.active i.menu-icon {
        color: #ffc107;
    }

    /* Collapsible Submenus Style */
    .sidebar-submenu {
        background: rgba(0,0,0,0.12);
        border-left: 3px solid rgba(255,255,255,0.15);
        margin-left: 1.2rem;
        margin-right: 0.8rem;
        border-radius: 6px;
        margin-bottom: 8px;
        padding: 4px 0;
    }
    .sidebar-submenu a {
        padding: 0.6rem 1.2rem !important;
        font-size: 0.88rem !important;
        border-left: none !important;
        margin-bottom: 0 !important;
        border-radius: 4px;
    }
    .sidebar-submenu a:hover, .sidebar-submenu a.active {
        background: rgba(255,255,255,0.15) !important;
        color: #ffc107 !important;
    }
    
    .toggle-arrow {
        transition: transform 0.2s ease;
        font-size: 0.78rem;
        opacity: 0.8;
        margin-left: auto;
    }
    a[aria-expanded="true"] .toggle-arrow {
        transform: rotate(180deg);
        opacity: 1;
        color: #ffc107 !important;
    }
</style>

<div class="d-flex flex-column h-100">
    <div class="clinic-brand-info text-center p-4 mb-2">
        @if(isset($clinicSetting) && $clinicSetting->logo_data)
            <img src="{{ $clinicSetting->logo_data }}"
                 alt="Logo {{ $clinicSetting->clinic_name }}"
                 class="mb-3 shadow-sm"
                 style="width: 80px; height: 80px; object-fit: contain; border-radius: 12px; background: rgba(255,255,255,0.15); padding: 6px;">
        @else
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm mb-3 text-primary" style="width: 80px; height: 80px; font-size: 2rem;">
                <i class="fas fa-hospital-alt"></i>
            </div>
        @endif

        <h6 class="text-white fw-bold mb-0" style="line-height: 1.4; letter-spacing: 0.5px;">
            {{ $clinicSetting->clinic_name ?? 'QL PHÒNG KHÁM' }}
        </h6>
    </div>

    @php
        // Tính toán trạng thái Active cho các nhóm menu collapsible
        $prefix = $sidebarIdPrefix ?? 'sidebar';

        $isDoctor = auth()->check() && auth()->user()->hasRole('DOCTOR');

        $isClinicalActive = request()->routeIs('consultations.doctor*') 
            || request()->routeIs('doctor.my_history') 
            || request()->routeIs('doctor.templates.*')
            || (request()->routeIs('consultations.show') && $isDoctor);

        $isReceptionActive = request()->routeIs('patients.*') 
            || (request()->routeIs('consultations.*') 
                && !request()->routeIs('consultations.doctor*') 
                && !request()->routeIs('doctor.my_history')
                && !(request()->routeIs('consultations.show') && $isDoctor)
               ) 
            || request()->routeIs('consultation_rooms.*');

        $isPharmacyActive = request()->routeIs('prescriptions.*') 
            || request()->routeIs('medicine_categories.*') 
            || request()->routeIs('medicines.*')
            || request()->routeIs('diagnoses.*');

        $isFacilityActive = request()->routeIs('departments.*') 
            || request()->routeIs('rooms.*') 
            || request()->routeIs('room_details.*');

        $isAdminActive = request()->routeIs('staff.*') 
            || request()->routeIs('users.*') 
            || request()->routeIs('settings.*') 
            || request()->routeIs('dashboard');
    @endphp

    <div class="flex-grow-1 pb-4">
        {{-- ===== ADMIN & RECEPTIONIST: Lễ tân & Khám Bệnh ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('RECEPTIONIST')))
            <!-- <h6 class="sidebar-menu-title">Lễ tân & Khám Bệnh</h6> -->
            
            <a href="#{{ $prefix }}_receptionMenu" role="button" aria-expanded="{{ $isReceptionActive ? 'true' : 'false' }}" aria-controls="{{ $prefix }}_receptionMenu" class="d-flex justify-content-between align-items-center {{ $isReceptionActive ? 'active' : 'collapsed' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-stethoscope menu-icon"></i> 
                    <span>Nghiệp vụ phòng khám</span>
                </div>
                <i class="fas fa-chevron-down toggle-arrow"></i>
            </a>
            <div class="collapse {{ $isReceptionActive ? 'show' : '' }}" id="{{ $prefix }}_receptionMenu">
                <div class="sidebar-submenu">
                    <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') && !request()->routeIs('patients.my_record') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i> Bệnh nhân
                    </a>
                    <a href="{{ route('consultations.index') }}" class="{{ request()->routeIs('consultations.index') && !request()->has('filter') ? 'active' : '' }}">
                        <i class="fas fa-notes-medical me-2"></i> Tất cả phiên khám
                    </a>
                    <a href="{{ route('consultations.index', ['filter' => 'no_room']) }}" class="text-warning {{ request()->routeIs('consultations.index') && request()->get('filter') === 'no_room' ? 'active' : '' }}">
                        <i class="fas fa-clock me-2"></i> Phiên khám chờ
                    </a>
                    <a href="{{ route('consultation_rooms.index') }}" class="{{ request()->routeIs('consultation_rooms.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open me-2"></i> Sắp xếp phòng khám
                    </a>
                </div>
            </div>
        @endif

        {{-- ===== DOCTOR & ADMIN: Khu Vực Lâm Sàng & Bác Sĩ ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('DOCTOR') || auth()->user()->hasRole('ADMIN')))
            @if(auth()->user()->hasRole('DOCTOR'))
                <!-- <h6 class="sidebar-menu-title">Khu Vực Bác Sĩ</h6> -->
                
                <a href="#{{ $prefix }}_clinicalMenu" role="button" aria-expanded="{{ $isClinicalActive ? 'true' : 'false' }}" aria-controls="{{ $prefix }}_clinicalMenu" class="d-flex justify-content-between align-items-center {{ $isClinicalActive ? 'active' : 'collapsed' }}">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-md menu-icon"></i> 
                        <span>Nghiệp vụ lâm sàng</span>
                    </div>
                    <i class="fas fa-chevron-down toggle-arrow"></i>
                </a>
                <div class="collapse {{ $isClinicalActive ? 'show' : '' }}" id="{{ $prefix }}_clinicalMenu">
                    <div class="sidebar-submenu">
                        <a href="{{ route('consultations.doctor') }}" class="{{ request()->routeIs('consultations.doctor') ? 'active' : '' }}">
                            <i class="fas fa-stethoscope me-2"></i> Ca khám của tôi
                        </a>
                        <a href="{{ route('doctor.my_history') }}" class="{{ request()->routeIs('doctor.my_history') ? 'active' : '' }}">
                            <i class="fas fa-history me-2"></i> Lịch sử khám
                        </a>
                        <a href="{{ route('doctor.templates.index') }}" class="{{ request()->routeIs('doctor.templates.*') ? 'active' : '' }}">
                            <i class="fas fa-file-prescription me-2"></i> Toa thuốc mẫu
                        </a>
                    </div>
                </div>
            @endif
        @endif

        {{-- ===== ADMIN, PHARMACIST, DOCTOR: Kho Dược & Thuốc ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('PHARMACIST') || auth()->user()->hasRole('DOCTOR')))
            <!-- <h6 class="sidebar-menu-title">Kho Dược & Đơn Thuốc</h6> -->
            
            <a href="#{{ $prefix }}_pharmacyMenu" role="button" aria-expanded="{{ $isPharmacyActive ? 'true' : 'false' }}" aria-controls="{{ $prefix }}_pharmacyMenu" class="d-flex justify-content-between align-items-center {{ $isPharmacyActive ? 'active' : 'collapsed' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-pills menu-icon"></i> 
                    <span>Quản lý kho dược</span>
                </div>
                <i class="fas fa-chevron-down toggle-arrow"></i>
            </a>
            <div class="collapse {{ $isPharmacyActive ? 'show' : '' }}" id="{{ $prefix }}_pharmacyMenu">
                <div class="sidebar-submenu">
                    @if(auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('PHARMACIST'))
                        <a href="{{ route('prescriptions.index') }}" class="{{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
                            <i class="fas fa-receipt me-2"></i> Đơn thuốc kê bác sĩ
                        </a>
                    @endif
                    <a href="{{ route('medicine_categories.index') }}" class="{{ request()->routeIs('medicine_categories.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group me-2"></i> Loại hình thuốc
                    </a>
                    <a href="{{ route('medicines.index') }}" class="{{ request()->routeIs('medicines.*') ? 'active' : '' }}">
                        <i class="fas fa-capsules me-2"></i> Danh mục thuốc
                    </a>
                    @if(auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('DOCTOR'))
                        <a href="{{ route('diagnoses.index') }}" class="{{ request()->routeIs('diagnoses.*') ? 'active' : '' }}">
                            <i class="fas fa-book-medical me-2"></i> Mã bệnh ICD-10
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- ===== ADMIN: Quản lý Cơ sở vật chất ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
            <!-- <h6 class="sidebar-menu-title">Cơ Sở Vật Chất</h6> -->
            
            <a href="#{{ $prefix }}_facilityMenu" role="button" aria-expanded="{{ $isFacilityActive ? 'true' : 'false' }}" aria-controls="{{ $prefix }}_facilityMenu" class="d-flex justify-content-between align-items-center {{ $isFacilityActive ? 'active' : 'collapsed' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-hospital menu-icon"></i> 
                    <span>Cơ cấu khoa phòng</span>
                </div>
                <i class="fas fa-chevron-down toggle-arrow"></i>
            </a>
            <div class="collapse {{ $isFacilityActive ? 'show' : '' }}" id="{{ $prefix }}_facilityMenu">
                <div class="sidebar-submenu">
                    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <i class="fas fa-network-wired me-2"></i> Danh sách khoa phòng
                    </a>
                    <a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        <i class="fas fa-door-open me-2"></i> Danh sách phòng khám
                    </a>
                    <a href="{{ route('room_details.index') }}" class="{{ request()->routeIs('room_details.*') ? 'active' : '' }}">
                        <i class="fas fa-bed me-2"></i> Chi tiết phòng khám
                    </a>
                </div>
            </div>
        @endif

        {{-- ===== ADMIN: Quản trị Hệ Thống & Nhân Sự ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('ADMIN'))
            <!-- <h6 class="sidebar-menu-title">Hệ Thống & Nhân Sự</h6> -->
            
            <a href="#{{ $prefix }}_adminMenu" role="button" aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}" aria-controls="{{ $prefix }}_adminMenu" class="d-flex justify-content-between align-items-center {{ $isAdminActive ? 'active' : 'collapsed' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-users-cog menu-icon"></i> 
                    <span>Quản trị hệ thống</span>
                </div>
                <i class="fas fa-chevron-down toggle-arrow"></i>
            </a>
            <div class="collapse {{ $isAdminActive ? 'show' : '' }}" id="{{ $prefix }}_adminMenu">
                <div class="sidebar-submenu">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line me-2"></i> Thống kê báo cáo
                    </a>
                    <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
                        <i class="fas fa-user-nurse me-2"></i> Quản lý nhân viên
                    </a>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-2"></i> Danh sách tài khoản
                    </a>
                    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cogs me-2"></i> Thiết lập phòng khám
                    </a>
                </div>
            </div>
        @endif

        {{-- ===== ADMIN & CASHIER: Hóa đơn ===== --}}
        @if(auth()->check() && (auth()->user()->hasRole('ADMIN') || auth()->user()->hasRole('CASHIER')))
            <!-- <h6 class="sidebar-menu-title">Tài Chính</h6> -->
            <a href="{{ route('invoices.index') }}" class="{{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <i class="fas fa-file-invoice-dollar menu-icon"></i> Hóa đơn thanh toán
            </a>
        @endif

        {{-- ===== TẤT CẢ (trừ PATIENT): Thông báo ===== --}}
        @if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
            @php
                $initialUnreadCount = \App\Models\SystemNotification::visibleTo(auth()->user())
                    ->whereDoesntHave('reads', function ($q) {
                        $q->where('user_id', auth()->id());
                    })->count();
            @endphp
            <!-- <h6 class="sidebar-menu-title">Khác</h6> -->
            <a href="{{ route('notifications.index') }}" id="sidebar-notif-link" class="d-flex justify-content-between align-items-center {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <div class="d-flex align-items-center">
                    <span class="position-relative d-inline-block" style="width: 28px; margin-right: 8px;">
                        <i class="fas fa-bell menu-icon" style="width: 100%; margin-right: 0;"></i>
                        <span class="position-absolute bg-danger border border-light rounded-circle" id="notif-dot" style="display: {{ $initialUnreadCount > 0 ? 'inline-block' : 'none' }}; width: 8px; height: 8px; top: 1px; right: 2px;"></span>
                    </span>
                    <span>Thông báo</span>
                </div>
                <span class="badge bg-danger rounded-pill" id="notif-badge" style="display: {{ $initialUnreadCount > 0 ? 'inline-block' : 'none' }}; font-size:0.7rem;">{{ $initialUnreadCount > 99 ? '99+' : $initialUnreadCount }}</span>
            </a>
        @endif

        {{-- ===== PATIENT: Hồ sơ bệnh án ===== --}}
        @if(auth()->check() && auth()->user()->hasRole('PATIENT'))
            <!-- <h6 class="sidebar-menu-title">Bệnh Nhân</h6> -->
            <a href="{{ route('patients.my_record') }}" class="{{ request()->routeIs('patients.my_record') ? 'active' : '' }}">
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

@push('scripts')
<script>
if (typeof window.sidebarScriptsInitialized === 'undefined') {
    window.sidebarScriptsInitialized = true;

    $(document).ready(function() {
        // 1. Quản lý Đóng/Mở trượt mượt mà cho các submenus Sidebar (Chống xung đột Double-loaded Bootstrap JS)
        $(document).on('click', '.sidebar a[href^="#"], .offcanvas-sidebar a[href^="#"]', function(e) {
            var targetSelector = $(this).attr('href');
            if (!targetSelector || !$(targetSelector).hasClass('collapse')) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            var $this = $(this);
            var $target = $(targetSelector);
            var isOpen = $target.hasClass('show');
            
            if (isOpen) {
                $target.slideUp(200, function() {
                    $target.removeClass('show');
                    $this.attr('aria-expanded', 'false').addClass('collapsed');
                });
            } else {
                $target.hide().addClass('show').slideDown(200, function() {
                    $this.attr('aria-expanded', 'true').removeClass('collapsed');
                });
            }
        });

        // 2. Tự động làm mới số lượng thông báo chưa đọc (nếu không phải Patient)
        @if(auth()->check() && !auth()->user()->hasRole('PATIENT'))
            function refreshNotifBadge() {
                $.getJSON('{{ route("notifications.ajax.unread") }}', function(data) {
                    var count = data.count || 0;
                    var badge = document.getElementById('notif-badge');
                    var dot = document.getElementById('notif-dot');
                    if (badge) {
                        if (count > 0) {
                            badge.textContent = count > 99 ? '99+' : count;
                            badge.style.display = 'inline-block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                    if (dot) {
                        if (count > 0) {
                            dot.style.display = 'inline-block';
                        } else {
                            dot.style.display = 'none';
                        }
                    }
                }).fail(function() {
                    // Silently ignore
                });
            }

            refreshNotifBadge();
            setInterval(refreshNotifBadge, 60000); // Polling mỗi 60 giây
        @endif
    });
}
</script>
@endpush