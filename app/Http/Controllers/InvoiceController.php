<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\MedicineDetail;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * 📋 Danh sách hóa đơn
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['consultation.patient', 'cashier']);

        // 🔍 Tìm kiếm
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_code', 'like', "%{$keyword}%")
                  ->orWhereHas('consultation.patient', function ($q2) use ($keyword) {
                      $q2->where('full_name', 'like', "%{$keyword}%")
                         ->orWhere('patient_code', 'like', "%{$keyword}%")
                         ->orWhere('phone_number', 'like', "%{$keyword}%");
                  });
            });
        }

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('id', 'desc')->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * 🔍 Tra cứu phiên khám để tạo hóa đơn (từ mã BN hoặc mã phiên khám)
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'lookup_keyword' => 'required|string|min:1',
        ], [
            'lookup_keyword.required' => 'Vui lòng nhập mã bệnh nhân hoặc mã phiên khám.',
        ]);

        $keyword = trim($request->lookup_keyword);

        // Thử tìm theo mã phiên khám (số nguyên)
        if (is_numeric($keyword)) {
            $consultation = Consultation::with('patient')
                ->where('id', $keyword)
                ->whereIn('status', [
                    Consultation::STATUS_WAIT_PAYMENT,
                    Consultation::STATUS_PROCESSING,
                ])
                ->first();

            if ($consultation) {
                // Kiểm tra đã có hóa đơn chưa
                $existingInvoice = \App\Models\Invoice::where('consultation_id', $consultation->id)->first();
                if ($existingInvoice) {
                    return redirect()->route('invoices.show', $existingInvoice->id)
                                     ->with('error', 'Phiên khám #' . $keyword . ' đã có hóa đơn!');
                }
                return redirect()->route('invoices.create', ['consultation_id' => $consultation->id]);
            }
        }

        // Tìm theo mã bệnh nhân hoặc tên
        $patient = \App\Models\Patient::where('patient_code', $keyword)
            ->orWhere('phone_number', $keyword)
            ->first();

        if (!$patient) {
            return redirect()->back()
                ->with('lookup_error', 'Không tìm thấy bệnh nhân với mã "' . $keyword . '".')
                ->withInput();
        }

        // Tìm phiên khám đang chờ thanh toán của bệnh nhân
        $consultations = Consultation::where('patient_id', $patient->id)
            ->whereIn('status', [
                Consultation::STATUS_WAIT_PAYMENT,
                Consultation::STATUS_PROCESSING,
            ])
            ->whereDoesntHave('invoices')
            ->orderBy('consultation_date', 'desc')
            ->get();

        if ($consultations->isEmpty()) {
            return redirect()->back()
                ->with('lookup_error', 'Bệnh nhân "' . $patient->full_name . '" không có phiên khám nào đang chờ thanh toán.')
                ->withInput();
        }

        if ($consultations->count() === 1) {
            return redirect()->route('invoices.create', ['consultation_id' => $consultations->first()->id]);
        }

        // Nhiều phiên khám: truyền về view để nhân viên chọn
        return redirect()->back()
            ->with('lookup_patient', $patient)
            ->with('lookup_consultations', $consultations)
            ->withInput();
    }

    /**
     * ➕ Form tạo hóa đơn
     */
    public function create(Request $request)
    {
        if (!$request->has('consultation_id')) {
            return redirect()->back()->with('error', 'Vui lòng chọn một phiên khám để tạo hóa đơn!');
        }

        $consultation = Consultation::with([
            'patient',
            'prescriptions.details.medicine.details' => function ($q) {
                $q->where('status', 1);
            }
        ])->findOrFail($request->consultation_id);

        // Kiểm tra đã có hóa đơn chưa
        $existingInvoice = Invoice::where('consultation_id', $consultation->id)->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->id)
                             ->with('error', 'Phiên khám này đã có hóa đơn!');
        }

        // Tính tiền thuốc từ đơn thuốc
        $medicineItems = [];
        $totalMedicineFee = 0;

        foreach ($consultation->prescriptions as $prescription) {
            foreach ($prescription->details as $detail) {
                // Tìm giá thuốc từ medicine_detail (lấy giá đầu tiên active)
                $medicinePrice = 0;
                $medicineUnit = $detail->unit ?? 'Viên';

                if ($detail->medicine && $detail->medicine->details->isNotEmpty()) {
                    // Tìm medicine_detail có unit khớp, nếu không thì lấy cái đầu tiên
                    $matchingDetail = $detail->medicine->details
                        ->where('unit', $detail->unit)
                        ->first();

                    if (!$matchingDetail) {
                        $matchingDetail = $detail->medicine->details->first();
                    }

                    if ($matchingDetail) {
                        $medicinePrice = $matchingDetail->price;
                        $medicineUnit = $matchingDetail->unit;
                    }
                }

                $lineTotal = $detail->quantity * $medicinePrice;
                $totalMedicineFee += $lineTotal;

                $medicineItems[] = [
                    'medicine_id' => $detail->medicine_id,
                    'medicine_name' => $detail->medicine->medicine_name ?? 'Không rõ',
                    'quantity' => $detail->quantity,
                    'unit' => $medicineUnit,
                    'unit_price' => $medicinePrice,
                    'line_total' => $lineTotal,
                ];
            }
        }

        $consultationFee = Invoice::DEFAULT_CONSULTATION_FEE;

        return view('invoices.create', compact(
            'consultation',
            'medicineItems',
            'totalMedicineFee',
            'consultationFee'
        ));
    }

    /**
     * 💾 Lưu hóa đơn
     */
    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultation,id',
            'consultation_fee' => 'required|numeric|min:0',
            'medicine_fee' => 'required|numeric|min:0',
            'other_fee' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:Cash,Card,Transfer,Insurance,Other',
        ]);

        // Kiểm tra đã có hóa đơn chưa
        $existingInvoice = Invoice::where('consultation_id', $request->consultation_id)->first();
        if ($existingInvoice) {
            return redirect()->route('invoices.show', $existingInvoice->id)
                             ->with('error', 'Phiên khám này đã có hóa đơn!');
        }

        try {
            return DB::transaction(function () use ($request) {
                $consultationFee = $request->consultation_fee ?? 0;
                $medicineFee = $request->medicine_fee ?? 0;
                $labTestFee = 0;
                $otherFee = $request->other_fee ?? 0;
                $discount = $request->discount ?? 0;

                $totalAmount = $consultationFee + $medicineFee + $labTestFee + $otherFee - $discount;
                if ($totalAmount < 0) $totalAmount = 0;

                // Tạo hóa đơn
                $invoice = Invoice::create([
                    'invoice_code' => Invoice::generateInvoiceCode(),
                    'consultation_id' => $request->consultation_id,
                    'invoice_date' => now(),
                    'consultation_fee' => $consultationFee,
                    'lab_test_fee' => $labTestFee,
                    'medicine_fee' => $medicineFee,
                    'other_fee' => $otherFee,
                    'discount' => $discount,
                    'total_amount' => $totalAmount,
                    'payment_method' => $request->payment_method,
                    'status' => Invoice::STATUS_PENDING,
                ]);

                // Tạo chi tiết: Chi phí khám bệnh
                if ($consultationFee > 0) {
                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => InvoiceDetail::TYPE_CONSULTATION,
                        'item_name' => 'Chi phí khám bệnh',
                        'reference_id' => $request->consultation_id,
                        'quantity' => 1,
                        'unit_price' => $consultationFee,
                        'discount' => 0,
                        'line_total' => $consultationFee,
                        'status' => 1,
                    ]);
                }

                // Tạo chi tiết: Từng loại thuốc
                $consultation = Consultation::with([
                    'prescriptions.details.medicine.details' => function ($q) {
                        $q->where('status', 1);
                    }
                ])->find($request->consultation_id);

                if ($consultation) {
                    foreach ($consultation->prescriptions as $prescription) {
                        foreach ($prescription->details as $detail) {
                            $medicinePrice = 0;

                            if ($detail->medicine && $detail->medicine->details->isNotEmpty()) {
                                $matchingDetail = $detail->medicine->details
                                    ->where('unit', $detail->unit)
                                    ->first();

                                if (!$matchingDetail) {
                                    $matchingDetail = $detail->medicine->details->first();
                                }

                                if ($matchingDetail) {
                                    $medicinePrice = $matchingDetail->price;
                                }
                            }

                            $lineTotal = $detail->quantity * $medicinePrice;

                            InvoiceDetail::create([
                                'invoice_id' => $invoice->id,
                                'item_type' => InvoiceDetail::TYPE_MEDICINE,
                                'item_name' => $detail->medicine->medicine_name ?? 'Thuốc không rõ',
                                'reference_id' => $detail->medicine_id,
                                'quantity' => $detail->quantity,
                                'unit_price' => $medicinePrice,
                                'discount' => 0,
                                'line_total' => $lineTotal,
                                'notes' => $detail->dosage,
                                'status' => 1,
                            ]);
                        }
                    }
                }

                // Tạo chi tiết: Chi phí khác
                if ($otherFee > 0) {
                    InvoiceDetail::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => InvoiceDetail::TYPE_OTHER,
                        'item_name' => $request->other_fee_note ?? 'Chi phí khác',
                        'quantity' => 1,
                        'unit_price' => $otherFee,
                        'discount' => 0,
                        'line_total' => $otherFee,
                        'status' => 1,
                    ]);
                }

                return redirect()->route('invoices.show', $invoice->id)
                                 ->with('success', 'Tạo hóa đơn thành công!');
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * 🔍 Chi tiết hóa đơn
     */
    public function show($id)
    {
        $invoice = Invoice::with([
            'consultation.patient',
            'details',
            'cashier'
        ])->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * 💰 Xác nhận thanh toán
     */
    public function pay(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:Cash,Card,Transfer,Insurance,Other',
        ]);

        $invoice = Invoice::findOrFail($id);

        if ($invoice->status == Invoice::STATUS_PAID) {
            return redirect()->route('invoices.show', $invoice->id)
                             ->with('error', 'Hóa đơn này đã được thanh toán!');
        }

        try {
            return DB::transaction(function () use ($request, $invoice) {
                $invoice->update([
                    'payment_method' => $request->payment_method,
                    'paid_at' => now(),
                    'cashier_id' => auth()->id() ?? null,
                    'status' => Invoice::STATUS_PAID,
                ]);

                // Cập nhật trạng thái phiên khám → Hoàn thành
                if ($invoice->consultation) {
                    $invoice->consultation->update([
                        'status' => Consultation::STATUS_COMPLETED,
                    ]);
                }

                return redirect()->route('invoices.show', $invoice->id)
                                 ->with('success', 'Thanh toán hóa đơn thành công!');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * 🔍 Tìm kiếm AJAX
     */
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;

        $invoices = Invoice::with(['consultation.patient', 'cashier'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('invoice_code', 'like', "%{$keyword}%")
                      ->orWhereHas('consultation.patient', function ($q2) use ($keyword) {
                          $q2->where('full_name', 'like', "%{$keyword}%")
                             ->orWhere('patient_code', 'like', "%{$keyword}%");
                      });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($invoices);
    }
}
