@extends('layouts.app')

@section('title', 'Chỉnh sửa hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary">
            <i class="fas fa-edit me-2"></i>Chỉnh Sửa Hóa Đơn - {{ $invoice->invoice_code }}
        </h3>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Trở về
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <!-- Thông tin bệnh nhân & Phiên khám -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-injured me-2 text-primary"></i>Thông Tin Khám</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td class="text-muted" style="width: 120px;">Mã BN:</td>
                            <td class="fw-bold">{{ $invoice->consultation->patient->patient_code ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Họ và tên:</td>
                            <td class="fw-bold text-primary">{{ $invoice->consultation->patient->full_name ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Điện thoại:</td>
                            <td>{{ $invoice->consultation->patient->phone_number ?? '---' }}</td>
                        </tr>
                        <tr><td colspan="2"><hr class="my-2"></td></tr>
                        <tr>
                            <td class="text-muted">Phiên khám:</td>
                            <td class="fw-bold">#{{ $invoice->consultation->id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ngày khám:</td>
                            <td>{{ $invoice->consultation->consultation_date ? \Carbon\Carbon::parse($invoice->consultation->consultation_date)->format('d/m/Y H:i') : '---' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Chi tiết phí và Chỉnh sửa -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 border-top border-warning border-3 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-warning"></i>Chi Tiết Chi Phí</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoices.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Chi phí khám bệnh (Mặc định)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ number_format($invoice->consultation_fee, 0, ',', '.') }}" readonly>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Chi phí thuốc (Đồng bộ từ bác sĩ)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ number_format($invoice->medicine_fee, 0, ',', '.') }}" readonly>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-1"></i> Thêm chiết khấu & Phụ phí</h6>
                        
                        <div class="row g-3">
                            @php
                                $otherFeeDetail = $invoice->details->where('item_type', \App\Models\InvoiceDetail::TYPE_OTHER)->first();
                                $otherFeeNote = $otherFeeDetail ? $otherFeeDetail->item_name : '';
                            @endphp
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Chi phí khác</label>
                                <div class="input-group">
                                    <input type="number" name="other_fee" id="other_fee" class="form-control" 
                                           value="{{ old('other_fee', $invoice->other_fee) }}" min="0" step="1000">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ghi chú chi phí khác</label>
                                <input type="text" name="other_fee_note" class="form-control" 
                                       value="{{ old('other_fee_note', $otherFeeNote) }}" 
                                       placeholder="VD: Sổ khám bệnh, tiền dịch vụ...">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">Chiết khấu (Giảm giá)</label>
                                <div class="input-group">
                                    <input type="number" name="discount" id="discount" class="form-control border-danger" 
                                           value="{{ old('discount', $invoice->discount) }}" min="0" step="1000">
                                    <span class="input-group-text bg-danger text-white">VNĐ</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                            <h4 class="mb-0 text-muted">Tổng thành tiền (Tạm tính):</h4>
                            <h3 class="mb-0 text-danger fw-bold" id="total_amount_preview">
                                {{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ
                            </h3>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-warning btn-lg px-4">
                                <i class="fas fa-save me-2"></i>Lưu Thay Đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const baseAmount = {{ $invoice->consultation_fee + $invoice->lab_test_fee + $invoice->medicine_fee }};
        
        function calculateTotal() {
            let otherFee = parseFloat($('#other_fee').val()) || 0;
            let discount = parseFloat($('#discount').val()) || 0;
            
            let total = baseAmount + otherFee - discount;
            if (total < 0) total = 0;
            
            $('#total_amount_preview').text(new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ');
        }

        $('#other_fee, #discount').on('input', calculateTotal);
    });
</script>
@endpush
