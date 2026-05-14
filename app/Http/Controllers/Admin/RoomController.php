<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Department;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('department')->latest()->paginate(10);
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $departments = Department::where('status', 1)->orderBy('dept_name')->get();
        return view('rooms.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_code' => 'required|string|max:30|unique:room,room_code',
            'room_name' => 'required|string|max:150',
            'room_type' => 'required|in:Examination,Lab,Pharmacy,Emergency,Waiting,Administrative',
            'department_id' => 'nullable|exists:department,id',
            'status' => 'required|in:0,1,2,3',
        ]);

        Room::create($request->all());

        return redirect()->route('rooms.index')->with('success', 'Thêm phòng thành công');
    }

    public function show($id)
    {
        $room = Room::with('department')->findOrFail($id);
        return view('rooms.show', compact('room'));
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        $departments = Department::where('status', 1)->orderBy('dept_name')->get();
        return view('rooms.edit', compact('room', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $request->validate([
            'room_code' => 'required|string|max:30|unique:room,room_code,' . $room->id,
            'room_name' => 'required|string|max:150',
            'room_type' => 'required|in:Examination,Lab,Pharmacy,Emergency,Waiting,Administrative',
            'department_id' => 'nullable|exists:department,id',
            'status' => 'required|in:0,1,2,3',
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Cập nhật phòng thành công');
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Xóa phòng thành công');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $rooms = Room::with('department')
            ->where('room_code', 'LIKE', "%{$keyword}%")
            ->orWhere('room_name', 'LIKE', "%{$keyword}%")
            ->orWhereHas('department', function ($q) use ($keyword) {
                $q->where('dept_name', 'LIKE', "%{$keyword}%");
            })
            ->latest()
            ->get();

        return response()->json($rooms);
    }
}
