<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    // 📌 Danh sách
    public function index()
    {
        $patients = Patient::latest()->paginate(10);
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
        'phone_number' => 'nullable|string|max:15',
        'email' => 'nullable|email',
        'address' => 'nullable|string|max:255',
        
    ]);

    // 🔥 Tạo object
    $patient = new Patient();

    $patient->full_name = $validated['full_name'];
    $patient->gender = $validated['gender'];
    $patient->phone_number = $validated['phone_number'] ?? null;
    $patient->email = $validated['email'] ?? null;
    $patient->address = $validated['address'] ?? null;
    $patient->date_of_birth = $validated['date_of_birth'] ?? null;

    // Lưu DB
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
            'phone_number' => "nullable|string|max:15|unique:patient,phone_number,$id",
            'email' => "nullable|email|unique:patient,email,$id",
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
            ->with('success', 'Xóa bệnh nhân thành công');
    }
}