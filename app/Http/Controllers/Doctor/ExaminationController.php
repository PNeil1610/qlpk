<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Medicine;
use Illuminate\Http\Request;

class ExaminationController extends Controller
{
    /**
     * 📋 Danh sách ca khám của bác sĩ (theo phòng được chỉ định)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->staff || !$user->staff->room_id) {
            return redirect()->back()->with('error', 'Bạn không có phòng khám được chỉ định. Vui lòng liên hệ quản trị viên cập nhật phòng khám cho bạn.');
        }

        $roomId = $user->staff->room_id;
        $roomName = $user->staff->room->room_name ?? 'Phòng khám';

        $consultations = Consultation::with('patient')->whereHas('rooms', function ($q) use ($roomId) {
            $q->where('id', $roomId);
        })->latest()->paginate(10);

        return view('doctor.doctor_index', compact('consultations', 'roomName'));
    }

    /**
     * 🔍 AJAX tìm kiếm ca khám của bác sĩ
     */
    public function ajaxSearch(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->staff || !$user->staff->room_id) {
            return response()->json([]);
        }

        $roomId = $user->staff->room_id;
        $keyword = $request->keyword;

        $consultations = Consultation::with('patient')
            ->whereHas('rooms', function ($q) use ($roomId) {
                $q->where('id', $roomId);
            })
            ->when($keyword, function ($query) use ($keyword) {
                $query->whereHas('patient', function ($q) use ($keyword) {
                    $q->where('full_name', 'like', "%$keyword%")
                        ->orWhere('patient_code', 'like', "%$keyword%")
                        ->orWhere('phone_number', 'like', "%$keyword%");
                });
            })
            ->latest()
            ->get();

        return response()->json($consultations);
    }

    /**
     * 🩺 Giao diện khám bệnh
     */
    public function examine($id)
    {
        $consultation = Consultation::with(['patient', 'rooms', 'prescriptions'])->findOrFail($id);

        // Lấy lịch sử phiên khám của bệnh nhân (chỉ các phiên đã hoàn thành, trừ phiên hiện tại)
        $history = Consultation::with(['prescriptions.details.medicine', 'diagnosis'])
            ->where('patient_id', $consultation->patient_id)
            ->where('id', '!=', $id)
            ->where('status', Consultation::STATUS_COMPLETED)
            ->latest()
            ->get();

        $medicines = Medicine::with([
            'details' => function ($q) {
                $q->where('status', 1);
            }
        ])->where('status', 1)->get();

        return view('doctor.examine', compact('consultation', 'medicines', 'history'));
    }

    /**
     * 🏥 Danh sách bệnh nhân trong phòng của bác sĩ
     */
    public function myRoomPatients()
    {
        $user = auth()->user();
        if (!$user || !$user->staff || !$user->staff->room_id) {
            return redirect()->back()->with('error', 'Bạn không có phòng khám được chỉ định.');
        }

        $roomId = $user->staff->room_id;
        $roomName = $user->staff->room->room_name ?? 'Phòng khám';

        $consultations = Consultation::with('patient')->whereHas('rooms', function ($q) use ($roomId) {
            $q->where('id', $roomId);
        })->latest()->paginate(10);

        return view('doctor.my_room', compact('consultations', 'roomName'));
    }
}
