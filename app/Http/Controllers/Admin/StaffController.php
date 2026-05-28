<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

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
            'username'  => 'required|unique:users,username',
            'password'  => 'required|min:6',
            'full_name' => 'required|string|max:150',
            'email'     => 'nullable|email|unique:users,email',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number',
            'address' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:20|unique:users,national_id',
            'staff_type'=> 'required',
        ]);

        return DB::transaction(function () use ($request) {
            $user = User::create([
                'username'      => $request->username,
                'full_name'     => $request->full_name,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'national_id'   => $request->national_id,
                'password_hash' => Hash::make($request->password),
                'status'        => 1,
            ]);

            $roleCode = strtoupper($request->staff_type);
            $role = Role::where('role_code', $roleCode)->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            $data = $request->all();
            $data['user_id'] = $user->id;
            $data['full_name'] = $user->full_name;
            $data['email'] = $user->email;

            if (!empty($data['yob'])) {
                $data['age'] = date('Y') - $data['yob'];
            }

            $staff = Staff::create($data);

            $prefix = ($staff->staff_type == 'Doctor') ? 'BS' : 'NV';
            $code = $prefix . date('y') . str_pad($staff->id, 4, '0', STR_PAD_LEFT);
            $staff->update(['staff_code' => $code]);

            return redirect()->route('staff.index')->with('success', 'Tạo nhân viên thành công!');
        });
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
            'address' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
        if (!empty($data['yob'])) {
            $data['age'] = date('Y') - $data['yob'];
        }

        $staff->update($data);

        if($staff->user) {
            $staff->user->update([
                'full_name'    => $request->full_name,
                'email'        => $request->email,
                'phone_number' => $request->phone_number,
                'address'      => $request->address,
                'national_id'  => $request->national_id,
            ]);
        }

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

        try {
            $staff->delete();
            return redirect()->route('staff.index')->with('success', 'Đã xóa hồ sơ nhân viên: ' . $name);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('staff.index')
                    ->with('error', 'Không thể xóa nhân viên "' . $name . '" vì hồ sơ này đang được liên kết với các dữ liệu khác (phiên khám, đơn thuốc, v.v.). Vui lòng xóa các dữ liệu liên quan trước.');
            }
            return redirect()->route('staff.index')
                ->with('error', 'Đã xảy ra lỗi khi xóa nhân viên. Vui lòng thử lại.');
        }
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $staffs = Staff::with(['department', 'room'])
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('full_name', 'like', "%{$keyword}%")
                    ->orWhere('staff_code', 'like', "%{$keyword}%")
                    ->orWhere('phone_number', 'like', "%{$keyword}%");
            })
            ->latest()
            ->limit(10)
            ->get();

        return response()->json($staffs);
    }
}
