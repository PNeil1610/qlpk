<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    // 📌 Danh sách bệnh nhân
    public function index(Request $request)
    {
        $query = Patient::query();

        // 🔍 nếu có keyword
        if ($request->keyword) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%$keyword%")
                    ->orWhere('phone_number', 'like', "%$keyword%")
                    ->orWhere('patient_code', 'like', "%$keyword%");
            });
        }

        $patients = $query->latest()->paginate(10);

        return view('patients.index', compact('patients'));
    }

    // 📌 Form thêm
    public function create()
    {
        return view('patients.create');
    }

    // 📌 Lưu
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0|max:150',
            'phone_number' => 'nullable|unique:patient,phone_number',
            'email' => 'nullable|email|unique:patient,email',
            'national_id' => 'nullable|unique:patient,national_id',
            'insurance_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);

        $patient = new Patient();
        $patient->fill($validated);

        // auto
        $patient->save(); // lưu trước để có id

        $patient->patient_code = 'BN' . str_pad($patient->id, 6, '0', STR_PAD_LEFT);
        $patient->status = Patient::STATUS_ACTIVE;

        $patient->save();

        return redirect()->route('patients.index')
            ->with('success', 'Thêm bệnh nhân thành công');
    }

    // 📌 Chi tiết
    public function show($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.show', compact('patient'));
    }

    // 📌 Form sửa
    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    // 📌 Cập nhật
    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'nullable|integer|min:0|max:150',
            'phone_number' => "nullable|unique:patient,phone_number,$id",
            'email' => "nullable|email|unique:patient,email,$id",
            'national_id' => "nullable|unique:patient,national_id,$id",
            'insurance_number' => "nullable|string|max:50",
            'address' => 'nullable|string|max:255',
        ]);

        $patient->update($validated);

        // Đồng bộ thông tin nếu bệnh nhân này có tài khoản User (user_id)
        if ($patient->user) {
            $patient->user->email = $validated['email'] ?? $patient->user->email;
            $patient->user->phone_number = $validated['phone_number'] ?? $patient->user->phone_number;
            $patient->user->full_name = $validated['full_name'] ?? $patient->user->full_name;
            $patient->user->save();
        }

        return redirect()->route('patients.index')
            ->with('success', 'Cập nhật thành công');
    }

    // 📌 Xóa
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);

        // 🔥 kiểm tra đã có phiên khám chưa
        if ($patient->consultations()->exists()) {

            return redirect()->route('patients.index')
                ->with(
                    'error',
                    'Không thể xóa bệnh nhân vì đã có phiên khám.'
                );
        }

        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Xóa bệnh nhân thành công.');
    }

    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $patients = Patient::where('full_name', 'like', "%$keyword%")
            ->orWhere('patient_code', 'like', "%$keyword%")
            ->limit(10)
            ->get(['id', 'full_name', 'patient_code']);

        return response()->json($patients);
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $patients = Patient::query()

            ->when($keyword, function ($query) use ($keyword) {

                $query->where('full_name', 'like', "%$keyword%")
                    ->orWhere('patient_code', 'like', "%$keyword%")
                    ->orWhere('phone_number', 'like', "%$keyword%");
            })

            ->latest()
            ->get();

        return response()->json($patients);
    }
    public function showMyMedicalRecord()
    {
        $user = Auth::user();

        if ($user->hasRole('PATIENT')) {
            $myPatientRecord = $user->patient;

            if (!$myPatientRecord) {
                return redirect()->home()->with('error', 'Bạn chưa có hồ sơ bệnh án trên hệ thống.');
            }

            return view('patients.my_record', ['patient' => $myPatientRecord]);
        }

        return redirect()->route('patients.index');
    }
    public function myRecord()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $myPatientRecord = $user->patient;

        if (!$myPatientRecord) {
            return redirect()->back()->with('error', 'Hệ thống chưa ghi nhận hồ sơ bệnh án của bạn.');
        }
        return view('patients.my_record', compact('myPatientRecord'));
    }
}