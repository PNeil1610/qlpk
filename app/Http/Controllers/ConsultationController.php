<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * 📋 Danh sách phiên khám
     */
    public function index(Request $request)
    {
        $query = Consultation::with('patient');

        // 🔍 tìm kiếm theo tên bệnh nhân
        if ($request->keyword) {
            $keyword = $request->keyword;

            $query->whereHas('patient', function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%$keyword%")
                    ->orWhere('patient_code', 'like', "%$keyword%")
                    ->orWhere('phone_number', 'like', "%$keyword%");
            });
        }

        $consultations = $query->latest()->paginate(10);

        return view('consultations.index', compact('consultations'));
    }

    /**
     * ➕ Form tạo phiên khám
     */
    public function create(Request $request)
    {
        $selectedPatient = null;

        if ($request->patient_id) {
            $selectedPatient = Patient::find($request->patient_id);
        }

        return view('consultations.create', compact(
            'selectedPatient'
        ));
    }
    /**
     * 💾 Lưu phiên khám
     */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'patient_id' => 'required|exists:patient,id',
        //     'consultation_date' => 'required|date',
        //     'symptoms' => 'nullable|string',
        //     'temperature' => 'nullable|numeric|min:30|max:45',
        //     'blood_pressure' => 'nullable|string|max:20',
        //     'chief_complaint' => 'nullable|string|max:500',
        // ]);

        $validated = $request->validate([

            'patient_id' => 'required|exists:patient,id',
            'symptoms' => 'nullable|string',

            'temperature' => 'nullable|numeric|min:30|max:45',

            'blood_pressure' => 'nullable|string|max:20',

            'chief_complaint' => 'nullable|string|max:500',

            'diagnosis_code' => 'nullable|string|max:15',

            'diagnosis_notes' => 'nullable|string',

            'follow_up_date' => 'nullable|date',

            'status' => 'required|integer',
        ]);

        $consultation = new Consultation();
        $consultation->fill($validated);

        // mặc định trạng thái
        $consultation->status = Consultation::STATUS_NEW;

        // auto số thứ tự
        $consultation->order_number = time();

        $consultation->save();

        return redirect()->route('consultations.index')
            ->with('success', 'Tạo phiên khám thành công');
    }

    /**
     * 🔍 Chi tiết
     */
    public function show($id)
    {
        $consultation = Consultation::with('patient')->findOrFail($id);

        return view('consultations.show', compact('consultation'));
    }

    /**
     * ✏️ Form sửa
     */
    public function edit($id)
    {
        $consultation = Consultation::findOrFail($id);
        $patients = Patient::get(['id', 'full_name', 'patient_code']);

        return view('consultations.edit', compact('consultation', 'patients'));
    }

    /**
     * 🔄 Cập nhật
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'consultation_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'chief_complaint' => 'nullable|string|max:500',
            'diagnosis_code' => 'nullable|string|max:15',
            'diagnosis_notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure' => 'nullable|string|max:20',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|integer',
        ]);

        $consultation->update($validated);

        return redirect()->route('consultations.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * 🗑️ Xóa
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return redirect()->route('consultations.index')
            ->with('success', 'Xóa thành công');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $consultations = Consultation::with('patient')

            ->when($keyword, function ($query) use ($keyword) {

                $query->whereHas('patient', function ($q) use ($keyword) {

                    $q->where('full_name', 'like', "%$keyword%")
                        ->orWhere('patient_code', 'like', "%$keyword%")
                        ->orWhere('phone_number', 'like', "%$keyword%");
                });
            })

            ->latest()
            ->get();

        return response()->json($consultations);
    }

}