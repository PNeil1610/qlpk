<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;

class SystemSettingController extends Controller
{
    /**
     * Danh sách các cấu hình
     */
    public function index()
    {
        $settings = SystemSetting::with('creator')
            ->orderBy('status', 'desc')   // active trước
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $activeSetting = SystemSetting::active();

        return view('settings.index', compact('settings', 'activeSetting'));
    }

    /**
     * Form tạo cấu hình mới
     */
    public function create()
    {
        return view('settings.create');
    }

    /**
     * Lưu cấu hình mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'clinic_name'    => 'required|string|max:200',
            'phone_number'   => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:255',
            'additional_info'=> 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
        ], [
            'clinic_name.required' => 'Tên phòng khám không được để trống.',
            'email.email'          => 'Địa chỉ email không hợp lệ.',
            'logo.image'           => 'Logo phải là file hình ảnh.',
            'logo.mimes'           => 'Logo chỉ chấp nhận định dạng: JPEG, PNG, WEBP, GIF.',
            'logo.max'             => 'Logo không được vượt quá 2MB.',
        ]);

        $applyNow = $request->boolean('apply_now');

        // Nếu áp dụng ngay → thu hồi tất cả cấu hình cũ
        if ($applyNow) {
            SystemSetting::where('status', 1)->update(['status' => 0]);
        }

        // Xử lý upload logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('clinic_logos', 'public');
        }

        SystemSetting::create([
            'created_by'     => auth()->id(),
            'clinic_name'    => $request->clinic_name,
            'phone_number'   => $request->phone_number,
            'email'          => $request->email,
            'address'        => $request->address,
            'additional_info'=> $request->additional_info,
            'logo_path'      => $logoPath,
            'status'         => $applyNow ? 1 : 0,
        ]);

        $msg = $applyNow
            ? 'Đã tạo và áp dụng cấu hình mới cho hệ thống!'
            : 'Đã lưu cấu hình. Nhấn "Áp dụng" để kích hoạt.';

        return redirect()->route('settings.index')->with('success', $msg);
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $setting = SystemSetting::findOrFail($id);
        return view('settings.edit', compact('setting'));
    }

    /**
     * Cập nhật cấu hình
     */
    public function update(Request $request, $id)
    {
        $setting = SystemSetting::findOrFail($id);

        $request->validate([
            'clinic_name'    => 'required|string|max:200',
            'phone_number'   => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string|max:255',
            'additional_info'=> 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:2048',
        ], [
            'clinic_name.required' => 'Tên phòng khám không được để trống.',
            'email.email'          => 'Địa chỉ email không hợp lệ.',
            'logo.image'           => 'Logo phải là file hình ảnh.',
            'logo.mimes'           => 'Logo chỉ chấp nhận định dạng: JPEG, PNG, WEBP, GIF.',
            'logo.max'             => 'Logo không được vượt quá 2MB.',
        ]);

        $data = [
            'created_by'     => auth()->id(),
            'clinic_name'    => $request->clinic_name,
            'phone_number'   => $request->phone_number,
            'email'          => $request->email,
            'address'        => $request->address,
            'additional_info'=> $request->additional_info,
        ];

        // Xử lý upload logo mới
        if ($request->hasFile('logo')) {
            // Xóa logo cũ khỏi storage
            if ($setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('clinic_logos', 'public');
        }

        $setting->update($data);

        return redirect()->route('settings.index')
            ->with('success', 'Đã cập nhật cấu hình thành công!');
    }

    /**
     * Áp dụng một cấu hình (kích hoạt, thu hồi cái cũ)
     */
    public function activate($id)
    {
        // Thu hồi tất cả
        SystemSetting::where('status', 1)->update(['status' => 0]);

        // Kích hoạt cái được chọn
        $setting = SystemSetting::findOrFail($id);
        $setting->update(['status' => 1]);

        return redirect()->route('settings.index')
            ->with('success', "Đã áp dụng cấu hình \"{$setting->clinic_name}\" cho hệ thống!");
    }

    /**
     * Thu hồi một cấu hình (không xóa, chỉ tắt)
     */
    public function deactivate($id)
    {
        $setting = SystemSetting::findOrFail($id);
        $setting->update(['status' => 0]);

        return redirect()->route('settings.index')
            ->with('success', 'Đã thu hồi cấu hình. Hệ thống sẽ dùng cấu hình mặc định cho đến khi có cấu hình mới được áp dụng.');
    }

    /**
     * Xóa logo của cấu hình (giữ nguyên các thông tin khác)
     */
    public function removeLogo($id)
    {
        $setting = SystemSetting::findOrFail($id);

        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
            $setting->update(['logo_path' => null]);
        }

        return redirect()->route('settings.edit', $id)
            ->with('success', 'Đã xóa logo phòng khám.');
    }

    /**
     * Xóa cấu hình
     */
    public function destroy($id)
    {
        $setting = SystemSetting::findOrFail($id);

        if ($setting->status == 1) {
            return back()->with('error', 'Không thể xóa cấu hình đang được áp dụng. Hãy thu hồi trước.');
        }

        // Xóa file logo kèm theo nếu có
        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->delete();

        return redirect()->route('settings.index')
            ->with('success', 'Đã xóa cấu hình.');
    }
}
