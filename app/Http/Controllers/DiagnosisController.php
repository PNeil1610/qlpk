<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    /**
     * 📋 Danh sách mã bệnh
     */
    public function index(Request $request)
    {
        $query = Diagnosis::query();

        // 🔍 tìm kiếm
        if ($request->keyword) {

            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {

                $q->where('diagnosis_code', 'like', "%$keyword%")
                    ->orWhere('diagnosis_name', 'like', "%$keyword%");
            });
        }

        $diagnoses = $query->latest()->paginate(10);

        return view('diagnoses.index', compact('diagnoses'));
    }

    /**
     * ➕ Form tạo
     */
    public function create()
    {
        return view('diagnoses.create');
    }

    /**
     * 💾 Lưu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'diagnosis_code' => 'required|string|max:15|unique:diagnosis,diagnosis_code',

            'diagnosis_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'icd_version' => 'nullable|string|max:10',
        ]);

        $diagnosis = new Diagnosis();

        $diagnosis->fill($validated);

        // mặc định active
        $diagnosis->status = Diagnosis::STATUS_ACTIVE;

        $diagnosis->save();

        return redirect()->route('diagnoses.index')
            ->with('success', 'Thêm mã bệnh thành công');
    }

    /**
     * 🔍 Chi tiết
     */
    public function show($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        return view('diagnoses.show', compact('diagnosis'));
    }

    /**
     * ✏️ Form sửa
     */
    public function edit($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        return view('diagnoses.edit', compact('diagnosis'));
    }

    /**
     * 🔄 Cập nhật
     */
    public function update(Request $request, $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        $validated = $request->validate([

            'diagnosis_code' =>
                "required|string|max:15|unique:diagnosis,diagnosis_code,$id",

            'diagnosis_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'icd_version' => 'nullable|string|max:10',

            'status' => 'required|in:0,1',
        ]);

        $diagnosis->update($validated);

        return redirect()->route('diagnoses.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * 🗑️ Xóa
     */
    public function destroy($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        // 🔥 kiểm tra đã dùng trong phiên khám chưa
        if ($diagnosis->consultations()->exists()) {

            return redirect()->route('diagnoses.index')
                ->with(
                    'error',
                    'Không thể xóa vì mã bệnh đã được sử dụng.'
                );
        }

        $diagnosis->delete();

        return redirect()->route('diagnoses.index')
            ->with('success', 'Xóa thành công');
    }

    /**
     * 🔥 AJAX SEARCH
     */
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $diagnoses = Diagnosis::query()

            ->when($keyword, function ($q) use ($keyword) {

                $q->where('diagnosis_code', 'like', "%$keyword%")
                    ->orWhere('diagnosis_name', 'like', "%$keyword%");

            })

            ->limit(20)
            ->get();

        return response()->json($diagnoses);
    }
}