<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionDetail;
use App\Models\Medicine;
use App\Models\Consultation;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        $status = $request->status;

        $query = Prescription::with(['consultation.patient', 'dispenser']);
        
        if ($keyword) {
            $query->whereHas('consultation.patient', function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%{$keyword}%")
                  ->orWhere('patient_code', 'like', "%{$keyword}%")
                  ->orWhere('phone_number', 'like', "%{$keyword}%");
            });
        }
        
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $prescriptions = $query->orderBy('id', 'desc')->paginate(10);
        
        return view('prescriptions.index', compact('prescriptions', 'keyword', 'status'));
    }

    public function updateStatus(Request $request, $id)
    {
        $prescription = Prescription::findOrFail($id);
        
        if ($prescription->status == 1) {
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
                $prescribedBy = null;
                $user = auth()->user();
                if ($user && $user->staff) {
                    $prescribedBy = $user->staff->id;
                }

                $prescription = Prescription::create([
                    'consultation_id' => $request->consultation_id,
                    'prescribed_by'   => $prescribedBy,
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
            'consultation.doctor',
            'details.medicine',
            'dispenser',
            'prescriber',
        ])->findOrFail($id);

        $setting = SystemSetting::active();

        return view('prescriptions.show', compact('prescription', 'setting'));
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $prescription = Prescription::findOrFail($id);
                $consultationId = $prescription->consultation_id;

                if ($prescription->status == 2) {
                    return redirect()->back()->with('error', 'Không thể xóa toa thuốc đã được phát thuốc!');
                }

                $invoice = \App\Models\Invoice::where('consultation_id', $consultationId)
                    ->where('status', \App\Models\Invoice::STATUS_PAID)
                    ->first();
                
                if ($invoice) {
                    return redirect()->back()->with('error', 'Không thể xóa toa thuốc vì hóa đơn đã được thanh toán!');
                }

                PrescriptionDetail::where('prescription_id', $id)->delete();
                $prescription->delete();

                $consultation = Consultation::find($consultationId);
                if ($consultation) {
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
