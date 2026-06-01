<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Consultation;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * 📋 Danh sách hóa đơn (Hàng đợi & Lịch sử)
     */
    public function index(Request $request)
    {
        // Khởi tạo lấy dữ liệu lịch sử hóa đơn (status = 1 hoặc 2)
        $historyQuery = Invoice::with(['consultation.patient', 'cashier'])
            ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_CANCELLED ?? 2]);
            
        if ($request->keyword) {
            $keyword = $request->keyword;
            $historyQuery->where(function ($q) use ($keyword) {
                $q->where('invoice_code', 'like', "%{$keyword}%")
                  ->orWhereHas('consultation.patient', function ($q2) use ($keyword) {
                      $q2->where('full_name', 'like', "%{$keyword}%")
                         ->orWhere('patient_code', 'like', "%{$keyword}%")
                         ->orWhere('phone_number', 'like', "%{$keyword}%");
                  });
            });
        }
        
        $historyInvoices = $historyQuery->orderBy('id', 'desc')->paginate(10);
        
        // Hàng đợi chờ thanh toán (status = 0)
        $pendingInvoices = Invoice::with(['consultation.patient'])
            ->where('status', Invoice::STATUS_PENDING)
            ->orderBy('id', 'asc')
            ->get();

        return view('invoices.index', compact('historyInvoices', 'pendingInvoices'));
    }

    /**
     * ✏️ Form chỉnh sửa hóa đơn (Thêm phụ phí, chiết khấu)
     */
    public function edit($id)
    {
        $invoice = Invoice::with([
            'consultation.patient',
            'details'
        ])->findOrFail($id);
        
        if ($invoice->status != Invoice::STATUS_PENDING) {
            return redirect()->route('invoices.index')->with('error', 'Chỉ có thể chỉnh sửa hóa đơn chưa thanh toán.');
        }

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * 🔄 Cập nhật hóa đơn
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status != Invoice::STATUS_PENDING) {
            return redirect()->route('invoices.index')->with('error', 'Chỉ có thể chỉnh sửa hóa đơn chưa thanh toán.');
        }
        
        $request->validate([
            'consultation_fee' => 'required|numeric|min:0',
            'other_fee' => 'nullable|numeric|min:0',
            'other_fee_note' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $invoice) {
                $consultationFee = $request->consultation_fee ?? 100000;
                $otherFee = $request->other_fee ?? 0;
                $discount = $request->discount ?? 0;
                
                // Cập nhật hoặc tạo lại dòng Chi phí khám bệnh trong InvoiceDetail
                InvoiceDetail::updateOrCreate(
                    [
                        'invoice_id' => $invoice->id,
                        'item_type' => InvoiceDetail::TYPE_CONSULTATION,
                    ],
                    [
                        'item_name' => 'Chi phí khám bệnh',
                        'reference_id' => $invoice->consultation_id,
                        'quantity' => 1,
                        'unit_price' => $consultationFee,
                        'discount' => 0,
                        'line_total' => $consultationFee,
                        'status' => 1,
                    ]
                );
                
                // Cập nhật lại các dòng InvoiceDetail loại OTHER
                // Xóa chi phí khác cũ
                InvoiceDetail::where('invoice_id', $invoice->id)
                    ->where('item_type', InvoiceDetail::TYPE_OTHER)
                    ->delete();
                    
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
                
                // Tính toán lại tổng tiền
                $totalAmount = $consultationFee + $invoice->lab_test_fee + $invoice->medicine_fee + $otherFee - $discount;
                if ($totalAmount < 0) $totalAmount = 0;
                
                $invoice->update([
                    'consultation_fee' => $consultationFee,
                    'other_fee' => $otherFee,
                    'discount' => $discount,
                    'total_amount' => $totalAmount
                ]);
            });
            
            return redirect()->route('invoices.index')->with('success', 'Đã cập nhật hóa đơn thành công.');
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

        $clinicSetting = SystemSetting::active();

        return view('invoices.show', compact('invoice', 'clinicSetting'));
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
     * 🔍 Tìm kiếm AJAX (Lịch sử)
     */
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $invoices = Invoice::with(['consultation.patient', 'cashier'])
            ->whereIn('status', [Invoice::STATUS_PAID, Invoice::STATUS_CANCELLED ?? 2])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('invoice_code', 'like', "%{$keyword}%")
                      ->orWhereHas('consultation.patient', function ($q2) use ($keyword) {
                          $q2->where('full_name', 'like', "%{$keyword}%")
                             ->orWhere('patient_code', 'like', "%{$keyword}%");
                      });
                });
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json($invoices);
    }
    
    /**
     * 🔄 AJAX Hàng đợi chờ thanh toán
     */
    public function ajaxPending()
    {
        $pendingInvoices = Invoice::with(['consultation.patient'])
            ->where('status', Invoice::STATUS_PENDING)
            ->orderBy('id', 'asc')
            ->get();
            
        return response()->json($pendingInvoices);
    }

    /**
     * 🗑️ Xóa hóa đơn (chỉ khi chưa thanh toán)
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status != Invoice::STATUS_PENDING) {
            return redirect()->route('invoices.index')->with('error', 'Chỉ có thể xóa hóa đơn chưa thanh toán.');
        }

        try {
            DB::transaction(function () use ($invoice) {
                // Xóa chi tiết hóa đơn trước
                InvoiceDetail::where('invoice_id', $invoice->id)->delete();
                // Xóa hóa đơn
                $invoice->delete();
            });

            return redirect()->route('invoices.index')->with('success', 'Đã xóa hóa đơn thành công.');
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')->with('error', 'Có lỗi xảy ra khi xóa hóa đơn: ' . $e->getMessage());
        }
    }
}
