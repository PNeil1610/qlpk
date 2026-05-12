<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicineCategory;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        $categories = MedicineCategory::latest()->paginate(10);

        return view('medicine_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('medicine_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:150|unique:medicine_category,category_name',
            'description'   => 'nullable|string',
            'status'        => 'required|in:0,1',
        ], [
            'category_name.required' => 'Vui lòng nhập tên loại thuốc.',
            'category_name.unique'   => 'Tên loại thuốc này đã tồn tại trong hệ thống.'
        ]);

        $category = MedicineCategory::create([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);

        $code = 'LT' . str_pad($category->id, 3, '0', STR_PAD_LEFT);
        
        $category->update(['category_code' => $code]);

        return redirect()->route('medicine_categories.index')
                         ->with('success', 'Đã thêm nhóm thuốc: ' . $category->category_name . ' thành công!');
    }
}