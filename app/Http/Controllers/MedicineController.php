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
            'unit'                 => 'required|string|max:50',
            'price'                => 'required|numeric|min:0',
            'active_ingredient'    => 'nullable|string|max:200',
        ], [
            'medicine_name.required' => 'Vui lòng nhập tên thuốc.',
            'unit.required'          => 'Vui lòng nhập đơn vị tính (Viên, Chai...).',
            'price.required'         => 'Vui lòng nhập giá bán.'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $medicine = Medicine::create([
                    'medicine_category_id' => $request->medicine_category_id,
                    'medicine_name'        => $request->medicine_name,
                    'note'                 => $request->note,
                    'status'               => $request->status,
                ]);

                $code = 'TH' . str_pad($medicine->id, 6, '0', STR_PAD_LEFT);
                $medicine->update(['medicine_code' => $code]);

                MedicineDetail::create([
                    'medicine_id'       => $medicine->id,
                    'unit'              => $request->unit,
                    'price'             => $request->price,
                    'active_ingredient' => $request->active_ingredient,
                    'storage_condition' => $request->storage_condition,
                    'order_number'      => $request->order_number,
                    'status'            => 1,
                ]);

                return redirect()->route('medicines.index')->with('success', 'Đã thêm thuốc: ' . $medicine->medicine_name);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Lỗi: ' . $e->getMessage());
        }
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
            'unit'                 => 'required|string|max:50',
            'price'                => 'required|numeric|min:0',
        ]);

        try {
            return DB::transaction(function () use ($request, $medicine) {
                $medicine->update([
                    'medicine_category_id' => $request->medicine_category_id,
                    'medicine_name'        => $request->medicine_name,
                    'note'                 => $request->note,
                    'status'               => $request->status,
                ]);

                $detail = $medicine->details()->where('status', 1)->first();
                if ($detail) {
                    $detail->update([
                        'unit'              => $request->unit,
                        'price'             => $request->price,
                        'active_ingredient' => $request->active_ingredient,
                        'storage_condition' => $request->storage_condition,
                        'order_number'      => $request->order_number,
                    ]);
                }

                return redirect()->route('medicines.index')->with('success', 'Đã cập nhật thông tin thuốc thành công.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
        }
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
}