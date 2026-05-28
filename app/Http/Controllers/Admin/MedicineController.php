<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\MedicineDetail;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;
        
        $query = Medicine::with([
            'category', 
            'details' => function($query) {
                $query->where('status', 1);
            }
        ]);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('medicine_name', 'like', "%$keyword%")
                  ->orWhere('medicine_code', 'like', "%$keyword%")
                  ->orWhereHas('details', function ($subQuery) use ($keyword) {
                      $subQuery->where('active_ingredient', 'like', "%$keyword%");
                  });
            });
        }

        $medicines = $query->orderBy('id', 'desc')->paginate(10);
        
        return view('medicines.index', compact('medicines', 'keyword'));
    }

    public function create()
    {
        $categories = MedicineCategory::where('status', 1)->get();
        return view('medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'medicine_category_id' => 'required|exists:medicine_category,id',
            'medicine_name'        => 'required|string|max:150',
        ], [
            'medicine_name.required' => 'Vui lòng nhập tên thuốc.',
        ]);

        $medicine = Medicine::create([
            'medicine_category_id' => $request->medicine_category_id,
            'medicine_name'        => $request->medicine_name,
            'note'                 => $request->note,
            'status'               => $request->status ?? 1,
        ]);

        $code = 'TH' . str_pad($medicine->id, 4, '0', STR_PAD_LEFT);
        $medicine->update(['medicine_code' => $code]);

        return redirect()->route('medicines.show', $medicine->id)
                         ->with('success', 'Đã khởi tạo thuốc thành công. Vui lòng thêm Đơn vị tính và Giá bán!');
    }
    
    public function show($id)
    {
        $medicine = Medicine::with(['category', 'details'])->findOrFail($id);
        return view('medicines.show', compact('medicine'));
    }

    public function edit($id)
    {
        $medicine = Medicine::with('details')->findOrFail($id);
        $categories = MedicineCategory::where('status', 1)->get();
        $currentDetail = $medicine->details()->where('status', 1)->first();

        return view('medicines.edit', compact('medicine', 'categories', 'currentDetail'));
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'medicine_category_id' => 'required|exists:medicine_category,id',
            'medicine_name'        => 'required|string|max:150',
        ]);

        $medicine->update([
            'medicine_category_id' => $request->medicine_category_id,
            'medicine_name'        => $request->medicine_name,
            'note'                 => $request->note,
            'status'               => $request->status,
        ]);

        return redirect()->route('medicines.show', $medicine->id)
                         ->with('success', 'Đã cập nhật thông tin gốc của thuốc.');
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        
        try {
            $medicine->details()->delete(); 
            $medicine->delete();
            return redirect()->route('medicines.index')->with('success', 'Đã xóa thuốc khỏi hệ thống.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể xóa vì thuốc này có dữ liệu liên quan.');
        }
    }

    public function details($id)
    {
        $medicine = Medicine::findOrFail($id);
        $details = $medicine->details()
                            ->orderBy('status', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('medicines_detail.index', compact('medicine', 'details'));
    }

    public function editDetail($detail_id)
    {
        $detail = MedicineDetail::with('medicine')->findOrFail($detail_id);
        return view('medicines_detail.edit', compact('detail'));
    }

    public function updateDetail(Request $request, $detail_id)
    {
        $detail = MedicineDetail::findOrFail($detail_id);
        $detail->update($request->validate([
            'unit'  => 'required|string',
            'price' => 'required|numeric|min:0',
            'active_ingredient' => 'nullable|string',
            'storage_condition' => 'nullable|string',
        ]));

        return redirect()->route('medicines.details', $detail->medicine_id)
                         ->with('success', 'Đã cập nhật chi tiết thành công.');
    }

    public function destroyDetail($detail_id)
    {
        $detail = MedicineDetail::findOrFail($detail_id);
        $mid = $detail->medicine_id;

        if ($detail->status == 1) {
            return redirect()->back()->with('error', 'Không thể xóa phiên bản đang áp dụng.');
        }

        $detail->delete();
        return redirect()->route('medicines.details', $mid)->with('success', 'Đã xóa chi tiết giá.');
    }

    public function createDetail($id)
    {
        $medicine = Medicine::findOrFail($id);
        return view('medicines_detail.create', compact('medicine'));
    }

    public function storeDetail(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        
        $request->validate([
            'unit'  => 'required|string',
            'price' => 'required|numeric|min:0',
            'active_ingredient' => 'nullable|string',
            'storage_condition' => 'nullable|string',
        ]);

        DB::transaction(function() use ($medicine, $request) {
            MedicineDetail::where('medicine_id', $medicine->id)->update(['status' => 0]);

            MedicineDetail::create([
                'medicine_id'       => $medicine->id,
                'unit'              => $request->unit,
                'price'             => $request->price,
                'active_ingredient' => $request->active_ingredient,
                'storage_condition' => $request->storage_condition,
                'status'            => 1,
            ]);
        });

        return redirect()->route('medicines.details', $medicine->id)
                         ->with('success', 'Đã thêm phiên bản đơn giá mới và áp dụng thành công.');
    }

    /**
     * 📥 Giao diện import Thuốc
     */
    public function importForm()
    {
        $categories = MedicineCategory::where('status', 1)->orderBy('category_name')->get();
        return view('medicines.import', compact('categories'));
    }

    /**
     * 📄 Tải xuống file CSV mẫu Thuốc
     */
    public function downloadTemplate()
    {
        $filename = "mau_nhap_thuoc.csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Loại bỏ cột Loại thuốc khỏi file Excel/CSV mẫu
        $columns = ['Tên thuốc', 'Đơn vị tính', 'Giá bán', 'Hoạt chất', 'Điều kiện bảo quản', 'Ghi chú'];
        $exampleData = [
            ['Paracetamol 500mg', 'Viên', '2000', 'Paracetamol', 'Nơi khô ráo, dưới 30 độ C', 'Uống sau khi ăn no'],
            ['Amoxicillin 500mg', 'Viên', '3500', 'Amoxicillin', 'Nhiệt độ phòng', 'Uống đủ liều theo đơn bác sĩ'],
            ['Siro Prospan 100ml', 'Chai', '75000', 'Cao khô lá thường xuân', 'Tránh ánh nắng trực tiếp', 'Lắc kỹ trước khi sử dụng']
        ];

        $callback = function() use($columns, $exampleData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);
            foreach ($exampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * 💾 Xử lý import Thuốc từ file CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:2048',
            'duplicate_behavior' => 'required|in:skip,update',
            'medicine_category_id' => 'required|exists:medicine_category,id', // Kiểm tra loại thuốc được chọn từ UI
        ]);

        $file = $request->file('csv_file');
        
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt'])) {
            return redirect()->back()->with('error', 'Chỉ chấp nhận file có định dạng CSV (.csv).');
        }

        $path = $file->getRealPath();
        $content = file_get_contents($path);

        if (!$content) {
            return redirect()->back()->with('error', 'File không có dữ liệu hoặc không thể đọc được.');
        }

        if (!mb_check_encoding($content, 'UTF-8')) {
            $desiredEncodings = ['UTF-16', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1', 'ASCII', 'CP1258', 'CP1252', 'Windows-1258', 'Windows-1252'];
            $supportedEncodings = array_intersect($desiredEncodings, mb_list_encodings());
            $encoding = mb_detect_encoding($content, $supportedEncodings, true);
            if ($encoding && $encoding !== 'UTF-8') {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }
        }

        if (strpos($content, "\xEF\xBB\xBF") === 0) {
            $content = substr($content, 3);
        }

        $lines = explode("\n", str_replace("\r", "", $content));
        
        $totalRows = 0;
        $successCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        $errorsList = [];

        $firstLine = $lines[0] ?? '';
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false && (substr_count($firstLine, ';') > substr_count($firstLine, ','))) {
            $delimiter = ';';
        }

        $isHeader = true;

        // Lấy thông tin loại thuốc được chọn từ giao diện web
        $category = MedicineCategory::findOrFail($request->medicine_category_id);

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            $row = str_getcsv($line, $delimiter);

            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $totalRows++;
            $rowNum = $index + 1;

            if (count($row) < 3) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Thiếu cột dữ liệu tối thiểu (Tên thuốc, Đơn vị tính, Giá bán).";
                continue;
            }

            // Đọc dữ liệu (đã bỏ cột Loại thuốc ra khỏi cấu trúc file)
            $name = trim($row[0] ?? '');
            $unit = trim($row[1] ?? '');
            $priceStr = trim($row[2] ?? '');
            $activeIngredient = trim($row[3] ?? '');
            $storageCondition = trim($row[4] ?? '');
            $note = trim($row[5] ?? '');

            // Validate dữ liệu
            if (empty($name)) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên thuốc không được để trống.";
                continue;
            }

            if (strlen($name) > 150) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên thuốc quá dài (tối đa 150 ký tự).";
                continue;
            }

            if (empty($unit)) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Đơn vị tính không được để trống.";
                continue;
            }

            if (empty($priceStr) || !is_numeric($priceStr) || floatval($priceStr) < 0) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Giá bán phải là số không âm.";
                continue;
            }

            $price = floatval($priceStr);

            // Kiểm tra thuốc đã tồn tại trong nhóm này chưa
            $existing = Medicine::where('medicine_name', $name)
                ->where('medicine_category_id', $category->id)
                ->first();

            try {
                if ($existing) {
                    if ($request->duplicate_behavior === 'skip') {
                        $skippedCount++;
                    } else {
                        // Cập nhật thông tin thuốc gốc
                        $existing->update([
                            'note' => $note ?: null,
                        ]);

                        // Cập nhật/Thêm đơn giá mới
                        DB::transaction(function() use ($existing, $unit, $price, $activeIngredient, $storageCondition) {
                            // Tắt kích hoạt các phiên bản giá cũ
                            MedicineDetail::where('medicine_id', $existing->id)->update(['status' => 0]);
                            
                            // Tạo phiên bản giá mới hoạt động
                            MedicineDetail::create([
                                'medicine_id'       => $existing->id,
                                'unit'              => $unit,
                                'price'             => $price,
                                'active_ingredient' => $activeIngredient ?: null,
                                'storage_condition' => $storageCondition ?: null,
                                'status'            => 1,
                            ]);
                        });
                        $updatedCount++;
                    }
                } else {
                    // Tạo mới thuốc gốc
                    DB::transaction(function() use ($category, $name, $note, $unit, $price, $activeIngredient, $storageCondition, &$successCount) {
                        $medicine = Medicine::create([
                            'medicine_category_id' => $category->id,
                            'medicine_name'        => $name,
                            'note'                 => $note ?: null,
                            'status'               => 1,
                        ]);

                        // Auto sinh mã thuốc THxxxx
                        $code = 'TH' . str_pad($medicine->id, 4, '0', STR_PAD_LEFT);
                        $medicine->update(['medicine_code' => $code]);

                        // Tạo chi tiết đơn giá kích hoạt ngay
                        MedicineDetail::create([
                            'medicine_id'       => $medicine->id,
                            'unit'              => $unit,
                            'price'             => $price,
                            'active_ingredient' => $activeIngredient ?: null,
                            'storage_condition' => $storageCondition ?: null,
                            'status'            => 1,
                        ]);
                        
                        $successCount++;
                    });
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Lỗi lưu thuốc vào cơ sở dữ liệu (" . $e->getMessage() . ").";
            }
        }

        $results = [
            'total' => $totalRows,
            'success' => $successCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'errors_count' => $errorCount,
            'errors' => $errorsList
        ];

        return redirect()->route('medicines.import.form')->with('import_results', $results);
    }

    /**
     * AJAX: Tìm kiếm thuốc theo keyword và/hoặc category_id
     */
    public function ajaxSearch(Request $request)
    {
        $keyword    = $request->keyword;
        $categoryId = $request->category_id;

        $medicines = Medicine::with([
                'details' => fn($q) => $q->where('status', 1),
                'category',
            ])
            ->where('status', 1)
            ->when($categoryId, fn($q) => $q->where('medicine_category_id', $categoryId))
            ->when($keyword,    fn($q) => $q->where('medicine_name', 'like', "%$keyword%"))
            ->orderBy('medicine_name')
            ->limit(50)
            ->get()
            ->map(function ($m) {
                $detail = $m->details->first();
                return [
                    'id'            => $m->id,
                    'medicine_name' => $m->medicine_name,
                    'medicine_code' => $m->medicine_code,
                    'category_name' => $m->category?->category_name ?? '',
                    'unit'          => $detail?->unit  ?? '',
                    'price'         => $detail?->price ?? 0,
                ];
            });

        return response()->json($medicines);
    }
}
