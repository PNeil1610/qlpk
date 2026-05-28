<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    /**
     * 📋 Danh sách mã bệnh
     */
    public function index(Request $request)
    {
        $query = Diagnosis::query();

        // 🔍 tìm kiếm
        if ($request->keyword) {

            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {

                $q->where('diagnosis_code', 'like', "%$keyword%")
                    ->orWhere('diagnosis_name', 'like', "%$keyword%");
            });
        }

        $diagnoses = $query->latest()->paginate(10);

        return view('diagnoses.index', compact('diagnoses'));
    }

    /**
     * ➕ Form tạo
     */
    public function create()
    {
        return view('diagnoses.create');
    }

    /**
     * 💾 Lưu
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'diagnosis_code' => 'required|string|max:15|unique:diagnosis,diagnosis_code',

            'diagnosis_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'icd_version' => 'nullable|string|max:10',
        ]);

        $diagnosis = new Diagnosis();

        $diagnosis->fill($validated);

        // mặc định active
        $diagnosis->status = Diagnosis::STATUS_ACTIVE;

        $diagnosis->save();

        return redirect()->route('diagnoses.index')
            ->with('success', 'Thêm mã bệnh thành công');
    }

    /**
     * 🔍 Chi tiết
     */
    public function show($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        return view('diagnoses.show', compact('diagnosis'));
    }

    /**
     * ✏️ Form sửa
     */
    public function edit($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        return view('diagnoses.edit', compact('diagnosis'));
    }

    /**
     * 🔄 Cập nhật
     */
    public function update(Request $request, $id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        $validated = $request->validate([

            'diagnosis_code' =>
                "required|string|max:15|unique:diagnosis,diagnosis_code,$id",

            'diagnosis_name' => 'required|string|max:150',

            'description' => 'nullable|string',

            'icd_version' => 'nullable|string|max:10',

            'status' => 'required|in:0,1',
        ]);

        $diagnosis->update($validated);

        return redirect()->route('diagnoses.index')
            ->with('success', 'Cập nhật thành công');
    }

    /**
     * 🗑️ Xóa
     */
    public function destroy($id)
    {
        $diagnosis = Diagnosis::findOrFail($id);

        // 🔥 kiểm tra đã dùng trong phiên khám chưa
        if ($diagnosis->consultations()->exists()) {

            return redirect()->route('diagnoses.index')
                ->with(
                    'error',
                    'Không thể xóa vì mã bệnh đã được sử dụng.'
                );
        }

        $diagnosis->delete();

        return redirect()->route('diagnoses.index')
            ->with('success', 'Xóa thành công');
    }

    /**
     * 📥 Giao diện import mã bệnh
     */
    public function importForm()
    {
        return view('diagnoses.import');
    }

    /**
     * 📄 Tải xuống file CSV mẫu với BOM tiếng Việt
     */
    public function downloadTemplate()
    {
        $filename = "mau_nhap_ma_benh.csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Mã bệnh', 'Tên bệnh', 'Phiên bản ICD', 'Mô tả'];
        $exampleData = [
            ['A00', 'Bệnh tả', 'ICD-10', 'Bệnh truyền nhiễm cấp tính do vi khuẩn gây ra'],
            ['B01', 'Bệnh thủy đậu', 'ICD-10', 'Bệnh do vi-rút varicella-zoster gây nên'],
            ['C00', 'Ung thư môi', 'ICD-10', 'Bệnh ác tính vùng môi']
        ];

        $callback = function() use($columns, $exampleData) {
            $file = fopen('php://output', 'w');
            
            // Xuất UTF-8 BOM để Excel hiển thị tiếng Việt chính xác
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Sử dụng dấu phẩy làm phân tách cột
            fputcsv($file, $columns);

            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 💾 Xử lý import mã bệnh từ file CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:2048',
            'duplicate_behavior' => 'required|in:skip,update',
        ]);

        $file = $request->file('csv_file');
        
        // Kiểm tra phần mở rộng file
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Chỉ chấp nhận file có định dạng CSV (.csv).');
        }

        $path = $file->getRealPath();
        $content = file_get_contents($path);

        if (!$content) {
            return redirect()->back()->with('error', 'File không có dữ liệu hoặc không thể đọc được.');
        }

        // Kiểm tra xem file đã là UTF-8 chuẩn chưa để tránh lỗi double-encoding (lỗi hiển thị chữ lạ)
        if (!mb_check_encoding($content, 'UTF-8')) {
            // Chỉ phát hiện và convert nếu file KHÔNG phải là UTF-8
            $desiredEncodings = ['UTF-16', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1', 'ASCII', 'CP1258', 'CP1252', 'Windows-1258', 'Windows-1252'];
            $supportedEncodings = array_intersect($desiredEncodings, mb_list_encodings());
            $encoding = mb_detect_encoding($content, $supportedEncodings, true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
        }

        // Loại bỏ UTF-8 BOM nếu có
        if (strpos($content, "\xEF\xBB\xBF") === 0) {
            $content = substr($content, 3);
        }

        // Tách thành các dòng
        $lines = explode("\n", str_replace("\r", "", $content));
        
        $totalRows = 0;
        $successCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errorsList = [];

        // Phát hiện delimiter dựa trên dòng đầu tiên
        $firstLine = $lines[0] ?? '';
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false && (substr_count($firstLine, ';') > substr_count($firstLine, ','))) {
            $delimiter = ';';
        }

        $isHeader = true;

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line, $delimiter);

            // Bỏ qua dòng tiêu đề
            if ($isHeader) {
                $isHeader = false;
                // Nếu dòng tiêu đề không chứa từ khóa đặc trưng, ta vẫn bỏ qua nhưng không tính vào dữ liệu
                continue;
            }

            $totalRows++;
            $rowNum = $index + 1;

            // Đảm bảo đủ các cột cần thiết (Mã bệnh, Tên bệnh, Phiên bản ICD, Mô tả)
            if (count($row) < 2) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Thiếu cột dữ liệu tối thiểu (cần có Mã bệnh và Tên bệnh).";
                continue;
            }

            $code = trim($row[0] ?? '');
            $name = trim($row[1] ?? '');
            $icd = trim($row[2] ?? '');
            $desc = trim($row[3] ?? '');

            // Validate dữ liệu dòng
            if (empty($code)) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Mã bệnh không được để trống.";
                continue;
            }

            if (strlen($code) > 15) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Mã bệnh '$code' quá dài (tối đa 15 ký tự).";
                continue;
            }

            if (empty($name)) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên bệnh không được để trống.";
                continue;
            }

            if (strlen($name) > 150) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên bệnh quá dài (tối đa 150 ký tự).";
                continue;
            }

            if (!empty($icd) && strlen($icd) > 10) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Phiên bản ICD '$icd' quá dài (tối đa 10 ký tự).";
                continue;
            }

            // Kiểm tra trùng lặp
            $existing = Diagnosis::where('diagnosis_code', $code)->first();

            try {
                if ($existing) {
                    if ($request->duplicate_behavior === 'skip') {
                        $skippedCount++;
                    } else {
                        // Cập nhật
                        $existing->update([
                            'diagnosis_name' => $name,
                            'icd_version' => $icd ?: null,
                            'description' => $desc ?: null,
                        ]);
                        $updatedCount++;
                    }
                } else {
                    // Thêm mới
                    Diagnosis::create([
                        'diagnosis_code' => $code,
                        'diagnosis_name' => $name,
                        'icd_version' => $icd ?: null,
                        'description' => $desc ?: null,
                        'status' => Diagnosis::STATUS_ACTIVE,
                    ]);
                    $successCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Lỗi hệ thống khi lưu dữ liệu (" . $e->getMessage() . ").";
            }
        }

        // Lưu thông tin kết quả vào Session để hiển thị ở view
        $results = [
            'total' => $totalRows,
            'success' => $successCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'errors_count' => $errorCount,
            'errors' => $errorsList
        ];

        return redirect()->route('diagnoses.import.form')->with('import_results', $results);
    }

    /**
     * 🔥 AJAX SEARCH

     */
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $diagnoses = Diagnosis::query()

            ->when($keyword, function ($q) use ($keyword) {

                $q->where('diagnosis_code', 'like', "%$keyword%")
                    ->orWhere('diagnosis_name', 'like', "%$keyword%");

            })

            ->latest()
            ->limit(20)
            ->get();

        return response()->json($diagnoses);
    }
}
