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

    <a href="{{ route('patients.index') }}">👤 Bệnh nhân</a>
    <a href="{{ route('consultations.index') }}">🩺 Phiên khám</a>
    <a href="{{ route('diagnoses.index') }}">📖 Danh mục mã bệnh</a>
    <a href="{{ route('departments.index') }}">🗂️ Danh mục khoa</a>
    <a href="#">💊 Đơn thuốc</a>
    <a href="#">💰 Hóa đơn</a>

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