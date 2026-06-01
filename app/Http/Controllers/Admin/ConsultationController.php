<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ConsultationController extends Controller
{
    /**
     * 📋 Danh sách phiên khám
     */
    public function index(Request $request)
    {
        $query = Consultation::with(['patient', 'rooms']);

        // Lọc phiên khám chưa có phòng
        if ($request->has('filter') && $request->filter == 'no_room') {
            $query->doesntHave('rooms');
        }

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

        // Tự động tạo Hóa đơn (Invoice) cho phiên khám này
        $consultationFee = Invoice::DEFAULT_CONSULTATION_FEE ?? 100000;
        $invoice = Invoice::create([
            'invoice_code' => Invoice::generateInvoiceCode(),
            'consultation_id' => $consultation->id,
            'invoice_date' => now(),
            'consultation_fee' => $consultationFee,
            'lab_test_fee' => 0,
            'medicine_fee' => 0,
            'other_fee' => 0,
            'discount' => 0,
            'total_amount' => $consultationFee,
            'status' => Invoice::STATUS_PENDING,
        ]);

        if ($consultationFee > 0) {
            InvoiceDetail::create([
                'invoice_id' => $invoice->id,
                'item_type' => InvoiceDetail::TYPE_CONSULTATION,
                'item_name' => 'Chi phí khám bệnh',
                'reference_id' => $consultation->id,
                'quantity' => 1,
                'unit_price' => $consultationFee,
                'discount' => 0,
                'line_total' => $consultationFee,
                'status' => 1,
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
        $consultation = Consultation::with([
            'patient',
            'invoice',
            'prescriptions.details.medicine',
            'prescriptions.dispenser',
            'rooms.staff',
            'diagnosis',
        ])->findOrFail($id);

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

        // 🔒 Không cho sửa nếu phiên khám đã hoàn thành thanh toán
        if ($consultation->status === Consultation::STATUS_COMPLETED) {
            return redirect()->route('consultations.index')
                ->with('error', 'Phiên khám #' . $id . ' đã hoàn thành. Không thể chỉnh sửa nội dung.');
        }

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
            $consultationRoomExists = \App\Models\ConsultationRoom::where('consultation_id', $consultation->id)->exists();

            if ($request->room_id) {
                if ($consultationRoomExists) {
                    \App\Models\ConsultationRoom::where('consultation_id', $consultation->id)
                        ->update(['room_id' => $request->room_id]);
                } else {
                    \App\Models\ConsultationRoom::create([
                        'consultation_id' => $consultation->id,
                        'room_id' => $request->room_id,
                        'status' => 1,
                    ]);
                }
            } else {
                \App\Models\ConsultationRoom::where('consultation_id', $consultation->id)->delete();
            }
        }

        // Redirect sau khi update (Admin/Receptionist)
        return redirect()->route('consultations.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * 🗑️ Xóa
     */
    public function destroy($id)
    {
        $consultation = Consultation::findOrFail($id);

        try {
            $consultation->delete();

            return redirect()->route('consultations.index')
                ->with('success', 'Xóa phiên khám thành công');
        } catch (QueryException $e) {
            // Lỗi vi phạm ràng buộc khóa ngoại (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                $message = 'Không thể xóa phiên khám #' . $id . ' vì dữ liệu này đang được liên kết với các bản ghi khác (hóa đơn, đơn thuốc, v.v.). Vui lòng xóa các dữ liệu liên quan trước.';

                return redirect()->route('consultations.index')
                    ->with('error', $message);
            }

            return redirect()->route('consultations.index')
                ->with('error', 'Đã xảy ra lỗi khi xóa phiên khám. Vui lòng thử lại.');
        }
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;
        $filter = $request->filter;

        $consultations = Consultation::with(['patient', 'rooms'])
            ->when($filter == 'no_room', function ($q) {
                $q->doesntHave('rooms');
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
}
