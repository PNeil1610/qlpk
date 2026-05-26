@extends('layouts.app')

@section('title', 'Danh sách hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-invoice-dollar me-2"></i>Thu Ngân - Quản Lý Hóa Đơn</h3>
    </div>

    <!-- TABS NAV -->
    <ul class="nav nav-tabs fw-bold mb-4" id="invoiceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active text-warning" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                <i class="fas fa-clock me-2"></i>Hàng Đợi Chờ Thanh Toán
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-success" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-history me-2"></i>Lịch Sử Hóa Đơn
            </button>
        </li>
    </ul>

    <!-- TABS CONTENT -->
    <div class="tab-content" id="invoiceTabsContent">
        
        <!-- TAB 1: PENDING -->
        <div class="tab-pane fade show active" id="pending" role="tabpanel" tabindex="0">
            <div class="card shadow-sm border-0 border-top border-warning border-3 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-warning"><i class="fas fa-list-ol me-2"></i>Danh sách chờ thanh toán</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="ps-4">Mã HĐ</th>
                                    <th scope="col">Ngày lập</th>
                                    <th scope="col">Bệnh nhân</th>
                                    <th scope="col" class="text-end">Tổng tiền (Dự kiến)</th>
                                    <th scope="col" class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="pending-table-body">
                                @forelse($pendingInvoices as $item)
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
                                            <div class="d-flex justify-content-center gap-1">
                                                <a href="{{ route('invoices.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa phụ phí/chiết khấu">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('invoices.show', $item->id) }}" class="btn btn-sm btn-success" title="Xác nhận thanh toán">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-pending">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-mug-hot fa-2x mb-2 d-block"></i>
                                            Tuyệt vời! Hiện không có hóa đơn nào đang chờ thanh toán.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: HISTORY -->
        <div class="tab-pane fade" id="history" role="tabpanel" tabindex="0">
            <div class="card shadow-sm border-0 border-top border-success border-3 mb-4">
                <div class="card-body py-3 bg-light">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-muted">Tìm kiếm lịch sử hóa đơn</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                <input type="text" id="search-keyword" class="form-control"
                                    placeholder="Gõ mã hóa đơn, tên bệnh nhân, mã BN để tìm..."
                                    value="{{ request('keyword') }}">
                            </div>
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
                            <tbody id="history-table-body">
                                @forelse($historyInvoices as $item)
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
                                            @if($item->status == 1)
                                                <span class="badge bg-success">Đã thanh toán</span>
                                            @elseif($item->status == 2)
                                                <span class="badge bg-danger">Đã hủy</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('invoices.show', $item->id) }}" class="btn btn-sm btn-outline-primary"
                                                title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
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
                        {{ $historyInvoices->links() }}
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    // AJAX HISTORY LOAD
    function loadHistoryInvoices() {
        let keyword = $('#search-keyword').val();

        $.ajax({
            url: '{{ route("invoices.ajax.search") }}',
            type: 'GET',
            data: { keyword: keyword },
            success: function(response) {
                let tbody = $('#history-table-body');
                tbody.empty();

                if (response.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Không tìm thấy lịch sử hóa đơn nào.
                            </td>
                        </tr>
                    `);
                    return;
                }

                $.each(response, function(index, item) {
                    let dateStr = '---';
                    if (item.invoice_date) {
                        let d = new Date(item.invoice_date);
                        let day = ('0' + d.getDate()).slice(-2);
                        let month = ('0' + (d.getMonth() + 1)).slice(-2);
                        let year = d.getFullYear();
                        let hours = ('0' + d.getHours()).slice(-2);
                        let minutes = ('0' + d.getMinutes()).slice(-2);
                        dateStr = `${day}/${month}/${year} ${hours}:${minutes}`;
                    }

                    let patientName = item.consultation && item.consultation.patient ? item.consultation.patient.full_name : 'Không rõ';
                    let patientCode = item.consultation && item.consultation.patient ? item.consultation.patient.patient_code : '';
                    let amountStr = new Intl.NumberFormat('vi-VN').format(item.total_amount) + ' đ';

                    let statusHtml = item.status == 1 
                        ? '<span class="badge bg-success">Đã thanh toán</span>' 
                        : '<span class="badge bg-danger">Đã hủy</span>';

                    let showUrl = '{{ route("invoices.show", ":id") }}'.replace(':id', item.id);

                    let row = `
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">${item.invoice_code}</td>
                            <td>${dateStr}</td>
                            <td>
                                <span class="fw-bold text-dark">${patientName}</span>
                                <div class="small text-muted">${patientCode}</div>
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">${amountStr}</td>
                            <td class="text-center">${statusHtml}</td>
                            <td class="text-center">
                                <a href="${showUrl}" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(err) {
                console.error("Lỗi khi tìm kiếm AJAX", err);
            }
        });
    }

    // AJAX PENDING LOAD
    function loadPendingInvoices() {
        $.ajax({
            url: '{{ route("invoices.ajax.pending") }}',
            type: 'GET',
            success: function(response) {
                let tbody = $('#pending-table-body');
                tbody.empty();

                if (response.length === 0) {
                    tbody.append(`
                        <tr id="empty-pending">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-mug-hot fa-2x mb-2 d-block"></i>
                                Tuyệt vời! Hiện không có hóa đơn nào đang chờ thanh toán.
                            </td>
                        </tr>
                    `);
                    return;
                }

                $.each(response, function(index, item) {
                    let dateStr = '---';
                    if (item.invoice_date) {
                        let d = new Date(item.invoice_date);
                        let day = ('0' + d.getDate()).slice(-2);
                        let month = ('0' + (d.getMonth() + 1)).slice(-2);
                        let year = d.getFullYear();
                        let hours = ('0' + d.getHours()).slice(-2);
                        let minutes = ('0' + d.getMinutes()).slice(-2);
                        dateStr = `${day}/${month}/${year} ${hours}:${minutes}`;
                    }

                    let patientName = item.consultation && item.consultation.patient ? item.consultation.patient.full_name : 'Không rõ';
                    let patientCode = item.consultation && item.consultation.patient ? item.consultation.patient.patient_code : '';
                    let amountStr = new Intl.NumberFormat('vi-VN').format(item.total_amount) + ' đ';

                    let editUrl = '{{ route("invoices.edit", ":id") }}'.replace(':id', item.id);
                    let showUrl = '{{ route("invoices.show", ":id") }}'.replace(':id', item.id);

                    let row = `
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">${item.invoice_code}</td>
                            <td>${dateStr}</td>
                            <td>
                                <span class="fw-bold text-dark">${patientName}</span>
                                <div class="small text-muted">${patientCode}</div>
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">${amountStr}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="${editUrl}" class="btn btn-sm btn-outline-warning" title="Chỉnh sửa phụ phí/chiết khấu">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="${showUrl}" class="btn btn-sm btn-success" title="Xác nhận thanh toán">
                                        <i class="fas fa-dollar-sign"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            },
            error: function(err) {
                console.error("Lỗi tải Hàng đợi AJAX", err);
            }
        });
    }

    // Event listeners
    $('#search-keyword').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadHistoryInvoices, 300); // 300ms debounce
    });

    // 🔥 AUTO REFRESH MỖI 5 GIÂY (Chỉ cập nhật Hàng Đợi)
    setInterval(function() {
        loadPendingInvoices();
    }, 5000);
});
</script>
@endpush
