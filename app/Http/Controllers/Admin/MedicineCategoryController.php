<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MedicineCategory;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MedicineCategoryController extends Controller
{
    public function index()
    {
        $categories = MedicineCategory::latest()->paginate(10);
        return view('medicine_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('medicine_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:150|unique:medicine_category,category_name',
            'description'   => 'nullable|string',
            'status'        => 'required|in:0,1',
        ], [
            'category_name.required' => 'Vui lòng nhập tên loại thuốc.',
            'category_name.unique'   => 'Tên loại thuốc này đã tồn tại trong hệ thống.'
        ]);

        // Kiểm tra trùng lặp gần đúng (Fuzzy Duplicate)
        $normalizedNew = $this->normalizeCategoryName($request->category_name);
        $existingCategories = MedicineCategory::all();
        foreach ($existingCategories as $existing) {
            if ($this->normalizeCategoryName($existing->category_name) === $normalizedNew) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['category_name' => "Tên loại thuốc này ('{$request->category_name}') rất giống với nhóm thuốc '{$existing->category_name}' (Mã: {$existing->category_code}) đã có sẵn. Vui lòng sử dụng nhóm thuốc sẵn có hoặc sử dụng tính năng 'Gộp Loại Thuốc' ngoài danh sách để hợp nhất."]);
            }
        }

        $category = MedicineCategory::create([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);

        $code = 'LT' . str_pad($category->id, 3, '0', STR_PAD_LEFT);
        $category->update(['category_code' => $code]);

        return redirect()->route('medicine_categories.index')
                         ->with('success', 'Đã thêm nhóm thuốc: ' . $category->category_name . ' thành công!');
    }

    public function show($id)
    {
        $category = MedicineCategory::findOrFail($id);
        return view('medicine_categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = MedicineCategory::findOrFail($id);
        return view('medicine_categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = MedicineCategory::findOrFail($id);

        $request->validate([
            'category_name' => 'required|string|max:150|unique:medicine_category,category_name,' . $category->id,
            'description'   => 'nullable|string',
            'status'        => 'required|in:0,1',
        ], [
            'category_name.required' => 'Vui lòng nhập tên loại thuốc.',
            'category_name.unique'   => 'Tên loại thuốc này đã tồn tại trong hệ thống.'
        ]);

        // Kiểm tra trùng lặp gần đúng (Fuzzy Duplicate) ngoại trừ chính nó
        $normalizedNew = $this->normalizeCategoryName($request->category_name);
        $existingCategories = MedicineCategory::where('id', '!=', $category->id)->get();
        foreach ($existingCategories as $existing) {
            if ($this->normalizeCategoryName($existing->category_name) === $normalizedNew) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['category_name' => "Tên loại thuốc này ('{$request->category_name}') rất giống với nhóm thuốc '{$existing->category_name}' (Mã: {$existing->category_code}) đã có sẵn. Vui lòng chọn tên khác hoặc sử dụng tính năng 'Gộp Loại Thuốc' ngoài danh sách để hợp nhất."]);
            }
        }

        $category->update([
            'category_name' => $request->category_name,
            'description'   => $request->description,
            'status'        => $request->status,
        ]);

        return redirect()->route('medicine_categories.index')
                         ->with('success', 'Cập nhật nhóm thuốc: ' . $category->category_name . ' thành công!');
    }

    public function destroy($id)
    {
        $category = MedicineCategory::findOrFail($id);
        $categoryName = $category->category_name;

        try {
            $category->delete();
            return redirect()->route('medicine_categories.index')
                             ->with('success', 'Đã xóa nhóm thuốc: ' . $categoryName . ' thành công!');
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return redirect()->route('medicine_categories.index')
                    ->with('error', 'Không thể xóa nhóm thuốc "' . $categoryName . '" vì vẫn còn thuốc thuộc nhóm này. Vui lòng chuyển hoặc xóa các thuốc trong nhóm trước.');
            }
            return redirect()->route('medicine_categories.index')
                ->with('error', 'Đã xảy ra lỗi khi xóa nhóm thuốc. Vui lòng thử lại.');
        }
    }

    /**
     * 📥 Giao diện import Loại thuốc
     */
    public function importForm()
    {
        return view('medicine_categories.import');
    }

    /**
     * 📄 Tải xuống file CSV mẫu Loại thuốc
     */
    public function downloadTemplate()
    {
        $filename = "mau_nhap_loai_thuoc.csv";
        
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tên loại thuốc', 'Mô tả'];
        $exampleData = [
            ['Thuốc kháng sinh', 'Các loại thuốc tiêu diệt hoặc kìm hãm vi khuẩn phát triển'],
            ['Thuốc giảm đau hạ sốt', 'Các nhóm Paracetamol, NSAIDs giảm đau đầu, sốt, đau răng'],
            ['Thuốc ho và bổ phế', 'Các siro ho, viên ngậm trị ho đờm, ho khan']
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
     * 💾 Xử lý import Loại thuốc từ file CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|max:2048',
            'duplicate_behavior' => 'required|in:skip,update',
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
        $similarWarningsList = [];

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

            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            $totalRows++;
            $rowNum = $index + 1;

            if (count($row) < 1) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Thiếu cột dữ liệu tối thiểu (Tên loại thuốc).";
                continue;
            }

            $name = trim($row[0] ?? '');
            $desc = trim($row[1] ?? '');

            if (empty($name)) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên loại thuốc không được để trống.";
                continue;
            }

            if (strlen($name) > 150) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Tên loại thuốc quá dài (tối đa 150 ký tự).";
                continue;
            }

            // 1. Kiểm tra khớp chính xác tên loại thuốc
            $existing = MedicineCategory::where('category_name', $name)->first();

            // 2. Kiểm tra khớp gần đúng (Fuzzy Duplicate)
            $fuzzyMatch = null;
            if (!$existing) {
                $normalizedNew = $this->normalizeCategoryName($name);
                $existingCategories = MedicineCategory::all();
                foreach ($existingCategories as $existingCat) {
                    if ($this->normalizeCategoryName($existingCat->category_name) === $normalizedNew) {
                        $fuzzyMatch = $existingCat;
                        break;
                    }
                }
            }

            try {
                if ($existing || $fuzzyMatch) {
                    $targetCat = $existing ?: $fuzzyMatch;
                    $isFuzzy = !$existing && $fuzzyMatch;

                    if ($request->duplicate_behavior === 'skip') {
                        $skippedCount++;
                        if ($isFuzzy) {
                            $similarWarningsList[] = "Dòng <b>$rowNum</b>: Tên nhóm thuốc <code>" . htmlspecialchars($name) . "</code> trùng lặp gần đúng với nhóm thuốc <code>" . htmlspecialchars($targetCat->category_name) . "</code> (Mã: <code>{$targetCat->category_code}</code>) đã có sẵn. Hệ thống đã <b>tự động bỏ qua</b> để tránh tạo nhóm trùng lặp.";
                        }
                    } else {
                        $targetCat->update([
                            'description' => $desc ?: $targetCat->description,
                        ]);
                        $updatedCount++;
                        if ($isFuzzy) {
                            $similarWarningsList[] = "Dòng <b>$rowNum</b>: Tên nhóm thuốc <code>" . htmlspecialchars($name) . "</code> trùng lặp gần đúng với nhóm thuốc <code>" . htmlspecialchars($targetCat->category_name) . "</code> (Mã: <code>{$targetCat->category_code}</code>). Hệ thống đã <b>tự động cập nhật</b> mô tả mới vào nhóm thuốc sẵn có.";
                        }
                    }
                } else {
                    $category = MedicineCategory::create([
                        'category_name' => $name,
                        'description' => $desc ?: null,
                        'status' => 1,
                    ]);
                    
                    // Auto-generate category code: LT001, LT002...
                    $code = 'LT' . str_pad($category->id, 3, '0', STR_PAD_LEFT);
                    $category->update(['category_code' => $code]);
                    
                    $successCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errorsList[] = "Dòng $rowNum: Lỗi hệ thống khi lưu dữ liệu (" . $e->getMessage() . ").";
            }
        }

        $results = [
            'total' => $totalRows,
            'success' => $successCount,
            'updated' => $updatedCount,
            'skipped' => $skippedCount,
            'errors_count' => $errorCount,
            'errors' => $errorsList,
            'similar_warnings' => $similarWarningsList
        ];

        return redirect()->route('medicine_categories.import.form')->with('import_results', $results);
    }


    /**
     * 🔀 Giao diện gộp loại thuốc
     */
    public function mergeForm()
    {
        $categories = MedicineCategory::where('status', 1)->orderBy('category_name')->get();
        return view('medicine_categories.merge', compact('categories'));
    }

    /**
     * 🔀 Xử lý gộp loại thuốc
     */
    public function merge(Request $request)
    {
        $request->validate([
            'source_category_id' => 'required|exists:medicine_category,id',
            'destination_category_id' => 'required|exists:medicine_category,id|different:source_category_id',
        ], [
            'source_category_id.required' => 'Vui lòng chọn loại thuốc cần gộp (loại thuốc muốn loại bỏ).',
            'destination_category_id.required' => 'Vui lòng chọn loại thuốc đích (loại thuốc muốn giữ lại).',
            'destination_category_id.different' => 'Loại thuốc cần gộp và loại thuốc đích phải khác nhau.'
        ]);

        $sourceId = $request->source_category_id;
        $destinationId = $request->destination_category_id;

        $sourceCategory = MedicineCategory::findOrFail($sourceId);
        $destinationCategory = MedicineCategory::findOrFail($destinationId);

        $sourceName = $sourceCategory->category_name;
        $destinationName = $destinationCategory->category_name;

        DB::transaction(function () use ($sourceId, $destinationId, $sourceCategory) {
            // Cập nhật toàn bộ thuốc thuộc nhóm nguồn sang nhóm đích
            Medicine::where('medicine_category_id', $sourceId)->update(['medicine_category_id' => $destinationId]);
            
            // Xóa nhóm thuốc nguồn
            $sourceCategory->delete();
        });

        return redirect()->route('medicine_categories.index')
            ->with('success', "Đã gộp thành công! Toàn bộ thuốc thuộc nhóm '$sourceName' đã được chuyển sang nhóm '$destinationName' và xóa nhóm dư thừa khỏi hệ thống.");
    }

    public function ajaxSearch(Request $request)
    {
        $keyword = $request->keyword;

        $categories = MedicineCategory::query()
            ->when($keyword, function ($query) use ($keyword) {
                $query->where('category_code', 'like', "%$keyword%")
                      ->orWhere('category_name', 'like', "%$keyword%");
            })
            ->latest()
            ->get();

        return response()->json($categories);
    }

    /**
     * Chuẩn hóa tên loại thuốc để so sánh trùng lặp gần đúng (Fuzzy Duplicate)
     */
    private function normalizeCategoryName($name)
    {
        $str = mb_strtolower($name, 'UTF-8');
        
        // Loại bỏ dấu tiếng Việt
        $unicode = [
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        ];
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        // Loại bỏ các từ tiền tố/hậu tố phổ biến trong phân loại nhóm thuốc
        $str = preg_replace('/^(thuoc|nhom|loai|dang|che pham)\s+/', '', $str);
        $str = preg_replace('/\s+(thuoc|nhom|loai|dang|che pham)$/', '', $str);
        
        // Loại bỏ tất cả khoảng trắng và ký tự đặc biệt
        $str = preg_replace('/[^a-z0-9]/', '', $str);
        
        return trim($str);
    }
}
