@extends('layouts.app')

@section('title', 'Danh sách hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Danh Sách Hóa Đơn</h3>
    </div>

    {{-- ===== PANEL TẠO HÓA ĐƠN MỚI ===== --}}
    <div class="card shadow-sm border-0 border-top border-success border-3 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-success"><i class="fas fa-plus-circle me-2"></i>Tạo Hóa Đơn Mới</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('invoices.lookup') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Nhập mã bệnh nhân, số điện thoại hoặc mã phiên khám</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white"><i class="fas fa-search"></i></span>
                            <input type="text" name="lookup_keyword"
                                class="form-control form-control-lg @error('lookup_keyword') is-invalid @enderror"
                                placeholder="VD: BN-0001, 0912345678, hoặc số phiên khám..."
                                value="{{ old('lookup_keyword') }}"
                                autofocus>
                            @error('lookup_keyword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Hệ thống sẽ tìm phiên khám đang ở trạng thái <strong>Đang khám</strong> hoặc <strong>Chờ thanh toán</strong>.
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                            <i class="fas fa-file-invoice me-2"></i>Tra cứu & Tạo HĐ
                        </button>
                    </div>
                </div>
            </form>

            {{-- Thông báo lỗi tra cứu --}}
            @if(session('lookup_error'))
                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('lookup_error') }}
                </div>
            @endif

            {{-- Trường hợp tìm được nhiều phiên khám cho 1 bệnh nhân --}}
            @if(session('lookup_patient') && session('lookup_consultations'))
                @php
                    $lPatient = session('lookup_patient');
                    $lConsultations = session('lookup_consultations');
                @endphp
                <div class="alert alert-info mt-3 mb-0">
                    <strong><i class="fas fa-user me-1"></i>{{ $lPatient->full_name }}</strong> có {{ $lConsultations->count() }} phiên khám chưa có hóa đơn. Vui lòng chọn phiên khám:
                    <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach($lConsultations as $c)
                            <a href="{{ route('invoices.create', ['consultation_id' => $c->id]) }}"
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-file-medical me-1"></i>
                                Phiên #{{ $c->id }} — {{ \Carbon\Carbon::parse($c->consultation_date)->format('d/m/Y H:i') }}
                                <span class="badge bg-light text-dark ms-1">{{ $c->status_text }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Thanh tìm kiếm --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-semibold small text-muted">Tìm kiếm hóa đơn</label>
                    <input type="text" id="search-keyword" class="form-control"
                        placeholder="Mã hóa đơn, tên bệnh nhân, mã BN..."
                        value="{{ request('keyword') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">Trạng thái</label>
                    <select id="search-status" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="btn-search" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="ps-4">Mã HĐ</th>
                            <th scope="col">Ngày lập</th>
                            <th scope="col">Bệnh nhân</th>
                            <th scope="col" class="text-end">Tổng tiền</th>
                            <th scope="col" class="text-center">Trạng thái</th>
                            <th scope="col" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="invoice-table-body">
                        @forelse($invoices as $item)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $item->invoice_code }}</td>
                                <td>{{ $item->invoice_date ? $item->invoice_date->format('d/m/Y H:i') : '---' }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->consultation->patient->full_name ?? 'Không rõ' }}</span>
                                    <div class="small text-muted">{{ $item->consultation->patient->patient_code ?? '' }}</div>
                                </td>
                                <td class="text-end fw-bold text-danger fs-6">
                                    {{ number_format($item->total_amount, 0, ',', '.') }} đ
                                </td>
                                <td class="text-center">
                                    @if($item->status == 0)
                                        <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                                    @elseif($item->status == 1)
                                        <span class="badge bg-success">Đã thanh toán</span>
                                    @elseif($item->status == 2)
                                        <span class="badge bg-danger">Đã hủy</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('invoices.show', $item->id) }}" class="btn btn-sm btn-outline-primary"
                                        title="Xem chi tiết">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Chưa có hóa đơn nào trên hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-end">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#btn-search').on('click', function() {
        let keyword = $('#search-keyword').val();
        let status = $('#search-status').val();
        let url = '{{ route("invoices.index") }}';
        let params = [];

        if (keyword) params.push('keyword=' + encodeURIComponent(keyword));
        if (status !== '') params.push('status=' + status);

        if (params.length > 0) {
            url += '?' + params.join('&');
        }

        window.location.href = url;
    });

    // Enter key search
    $('#search-keyword').on('keypress', function(e) {
        if (e.which === 13) {
            $('#btn-search').click();
        }
    });
});
</script>
@endpush
