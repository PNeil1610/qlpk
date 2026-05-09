<?php

namespace App\Http\Controllers;

use App\Models\RoomDetail;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomDetailController extends Controller
{
    public function index()
    {
        $roomDetails = RoomDetail::with('room')->paginate(10);
        return view('room_details.index', compact('roomDetails'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('room_details.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:room,id|unique:room_detail,room_id',
            'equipment_list' => 'nullable|string',
            'area_sqm' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'last_cleaned_at' => 'nullable|date',
        ]);

        RoomDetail::create($validated);

        return redirect()->route('room_details.index')->with('success', 'Thêm chi tiết phòng thành công');
    }

    public function show($id)
    {
        $roomDetail = RoomDetail::with('room')->findOrFail($id);
        return view('room_details.show', compact('roomDetail'));
    }

    public function edit($id)
    {
        $roomDetail = RoomDetail::findOrFail($id);
        $rooms = Room::all();
        return view('room_details.edit', compact('roomDetail', 'rooms'));
    }

    public function update(Request $request, $id)
    {
        $roomDetail = RoomDetail::findOrFail($id);

        $validated = $request->validate([
            'room_id' => 'required|exists:room,id|unique:room_detail,room_id,' . $id,
            'equipment_list' => 'nullable|string',
            'area_sqm' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'last_cleaned_at' => 'nullable|date',
        ]);

        $roomDetail->update($validated);

        return redirect()->route('room_details.index')->with('success', 'Cập nhật chi tiết phòng thành công');
    }

    public function destroy($id)
    {
        $roomDetail = RoomDetail::findOrFail($id);
        $roomDetail->delete();

        return redirect()->route('room_details.index')->with('success', 'Xóa chi tiết phòng thành công');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $roomDetails = RoomDetail::with('room')
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('room', function ($q) use ($keyword) {
                    $q->where('room_code', 'like', "%$keyword%")
                        ->orWhere('room_name', 'like', "%$keyword%");
                });
            })
            ->latest()
            ->get();

        return response()->json($roomDetails);
    }
}
