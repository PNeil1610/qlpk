<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        if ($request->keyword) {

            $query->search($request->keyword);

        }

        $departments = $query
            ->latest()
            ->paginate(10);

        return view(
            'departments.index',
            compact('departments')
        );
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'dept_code' => 'required|string|max:30|unique:department,dept_code',

            'dept_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'status' => 'required|integer',

        ]);

        Department::create($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Thêm khoa thành công');
    }

    public function show($id)
    {
        $department = Department::findOrFail($id);

        return view(
            'departments.show',
            compact('department')
        );
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);

        return view(
            'departments.edit',
            compact('department')
        );
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([

            'dept_code' => 'required|string|max:30|unique:department,dept_code,' . $id,

            'dept_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'status' => 'required|integer',

        ]);

        $department->update($validated);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Cập nhật khoa thành công');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Xóa khoa thành công');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $departments = Department::query()

            ->when($keyword, function ($q) use ($keyword) {

                $q->where('dept_code', 'like', "%$keyword%")
                    ->orWhere('dept_name', 'like', "%$keyword%");

            })

            ->latest()
            ->get();

        return response()->json($departments);
    }
}
