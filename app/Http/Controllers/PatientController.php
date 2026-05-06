<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // 📌 Danh sách
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

        return redirect()->route('patients.index')
            ->with('success', 'Cập nhật thành công');
    }

    // 📌 Xóa
    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Xóa thành công');
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
}