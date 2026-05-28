<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Room;
use App\Models\ConsultationRoom;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ConsultationRoomController extends Controller
{
    public function index()
    {
        $consultationRooms = ConsultationRoom::with([
            'consultation.patient',
            'room'
        ])->paginate(10);

        return view('consultation_rooms.index', compact('consultationRooms'));
    }

    public function create()
    {
        $consultations = Consultation::with('patient')->get();

        $rooms = Room::with('department')->get();

        return view('consultation_rooms.create', compact(
            'consultations',
            'rooms'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([

            'consultation_id' => 'required|exists:consultation,id',

            'room_id' => 'required|exists:room,id',

            'notes' => 'nullable|string',

            'status' => 'required|integer',

        ]);

        ConsultationRoom::create([

            'consultation_id' => $request->consultation_id,

            'room_id' => $request->room_id,

            'notes' => $request->notes,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('consultation_rooms.index')
            ->with('success', 'Phân phòng thành công');
    }

    public function show($consultationId, $roomId)
    {
        $consultationRoom = ConsultationRoom::with([
            'consultation.patient',
            'room.department'
        ])
            ->where('consultation_id', $consultationId)
            ->where('room_id', $roomId)
            ->firstOrFail();

        return view('consultation_rooms.show', compact('consultationRoom'));
    }

    public function edit($consultation_id, $room_id)
    {
        $consultationRoom = ConsultationRoom::where('consultation_id', $consultation_id)
            ->where('room_id', $room_id)
            ->firstOrFail();

        $consultations = Consultation::with('patient')->get();

        $rooms = Room::with('department')->get();

        return view('consultation_rooms.edit', compact(
            'consultationRoom',
            'consultations',
            'rooms'
        ));
    }

    public function update(Request $request, $consultation_id, $room_id)
    {
        $request->validate([

            'consultation_id' => 'required|exists:consultation,id',

            'room_id' => 'required|exists:room,id',

            'notes' => 'nullable|string',

            'status' => 'required|integer',

        ]);

        ConsultationRoom::where('consultation_id', $consultation_id)
            ->where('room_id', $room_id)
            ->update([

                'consultation_id' => $request->consultation_id,

                'room_id' => $request->room_id,

                'notes' => $request->notes,

                'status' => $request->status,

            ]);

        return redirect()
            ->route('consultation_rooms.index')
            ->with('success', 'Cập nhật thành công');
    }

    public function destroy($consultation_id, $room_id)
    {
        try {
            ConsultationRoom::where('consultation_id', $consultation_id)
                ->where('room_id', $room_id)
                ->delete();

            return redirect()
                ->route('consultation_rooms.index')
                ->with('success', 'Xóa thành công');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('consultation_rooms.index')
                    ->with('error', 'Không thể xóa bản ghi này vì đang được liên kết với dữ liệu khác trong hệ thống.');
            }
            return redirect()->route('consultation_rooms.index')
                ->with('error', 'Đã xảy ra lỗi khi xóa. Vui lòng thử lại.');
        }
    }
}
