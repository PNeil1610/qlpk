<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineDetail;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with([
            'category', 
            'details' => function($query) {
                $query->where('status', 1);
            }
        ])->orderBy('id', 'desc')->paginate(10);
        
        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        $categories = MedicineCategory::where('status', 1)->get();
        
        return view('medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_category_id' => 'required|exists:medicine_category,id',
            'medicine_name'        => 'required|string|max:150',
        ], [
            'medicine_name.required' => 'Vui lòng nhập tên thuốc.',
        ]);

        $medicine = Medicine::create([
            'medicine_category_id' => $request->medicine_category_id,
            'medicine_name'        => $request->medicine_name,
            'note'                 => $request->note,
            'status'               => $request->status ?? 1,
        ]);

        $code = 'TH' . str_pad($medicine->id, 4, '0', STR_PAD_LEFT);
        $medicine->update(['medicine_code' => $code]);

        return redirect()->route('medicines.show', $medicine->id)
                         ->with('success', 'Đã khởi tạo thuốc thành công. Vui lòng thêm Đơn vị tính và Giá bán!');
    }
    
    public function show($id)
    {
        $medicine = Medicine::with(['category', 'details'])->findOrFail($id);
        return view('medicines.show', compact('medicine'));
    }

    public function edit($id)
    {
        $medicine = Medicine::with('details')->findOrFail($id);
        $categories = MedicineCategory::where('status', 1)->get();
        
        $currentDetail = $medicine->details()->where('status', 1)->first();

        return view('medicines.edit', compact('medicine', 'categories', 'currentDetail'));
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'medicine_category_id' => 'required|exists:medicine_category,id',
            'medicine_name'        => 'required|string|max:150',
        ]);

        $medicine->update([
            'medicine_category_id' => $request->medicine_category_id,
            'medicine_name'        => $request->medicine_name,
            'note'                 => $request->note,
            'status'               => $request->status,
        ]);

        return redirect()->route('medicines.show', $medicine->id)
                         ->with('success', 'Đã cập nhật thông tin gốc của thuốc.');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        
        try {
            $medicine->details()->delete(); 
            $medicine->delete();
            return redirect()->route('medicines.index')->with('success', 'Đã xóa thuốc khỏi hệ thống.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa vì thuốc này có dữ liệu liên quan.');
        }
    }

    public function details($id)
    {
        $medicine = Medicine::findOrFail($id);
        
        $details = $medicine->details()
                            ->orderBy('status', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('medicines.details', compact('medicine', 'details'));
    }
}