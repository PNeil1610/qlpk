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
            ->limit(10)
            ->get();

        return response()->json($consultations);
    }

    /**
     * 🩺 Giao diện khám bệnh
     */
    public function examine($id)
    {
        $consultation = Consultation::with(['patient', 'rooms', 'prescriptions'])->findOrFail($id);

        // Tự động chuyển sang trạng thái "Đang khám" nếu đang là mới tạo hoặc chờ khám
        if (in_array($consultation->status, [Consultation::STATUS_NEW, Consultation::STATUS_PENDING])) {
            $consultation->update(['status' => Consultation::STATUS_PROCESSING]);
        }

        // Thuốc sẽ được load qua AJAX trong view, giảm tải băng thông
        $categories = \App\Models\MedicineCategory::where('status', 1)->orderBy('category_name')->get();

        $patientHistory = Consultation::with(['doctor', 'diagnosis', 'prescriptions'])
            ->where('patient_id', $consultation->patient_id)
            ->where('status', Consultation::STATUS_COMPLETED)
            ->where('id', '!=', $id)
            ->latest()
            ->get();

        return view('doctor.examine', compact('consultation', 'categories', 'patientHistory'));
    }

    /**
     * 💾 Lưu kết luận chuyên môn (dành riêng cho Doctor)
     */
    public function saveConclusion(Request $request, $id)
    {
        $consultation = Consultation::findOrFail($id);

        // 🔒 Không cho sửa nếu phiên khám đã hoàn thành thanh toán
        if ($consultation->status === Consultation::STATUS_COMPLETED) {
            return redirect()->route('consultations.doctor')
                ->with('error', 'Phiên khám #' . $id . ' đã hoàn thành. Không thể chỉnh sửa nội dung.');
        }

        $request->validate([
            'diagnosis_notes' => 'nullable|string',
            'diagnosis_code'  => 'nullable|string|max:15',
            'follow_up_date'  => 'nullable|date',
        ]);

        // Tự động nâng lên "Chờ thanh toán" nếu đang ở các bước khám lâm sàng đầu tiên
        $newStatus = $consultation->status;
        if (in_array($consultation->status, [Consultation::STATUS_NEW, Consultation::STATUS_PENDING, Consultation::STATUS_PROCESSING])) {
            $newStatus = Consultation::STATUS_WAIT_PAYMENT;
        }

        $consultation->update([
            'diagnosis_notes' => $request->diagnosis_notes,
            'diagnosis_code'  => $request->diagnosis_code,
            'follow_up_date'  => $request->follow_up_date,
            'status'          => $newStatus,
            'doctor_id'       => auth()->user()->staff->id ?? $consultation->doctor_id,
            // Giữ lại các trường readonly
            'patient_id'        => $request->patient_id ?? $consultation->patient_id,
            'consultation_date'  => $request->consultation_date ?? $consultation->consultation_date,
            'chief_complaint'    => $request->chief_complaint ?? $consultation->chief_complaint,
            'symptoms'           => $request->symptoms ?? $consultation->symptoms,
            'temperature'        => $request->temperature ?? $consultation->temperature,
            'blood_pressure'     => $request->blood_pressure ?? $consultation->blood_pressure,
        ]);

        return redirect()->route('consultations.doctor')
            ->with('success', 'Đã lưu kết luận thành công!');
    }

    /**
     * 📚 Lịch sử khám bệnh của bác sĩ (trang riêng)
     */
    public function myHistory(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->staff) {
            return redirect()->back()->with('error', 'Không tìm thấy thông tin bác sĩ.');
        }

        $staffId = $user->staff->id;
        $keyword = $request->keyword;
        $fromConsultation = $request->from_consultation; // ID phiếu khám hiện tại (nếu đến từ trang khám)

        // Lấy tất cả phiếu khám mà bác sĩ đã kê đơn (qua bảng prescription.prescribed_by)
        $query = Consultation::with(['patient', 'diagnosis'])
            ->whereHas('prescriptions', function ($q) use ($staffId) {
                $q->where('prescribed_by', $staffId);
            })
            ->where('status', Consultation::STATUS_COMPLETED);

        if ($keyword) {
            $query->whereHas('patient', function ($q) use ($keyword) {
                $q->where('full_name', 'like', "%$keyword%")
                    ->orWhere('patient_code', 'like', "%$keyword%")
                    ->orWhere('phone_number', 'like', "%$keyword%");
            });
        }

        $consultations = $query->latest()->paginate(18);
        $doctorName    = $user->staff->full_name ?? $user->name;

        return view('doctor.my_history', compact('consultations', 'doctorName', 'keyword', 'fromConsultation'));
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
