<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Invoice;

class PrescriptionController extends Controller
{
    /**
     * Xác nhận hoặc hủy xác nhận đã cấp phát thuốc
     */
    public function updateStatus(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        
        if ($prescription->status == 1) {
            // RÀNG BUỘC THANH TOÁN: Chỉ cho phép phát thuốc nếu hóa đơn liên kết đã thanh toán thành công
            $invoice = Invoice::where('consultation_id', $prescription->consultation_id)->first();
            if (!$invoice || $invoice->status !== Invoice::STATUS_PAID) {
                return back()->with('error', 'Không thể cấp phát thuốc vì hóa đơn của phiên khám này chưa được thanh toán!');
            }

            $prescription->status = 2; // Đã phát thuốc
            if (auth()->user() && auth()->user()->staff) {
                $prescription->dispensed_by = auth()->user()->staff->id;
            }
            $prescription->dispensed_at = now();
            $msg = 'Đã xác nhận lấy thuốc thành công!';
        } else {
            $prescription->status = 1; // Chưa phát thuốc
            $prescription->dispensed_by = null;
            $prescription->dispensed_at = null;
            $msg = 'Đã hủy xác nhận lấy thuốc!';
        }
        
        $prescription->save();
        
        return back()->with('success', $msg);
    }
}
