<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use App\Models\Consultation;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index()
    {
        $prescriptions = Prescription::with(['consultation.patient', 'dispenser'])
                                     ->orderBy('id', 'desc')
                                     ->paginate(10);
        
        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        if (!$request->has('consultation_id')) {
            return redirect()->back()->with('error', 'Vui lòng chọn một phiên khám để kê toa!');
        }

        $consultation = Consultation::with('patient')->findOrFail($request->consultation_id);

        $medicines = Medicine::with(['details' => function($query) {
            $query->where('status', 1);
        }])->where('status', 1)->get();

        return view('prescriptions.create', compact('consultation', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consultation_id' => 'required|exists:consultation,id',
            'doctor_advice'   => 'nullable|string',
            'medicines'       => 'required|array|min:1',
            'medicines.*'     => 'exists:medicine,id',
            'quantities'      => 'required|array',
            'quantities.*'    => 'required|integer|min:1',
            'dosages'         => 'required|array',
            'units'           => 'required|array',
        ], [
            'medicines.required' => 'Bạn phải chọn ít nhất một loại thuốc cho toa này.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $prescription = Prescription::create([
                    'consultation_id' => $request->consultation_id,
                    'doctor_advice'   => $request->doctor_advice,
                    'prescribed_at'   => now(),
                    'status'          => 1,
                ]);

                $medicineIds = $request->medicines;
                $quantities = $request->quantities;
                $dosages = $request->dosages;
                $units = $request->units;
                $instructions = $request->usage_instructions ?? [];
                $durations = $request->duration_days ?? [];

                foreach ($medicineIds as $index => $medicineId) {
                    PrescriptionDetail::create([
                        'prescription_id'    => $prescription->id,
                        'medicine_id'        => $medicineId,
                        'quantity'           => $quantities[$index],
                        'unit'               => $units[$index] ?? 'Viên',
                        'dosage'             => $dosages[$index] ?? '',
                        'usage_instructions' => $instructions[$index] ?? null,
                        'duration_days'      => $durations[$index] ?? null,
                        'status'             => 1,
                    ]);
                }

                $consultation = Consultation::find($request->consultation_id);
                $consultation->update(['status' => Consultation::STATUS_WAIT_PAYMENT]);

                return redirect()->route('consultations.show', $request->consultation_id)
                                 ->with('success', 'Đã kê toa thuốc thành công!');
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $prescription = Prescription::with([
            'consultation.patient', 
            'details.medicine', 
            'dispenser'
        ])->findOrFail($id);

        return view('prescriptions.show', compact('prescription'));
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $prescription = Prescription::findOrFail($id);
                $consultationId = $prescription->consultation_id;

                // Kiểm tra nếu toa thuốc đã được phát thì không cho xóa
                if ($prescription->status == 2) {
                    return redirect()->back()->with('error', 'Không thể xóa toa thuốc đã được phát thuốc!');
                }

                // Kiểm tra nếu đã thanh toán hóa đơn thì không cho xóa (nếu có tính năng hóa đơn)
                $invoice = \App\Models\Invoice::where('consultation_id', $consultationId)
                    ->where('status', \App\Models\Invoice::STATUS_PAID)
                    ->first();
                
                if ($invoice) {
                    return redirect()->back()->with('error', 'Không thể xóa toa thuốc vì hóa đơn đã được thanh toán!');
                }

                // Xóa chi tiết toa thuốc
                PrescriptionDetail::where('prescription_id', $id)->delete();

                // Xóa toa thuốc
                $prescription->delete();

                // Cập nhật lại trạng thái phiên khám
                $consultation = Consultation::find($consultationId);
                if ($consultation) {
                    // Kiểm tra xem còn toa thuốc nào khác không
                    $remainingPrescriptions = Prescription::where('consultation_id', $consultationId)
                        ->where('id', '!=', $id)
                        ->count();
                    
                    if ($remainingPrescriptions == 0) {
                        $consultation->update(['status' => Consultation::STATUS_PROCESSING]);
                    }
                }

                return redirect()->route('consultations.doctor.examine', $consultationId)
                                 ->with('success', 'Đã xóa toa thuốc thành công!');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa toa thuốc: ' . $e->getMessage());
        }
    }
}