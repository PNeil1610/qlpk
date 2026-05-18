<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrescriptionTemplate;
use App\Models\PrescriptionTemplateDetail;
use App\Models\MedicineCategory;
use Illuminate\Support\Facades\DB;

class PrescriptionTemplateController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->staff) {
            return redirect()->back()->with('error', 'Chỉ bác sĩ mới có thể quản lý toa thuốc mẫu.');
        }

        $keyword = $request->keyword;
        $doctorId = $user->staff->id;

        $templates = PrescriptionTemplate::with('details')
            ->where('doctor_id', $doctorId)
            ->when($keyword, function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('template_name', 'like', "%{$keyword}%")
                      ->orWhere('disease_name', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('doctor.templates.index', compact('templates', 'keyword'));
    }

    public function create()
    {
        $categories = MedicineCategory::where('status', 1)->orderBy('category_name')->get();
        return view('doctor.templates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string|max:150',
            'disease_name' => 'nullable|string|max:150',
            'diagnosis_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'medicines' => 'required|array|min:1',
            'medicines.*' => 'required|exists:medicine,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:0.1',
            'dosages' => 'required|array',
            'duration_days' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $doctorId = auth()->user()->staff->id;

            $template = PrescriptionTemplate::create([
                'template_name' => $request->template_name,
                'disease_name' => $request->disease_name,
                'diagnosis_code' => $request->diagnosis_code,
                'doctor_id' => $doctorId,
                'description' => $request->description,
                'status' => 1,
            ]);

            $medicineIds = $request->medicines;
            $quantities = $request->quantities;
            $units = $request->units;
            $dosages = $request->dosages;
            $instructions = $request->usage_instructions;
            $durations = $request->duration_days;

            foreach ($medicineIds as $index => $medicineId) {
                PrescriptionTemplateDetail::create([
                    'template_id' => $template->id,
                    'medicine_id' => $medicineId,
                    'quantity' => $quantities[$index],
                    'unit' => $units[$index] ?? 'Viên',
                    'dosage' => $dosages[$index] ?? '',
                    'usage_instructions' => $instructions[$index] ?? '',
                    'duration_days' => $durations[$index] ?? null,
                    'status' => 1,
                ]);
            }

            DB::commit();
            return redirect()->route('doctor.templates.index')->with('success', 'Đã lưu toa thuốc mẫu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi lưu toa thuốc: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $doctorId = auth()->user()->staff->id;
        $template = PrescriptionTemplate::with(['details.medicine'])->where('doctor_id', $doctorId)->findOrFail($id);
        $categories = MedicineCategory::where('status', 1)->orderBy('category_name')->get();

        return view('doctor.templates.edit', compact('template', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $doctorId = auth()->user()->staff->id;
        $template = PrescriptionTemplate::where('doctor_id', $doctorId)->findOrFail($id);

        $request->validate([
            'template_name' => 'required|string|max:150',
            'disease_name' => 'nullable|string|max:150',
            'diagnosis_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'medicines' => 'required|array|min:1',
            'medicines.*' => 'required|exists:medicine,id',
        ]);

        try {
            DB::beginTransaction();

            $template->update([
                'template_name' => $request->template_name,
                'disease_name' => $request->disease_name,
                'diagnosis_code' => $request->diagnosis_code,
                'description' => $request->description,
            ]);

            PrescriptionTemplateDetail::where('template_id', $template->id)->delete();

            $medicineIds = $request->medicines;
            $quantities = $request->quantities;
            $units = $request->units;
            $dosages = $request->dosages;
            $instructions = $request->usage_instructions;
            $durations = $request->duration_days;

            foreach ($medicineIds as $index => $medicineId) {
                PrescriptionTemplateDetail::create([
                    'template_id' => $template->id,
                    'medicine_id' => $medicineId,
                    'quantity' => $quantities[$index],
                    'unit' => $units[$index] ?? 'Viên',
                    'dosage' => $dosages[$index] ?? '',
                    'usage_instructions' => $instructions[$index] ?? '',
                    'duration_days' => $durations[$index] ?? null,
                    'status' => 1,
                ]);
            }

            DB::commit();
            return redirect()->route('doctor.templates.index')->with('success', 'Cập nhật toa thuốc mẫu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $doctorId = auth()->user()->staff->id;
            $template = PrescriptionTemplate::where('doctor_id', $doctorId)->findOrFail($id);
            PrescriptionTemplateDetail::where('template_id', $template->id)->delete();
            $template->delete();
            DB::commit();
            return back()->with('success', 'Xóa toa mẫu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Không thể xóa: ' . $e->getMessage());
        }
    }

    public function ajaxGetTemplates()
    {
        $doctorId = auth()->user()->staff->id;
        $templates = PrescriptionTemplate::with(['details.medicine'])
            ->where('doctor_id', $doctorId)
            ->where('status', 1)
            ->get();
        
        return response()->json($templates);
    }
}
