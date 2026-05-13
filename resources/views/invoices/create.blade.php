@extends('layouts.app')

@section('title', 'Tạo hóa đơn')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-primary"><i class="fas fa-file-invoice me-2"></i>Tạo Hóa Đơn</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
        @csrf

        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">

        <div class="row">
            {{-- Cột trái: Thông tin --}}
            <div class="col-lg-8">
                {{-- Thông tin bệnh nhân --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-user-injured me-2 text-primary"></i>Thông tin Bệnh nhân
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">Họ và tên:</td>
                                        <td class="fw-bold">{{ $consultation->patient->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Mã BN:</td>
                                        <td class="fw-bold">{{ $consultation->patient->patient_code ?? '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Giới tính:</td>
                                        <td>{{ $consultation->patient->gender_text }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" width="40%">SĐT:</td>
                                        <td>{{ $consultation->patient->phone_number ?? '---' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Mã phiên khám:</td>
                                        <td class="fw-bold text-primary">#{{ $consultation->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Chẩn đoán:</td>
                                        <td class="text-danger fw-semibold">{{ $consultation->diagnosis_notes ?? $consultation->symptoms ?? '---' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chi tiết thuốc --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-pills me-2 text-success"></i>Chi tiết thuốc
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">STT</th>
                                        <th width="35%">Tên thuốc</th>
                                        <th class="text-center" width="10%">SL</th>
                                        <th class="text-center" width="12%">Đơn vị</th>
                                        <th class="text-end" width="18%">Đơn giá</th>
                                        <th class="text-end" width="20%">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($medicineItems as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="fw-bold">{{ $item['medicine_name'] }}</td>
                                            <td class="text-center">{{ $item['quantity'] }}</td>
                                            <td class="text-center">{{ $item['unit'] }}</td>
                                            <td class="text-end">{{ number_format($item['unit_price'], 0, ',', '.') }} đ</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($item['line_total'], 0, ',', '.') }} đ</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-info-circle me-1"></i> Không có thuốc trong đơn thuốc
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($medicineItems) > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="5" class="text-end fw-bold">Tổng tiền thuốc:</td>
                                        <td class="text-end fw-bold text-danger fs-6">
                                            {{ number_format($totalMedicineFee, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Tổng hợp chi phí --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4" style="position: sticky; top: 80px;">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-calculator me-2"></i>Tổng hợp chi phí
                        </h5>
                    </div>
                    <div class="card-body">
                        {{-- Chi phí khám bệnh --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-stethoscope me-1 text-primary"></i> Chi phí khám bệnh
                            </label>
                            <div class="input-group">
                                <input type="number" name="consultation_fee" id="consultation_fee"
                                    class="form-control text-end fee-input"
                                    value="{{ $consultationFee }}" min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        {{-- Tiền thuốc (readonly) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-pills me-1 text-success"></i> Tiền thuốc
                            </label>
                            <div class="input-group">
                                <input type="number" name="medicine_fee" id="medicine_fee"
                                    class="form-control text-end bg-light"
                                    value="{{ $totalMedicineFee }}" readonly>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        {{-- Chi phí khác --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-plus-circle me-1 text-info"></i> Chi phí khác
                            </label>
                            <div class="input-group">
                                <input type="number" name="other_fee" id="other_fee"
                                    class="form-control text-end fee-input"
                                    value="0" min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                            <input type="text" name="other_fee_note" class="form-control form-control-sm mt-1"
                                placeholder="Ghi chú chi phí khác...">
                        </div>

                        {{-- Giảm giá --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-warning"></i> Giảm giá
                            </label>
                            <div class="input-group">
                                <input type="number" name="discount" id="discount"
                                    class="form-control text-end fee-input"
                                    value="0" min="0" step="1000">
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>

                        <hr>

                        {{-- Tổng cộng --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fs-5 fw-bold">TỔNG CỘNG:</span>
                            <span class="fs-4 fw-bold text-danger" id="total-display">0 đ</span>
                        </div>

                        {{-- Phương thức thanh toán --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-credit-card me-1"></i> Phương thức thanh toán
                            </label>
                            <select name="payment_method" class="form-select">
                                <option value="">-- Chọn sau --</option>
                                <option value="Cash">💵 Tiền mặt</option>
                                <option value="Card">💳 Thẻ</option>
                                <option value="Transfer">🏦 Chuyển khoản</option>
                                <option value="Insurance">🏥 Bảo hiểm</option>
                                <option value="Other">📝 Khác</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
                            <i class="fas fa-save me-2"></i> Tạo Hóa Đơn
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calculateTotal() {
        let consultationFee = parseFloat($('#consultation_fee').val()) || 0;
        let medicineFee = parseFloat($('#medicine_fee').val()) || 0;
        let otherFee = parseFloat($('#other_fee').val()) || 0;
        let discount = parseFloat($('#discount').val()) || 0;

        let total = consultationFee + medicineFee + otherFee - discount;
        if (total < 0) total = 0;

        $('#total-display').text(total.toLocaleString('vi-VN') + ' đ');
    }

    // Tính tổng ban đầu
    calculateTotal();

    // Lắng nghe thay đổi
    $('.fee-input').on('input change', function() {
        calculateTotal();
    });
});
</script>
@endpush
