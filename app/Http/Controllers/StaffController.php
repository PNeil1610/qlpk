<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Room;
use App\Models\User;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::with(['department', 'room'])->orderBy('id', 'desc')->paginate(10);
        
        return view('staff.index', compact('staffs'));
    }

    public function create()
    {
        $departments = Department::all();
        $rooms = Room::all();

        return view('staff.create', compact('departments', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:150',
            'staff_type' => 'required|in:Doctor,Nurse,Technician,Pharmacist,Receptionist,Cashier,Admin',
            'email' => 'nullable|email|max:100',
            'phone_number' => 'nullable|string|max:15',
        ]);

        $data = $request->all();
        if (!empty($data['yob'])) {
            $data['age'] = date('Y') - $data['yob'];
        }

        $staff = Staff::create($data);

        $prefix = 'NV';
        if ($staff->staff_type == 'Doctor') {
            $prefix = 'BS';
        } elseif ($staff->staff_type == 'Nurse') {
            $prefix = 'DD';
        } elseif ($staff->staff_type == 'Pharmacist') {
            $prefix = 'DS';
        }

        $year = date('y'); 
        $code = $prefix . str_pad($staff->id, 6, '0', STR_PAD_LEFT);

        $staff->update(['staff_code' => $code]);

        return redirect()->route('staff.index')->with('success', 'Đã thêm nhân viên: ' . $staff->full_name . ' thành công!');
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        $departments = Department::all();
        $rooms = Room::all();

        return view('staff.edit', compact('staff', 'departments', 'rooms'));
    }
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:150',
            'staff_type' => 'required',
            'email' => 'nullable|email|max:100',
            'national_id' => 'nullable|unique:staff,national_id,' . $id,
            'phone_number' => 'nullable|string|max:15',
        ]);

        $data = $request->all();
        
        if (!empty($data['yob'])) {
            $data['age'] = date('Y') - $data['yob'];
        }

        $staff->update($data);

        return redirect()->route('staff.index')->with('success', 'Hồ sơ của ' . $staff->full_name . ' đã được cập nhật toàn diện.');
    }

    public function show($id)
    {
        $staff = Staff::with(['department', 'room'])->findOrFail($id);
        
        return view('staff.show', compact('staff'));
    }

    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $name = $staff->full_name;
        
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Đã xóa hồ sơ nhân viên: ' . $name);
    }
}