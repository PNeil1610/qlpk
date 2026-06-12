<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\KeycloakAdminService;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Cập nhật thông tin liên lạc (lưu vào DB local).
     * Email, số điện thoại, địa chỉ, CCCD vẫn được lưu tại DB QLPK.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email'       => ['nullable', 'email', 'max:150'],
            'phone_number'=> ['nullable', 'string', 'max:15'],
            'address'     => ['nullable', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        ], [
            'email.email'         => 'Địa chỉ email không hợp lệ.',
            'email.max'           => 'Email không được vượt quá 150 ký tự.',
            'phone_number.max'    => 'Số điện thoại không được vượt quá 15 ký tự.',
            'national_id.unique'  => 'Số CCCD/CMND đã tồn tại trên hệ thống.',
        ]);

        $user->email        = $request->email;
        $user->phone_number = $request->phone_number;
        $user->address      = $request->address;
        $user->national_id  = $request->national_id;
        $user->save();

        // Đồng bộ thông tin sang bảng Patient nếu user này là bệnh nhân
        if ($user->patient) {
            $user->patient->email        = $request->email;
            $user->patient->phone_number = $request->phone_number;
            $user->patient->address      = $request->address;
            $user->patient->national_id  = $request->national_id;
            $user->patient->save();
        }

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Đổi mật khẩu → Redirect sang Keycloak Account Console.
     * Keycloak quản lý toàn bộ vòng đời mật khẩu.
     */
    public function changePassword()
    {
        try {
            $keycloakService = app(KeycloakAdminService::class);
            $accountUrl = $keycloakService->getAccountConsoleUrl();
            // Mở trang Account Console Keycloak để user tự đổi mật khẩu
            return redirect($accountUrl . '/password');
        } catch (\Exception $e) {
            // Fallback nếu Keycloak chưa chạy
            return back()->with('error',
                'Không thể kết nối đến cổng quản lý tài khoản Keycloak. Vui lòng thử lại sau.'
            );
        }
    }
}