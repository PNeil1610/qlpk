<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * 📋 Danh sách phiên khám
     */
    public function index(Request $request)
    {
        $query = Consultation::with('patient');

        // 🔍 tìm kiếm theo tên bệnh nhân
        if ($request->keyword) {
            $keyword = $request->keyword;

            $query->whereHas('patient', function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%$keyword%")
                    ->orWhere('patient_code', 'like', "%$keyword%")
                    ->orWhere('phone_number', 'like', "%$keyword%");
            });
        }

        $consultations = $query->latest()->paginate(10);

        return view('consultations.index', compact('consultations'));
    }

    /**
     * ➕ Form tạo phiên khám
     */
    public function create(Request $request)
    {
        $selectedPatient = null;

        if ($request->patient_id) {
            $selectedPatient = Patient::find($request->patient_id);
        }

        $rooms = \App\Models\Room::where('status', \App\Models\Room::STATUS_AVAILABLE)->get();

        return view('consultations.create', compact(
            'selectedPatient',
            'rooms'
        ));
    }
    /**
     * 💾 Lưu phiên khám
     */
    public function store(Request $request)
    {
        // $validated = $request->validate([
        //     'patient_id' => 'required|exists:patient,id',
        //     'consultation_date' => 'required|date',
        //     'symptoms' => 'nullable|string',
        //     'temperature' => 'nullable|numeric|min:30|max:45',
        //     'blood_pressure' => 'nullable|string|max:20',
        //     'chief_complaint' => 'nullable|string|max:500',
        // ]);

        $validated = $request->validate([

            'patient_id' => 'required|exists:patient,id',
            'room_id' => 'nullable|exists:room,id',
            'symptoms' => 'nullable|string',

            'temperature' => 'nullable|numeric|min:30|max:45',

            'blood_pressure' => 'nullable|string|max:20',

            'chief_complaint' => 'nullable|string|max:500',

            'diagnosis_code' => 'nullable|string|max:15',

            'diagnosis_notes' => 'nullable|string',

            'follow_up_date' => 'nullable|date',
        ]);

        $consultation = new Consultation();
        $consultation->fill($validated);

        // mặc định trạng thái
        $consultation->status = Consultation::STATUS_NEW;

        // auto số thứ tự
        $consultation->order_number = time();

        $consultation->save();

        // Nếu có chọn phòng khám, tạo bản ghi consultation_room
        if ($request->room_id) {
            \App\Models\ConsultationRoom::create([
                'consultation_id' => $consultation->id,
                'room_id' => $request->room_id,
                'status' => 1, // Đang chờ
            ]);
        }

        return redirect()->route('consultations.index')
            ->with('success', 'Tạo phiên khám thành công');
    }

    /**
     * 🔍 Chi tiết
     */
    public function show($id)
    {
        $consultation = Consultation::with('patient')->findOrFail($id);

        return view('consultations.show', compact('consultation'));
    }

    /**
     * ✏️ Form sửa
     */
    public function edit($id)
    {
        $consultation = Consultation::with('rooms')->findOrFail($id);
        $patients = Patient::get(['id', 'full_name', 'patient_code']);
        $rooms = \App\Models\Room::where('status', \App\Models\Room::STATUS_AVAILABLE)->get();

        return view('consultations.edit', compact('consultation', 'patients', 'rooms'));
    }

    /**
     * 🔄 Cập nhật
     */
    public function update(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patient,id',
            'room_id' => 'nullable|exists:room,id',
            'consultation_date' => 'required|date',
            'symptoms' => 'nullable|string',
            'chief_complaint' => 'nullable|string|max:500',
            'diagnosis_code' => 'nullable|string|max:15',
            'diagnosis_notes' => 'nullable|string',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'blood_pressure' => 'nullable|string|max:20',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|integer',
        ]);

        $consultation->update($validated);

        // Cập nhật phòng khám
        if ($request->has('room_id')) {
            $consultationRoom = \App\Models\ConsultationRoom::where('consultation_id', $consultation->id)->first();
            
            if ($request->room_id) {
                if ($consultationRoom) {
                    $consultationRoom->update(['room_id' => $request->room_id]);
                } else {
                    \App\Models\ConsultationRoom::create([
                        'consultation_id' => $consultation->id,
                        'room_id' => $request->room_id,
                        'status' => 1,
                    ]);
                }
            } else {
                if ($consultationRoom) {
                    $consultationRoom->delete();
                }
            }
        }

        return redirect()->route('consultations.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * 🗑️ Xóa
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);
        $consultation->delete();

        return redirect()->route('consultations.index')
            ->with('success', 'Xóa thành công');
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $consultations = Consultation::with('patient')

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
    public function doctorConsultations(Request $request)
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

    public function doctorAjaxSearch(Request $request)
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
}